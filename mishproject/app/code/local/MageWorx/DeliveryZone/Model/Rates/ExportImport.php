<?php
/**
 * MageWorx
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MageWorx EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.mageworx.com/LICENSE-1.0.html
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@mageworx.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.mageworx.com/ for more information
 * or send an email to sales@mageworx.com
 *
 * @category   MageWorx
 * @package    MageWorx_DeliveryZone
 * @copyright  Copyright (c) 2013 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * MageWorx DeliveryZone extension
 *
 * @category   MageWorx
 * @package    MageWorx_DeliveryZone
 * @author     MageWorx Dev Team <dev@mageworx.com>
 */

class MageWorx_DeliveryZone_Model_Rates_ExportImport extends Mage_Core_Model_Abstract
{
   /**
    * Input field ID on form
    */
   const   FIELD_NAME_SOURCE_FILE = 'import_file';
   
   public  $errors = array();
   /**
    * All standard possible operators for rule conditions
    * @var array 
    */
   public $operators = array("==","!=",">=","<=",">","<","{}","!{}","()","!()");
   
   /**
    * System data fields with descriptions (Name of columns in CSV)
    * @var array
    */
   public  $csvList = array(
            'rate_id'           =>"Rate ID",
            'name'              =>"Rate Name",
            'descripton'        =>"Description",
            'is_active'         =>"Is Active",
            'simple_action'     =>"Action",
            'shipping_cost'     =>"Shipping Cost",
            'surcharge_fixed'   =>"Surcharge Fixed",
            'surcharge_percent' =>"Surcharge Percent",
            'fixed_per_product' =>"Fixed Per Product",
            'percent_per_product'=>"Percent Per Product",
            'percent_per_item'  =>"Percent Per Item",
            'fixed_per_item'    =>"Fixed Per Item",
            'percent_per_order' =>"Percent Per Order",
            'fixed_per_weight'  =>"Fixed Per Weight",
            'sort_order'        =>"Sort Order",
            'aggregator'        =>"ALL_ANY",
            'value'             =>"TRUE_FALSE",
            'store_ids'       =>"Stores",
            'customer_group_ids'=>"Customer Groups",
            'carrier_methods'   =>"Carrier Methods",
            );
   
   private $_stores;
   private $_customergroups;
   private $_methods;
   private $_attributes = array();
   private $_productAttributes;
   private $_addressAttributes;
   
   /**
     * Import/Export working directory (source files, result files, lock files etc.).
     *
     * @return string
     */
    
    public static function getWorkingDir()
    {
        $path = Mage::getBaseDir('var') . DS . 'exportimport' . DS;
        if(!is_dir($path)) {
            mkdir($path);
        }
        return $path;
    }
    
    /**
     * Public wrap for export CSV
     * @return array
     */
    public function export() {
        return $this->_generateContentCsv(TRUE);
    }
    
    /**
     * Public empty export CSV
     * @return array
     */
    public function generateEmptyFile() {
        return $this->_generateContentCsv(FALSE,'Shipping Suite Rules Sample File');
    }

    /**
     * Public wrap for import CSV
     */
    public function import() {
        
        $preparedData = array();
        if(!$this->_productAttributes) {
            $productCondition = Mage::getModel('deliveryzone/rates_condition_product');
            $this->_productAttributes = $productCondition->loadAttributeOptions()->getAttributeOption();
        }
        if(!$this->_addressAttributes) {
            $addressCondition = Mage::getModel('salesrule/rule_condition_address');
            $addressAttributes = $addressCondition->loadAttributeOptions()->getAttributeOption();
            $addressAttributes['firstname'] = Mage::helper('deliveryzone')->__('First Name');
            $addressAttributes['lastname'] = Mage::helper('deliveryzone')->__('Last Name');
            $this->_addressAttributes = $addressAttributes;
        }
        $fileName   = $this->uploadFile();
        $content    = $this->readFile($fileName);
        $importList = array();
        $indexList  = array_shift($content);
        foreach($content as $k=>$line) {
            $model = Mage::getModel('deliveryzone/rates');
            $importList[$k] = array();
            foreach ($line as $key=>$value) {
                $attributeCode = str_replace(' ',"_",strtolower($indexList[$key]));
                list($attributeCode,$value) = $this->_prepareAttributeToImport($attributeCode,$value);
               
                if(is_array($value) && isset($value[0]) && is_array($value[0]) && isset($value[0]['type'])) {
                    if(!isset($importList[$k]['conditions_serialized'])) {
                        $importList[$k]['conditions_serialized'] = array();
                        $importList[$k]['conditions_serialized']["type"]= "deliveryzone/rates_condition_combine";
                        $importList[$k]['conditions_serialized']["attribute"] = "";
                        $importList[$k]['conditions_serialized']["operator"] = ""; 
                        $importList[$k]['conditions_serialized']["is_value_processed"] = "";
                        $importList[$k]['conditions_serialized']['conditions'] = array();
                    }
                    foreach ($value as $cond) {
                        $importList[$k]['conditions_serialized']['conditions'][] = $cond;
                    }
                } else {
                   $importList[$k][$attributeCode] = $value;
                }
            }
            $importList[$k]['conditions_serialized']["value"] = $importList[$k]["value"];
            $importList[$k]['conditions_serialized']["aggregator"] = $importList[$k]["aggregator"];
            
            $importList[$k]['conditions_serialized'] = serialize($importList[$k]['conditions_serialized']);
            $importList[$k]['actions_serialized'] = 'a:4:{s:4:"type";s:36:"deliveryzone/rates_action_collection";s:9:"attribute";N;s:8:"operator";s:1:"=";s:5:"value";N;}';
         
            $model->setData($importList[$k]);
            if($importList[$k]["rate_id"]) {
                $loadModel = Mage::getModel('deliveryzone/rates')->load($importList[$k]["rate_id"]);
                if($loadModel->getId()) {
                    $model->setRateId($importList[$k]["rate_id"]);
                } else {
                    $model->setRateId(null);
                }
            } else {
                $model->setRateId(null);
            }
            $model->save();
        }
    }
    
    /**
     * Convert labes and codes for import
     * @param string $value
     * @param array $values
     * @return array
     */
    private function _attributeValuesToArray($value,$values) {
        $list = array();
        $_values = explode(",",$value);
        foreach ($_values as $_value) {
            foreach ($values as $k=>$v){
                if(strtoupper($v)==  strtoupper($_value)){
                    $list[] = $k;
                }
            }
        }
        return $list;
    }
    
    /**
     * Prepare attribute label for import. Define condition operator. Default operator is '=='
     * @param string $value
     * @return array
     */
    private function _prepareAttributeDataToCondition($value) {
        $operator = "==";
        $matches = array();
        $pattern = "/\[([^\[]*?)\]/";
        preg_match($pattern, $value, $matches);
        if(isset($matches[1]) && in_array($matches[1], $this->operators)) {
            $operator = $matches[1];
            $value = str_replace($matches[0], "", $value);
        }
        return array($operator,$value);
    }

    /**
     * Prepare attribute to import
     * @param string $code
     * @param string $value
     * @return array
     */
    private function _prepareAttributeToImport($code,$value='') {
        if(!$value) return array($code,$value);
        $this->_prepareCustomerGroups();
        $this->_prepareStores();
        $this->_prepareMethods();
        $groups = $this->_customergroups;
        $stores = $this->_stores;
        $methods  = $this->_methods;
        
        switch ($code) {
            case "customer_groups":
                $code = "customer_group_ids";
                $value = $this->_attributeValuesToArray($value, $groups);
                return array($code,$value);
            case "stores":
                $code = "store_ids";
                $value = $this->_attributeValuesToArray($value, $stores);
                return array($code,$value);
            case "action":
                $code = "simple_action";
                return array($code,$value);
            case "rate_name":
                $code = "name";
                return array($code,$value);
            case "all_any":
                $code = "aggregator";
                return array($code,$value);
            case "true_false":
                $code = "value";
                return array($code,$value);
            case "carrier_methods":
                $value = $this->_attributeValuesToArray($value, $methods);
                return array($code,$value);
            default :
                if(isset($this->csvList[$code])) {
                    return array($code,$value);
                } else {
                    $list = array();
                    foreach (explode(",",$value) as $_value) {
                        list($operator,$_value) = $this->_prepareAttributeDataToCondition($_value);
                        
                        list($type,$code) = $this->_checkAttributeType($code);
                        if($code == "payment_method") {
                            if(!isset($this->_attributes[$code])) {
                                $allAvailablePaymentMethods = Mage::getSingleton('adminhtml/system_config_source_payment_allmethods')->toOptionArray();
                                foreach ($allAvailablePaymentMethods as $_option) {
                                    if(is_array($_option['value'])) {
                                        foreach ($_option['value'] as $_item) {
                                            $this->_attributes[$code][$_item['value']] = $_item['label'];
                                        }
                                    } else {
                                        $this->_attributes[$code][$_option['value']] = $_option['label'];
                                    }
                                }
                            }
                            foreach ($this->_attributes[$code] as $k=>$v) {
                                if($v==$_value) {
                                    $_value = $k;
                                    break;
                                }
                            }
                        }
                        if($code == "category_ids") {
                            if(!isset($this->_attributes[$code])) {
                                $this->_attributes[$code] = $value;
                            }
                            foreach ($this->_attributes[$code] as $k=>$v) {
                                if($v==$_value) {
                                    $_value = $k;
                                    break;
                                }
                            }
                        }
                        
                        
                        if(!isset($this->_attributes[$code]) || !isset($this->_attributes[$code][$_value])) {
                            $attribute = Mage::getModel('catalog/resource_eav_attribute')->load($code,"attribute_code");

                            if($attribute->getFrontendInput() == 'text') {
                                $this->_attributes[$code][$_value] = $_value;
                            }
                            $attributeOptions = $attribute->getSource()->getAllOptions();
                            foreach ($attributeOptions as $_option) {
                                if($_option['label']) {
                                    $this->_attributes[$code][$_option['value']] = $_option['label'];
                                }
                            }
                        }
                       // echo "<pre>"; print_r($this->_attributes);
                        foreach ($this->_attributes[$code] as $k=>$v) {
                            if($v==$_value) {
                                $_value = $k;
                                break;
                            }
                        }
                        $list[] = array(
                            "type" => $type,
                            "attribute" => $code,
                            "operator" => $operator,
                            "value" => $_value,
                            "is_value_processed" => ""
                        );
                    }
                    return array($code,$list);
                }
        }
        return array($code,$value);
        
    }

    /**
     * Check and Define attribute type.
     * @param string $code
     * @return array
     */
    private function _checkAttributeType($code) {
        $addressType = "salesrule/rule_condition_address";
        $productType = "deliveryzone/rates_condition_product";
        foreach ($this->_addressAttributes as $k=>$v) {
            $v = str_replace(' ',"_",  strtolower($v));
            if($code==$v) {
                return array($addressType,$k);
            }
        }
        foreach ($this->_productAttributes as $k=>$v) {
            $v = str_replace(' ',"_",  strtolower($v));
            if($code==$v) {
                return array($productType,$k);
            }
        }
        return array($productType,$code);
    }

    /**
     * Generate content for export CSV
     * @param array $existData
     * @return array
     */
    private function _generateContentCsv($existData = FALSE,$file_name = 'Export Shipping Rates') {
        $collection = Mage::getModel('deliveryzone/rates')->getCollection();
        $file_path = self::getWorkingDir()  .$file_name." ".date("Y-m-d H-i-s").".csv"; //file path of the CSV file in which the data to be saved
       
        $productCondition = Mage::getModel('deliveryzone/rates_condition_product');
        $productAttributes = $productCondition->loadAttributeOptions()->getAttributeOption();
        
        $addressCondition = Mage::getModel('salesrule/rule_condition_address');
        $addressAttributes = $addressCondition->loadAttributeOptions()->getAttributeOption();
       
        $rows = array();   
        $data = $this->csvList; //array_keys($this->csvList);
        $header = $this->csvList;
        
        foreach ($collection as $rate) {
            $dataRate = array();
            $dataRate = $data;
            foreach ($data as $k=>$v) {
                if($rate->hasData($k)) {
                    $dataRate[$k] = $rate->getData($k);
                } else {
                    $dataRate[$k] = "";
                }
            }
            $conditions = unserialize($rate->getConditionsSerialized());
            foreach ($conditions['conditions'] as $condition) {
                if(!isset($data[$condition['attribute']])) {
                    if(isset($productAttributes[$condition['attribute']])){
                        $attributeTitle = $productAttributes[$condition['attribute']];
                    }
                    elseif(isset($addressAttributes[$condition['attribute']])) {
                        $attributeTitle = $addressAttributes[$condition['attribute']];
                    }
                    elseif(isset($specAttributes[$condition['attribute']])) {
                        $attributeTitle = $specAttributes[$condition['attribute']];
                    } else {
                        $this->errors[] = Mage::helper('deliveryzone')->__("Attribute %s can't export",$condition['attribute']);
                        continue;
                    }
                    $data[$condition['attribute']]='';
                    $dataRate[$condition['attribute']]="[".$condition['operator']."]".$this->_prepareAttribute($condition['attribute'],$condition['value']);
                    
                    $header[$condition['attribute']] = $attributeTitle;
                }
                else {
                    $attrValue = trim($this->_prepareAttribute($condition['attribute'],$condition['value']));
                    if($attrValue) {
                        $dataRate[$condition['attribute']] = $dataRate[$condition['attribute']] ? $dataRate[$condition['attribute']] . ",[".$condition['operator']."]".$attrValue : "[".$condition['operator']."]".$attrValue;
                    }
                }
            }
            $stores = $rate->_getResource()->getStoreIds($rate->getId());
            $dataRate['store_ids'] = join(',',  $this->_prepareStores($stores));
            $customerGroups = $rate->_getResource()->getCustomerGroupIds($rate->getId());
            $dataRate['customer_group_ids'] = join(',',$this->_prepareCustomerGroups($customerGroups));
            $methods = $rate->_getResource()->getCarrierMethods($rate->getId());
            $dataRate['carrier_methods'] = join(',',$this->_prepareMethods($methods));
            $dataRate['aggregator'] = $conditions['aggregator'];
            $dataRate['value'] = $conditions['value'];
       
            $rows[] = $dataRate;
        }
        
        array_unshift($rows, $header);
        if(!$existData) {
            $rows = array($header);
        }
       // echo "<pre>"; print_r($rows); exit;
        $this->_generateCsv($file_path,$rows);
        $name = pathinfo($file_path, PATHINFO_BASENAME);
        return array($name,$file_path);
    }
    
    /**
     * Define stores to object. If Ids exists, return data
     * @param array $ids
     * @return array
     */
    private function _prepareStores($ids=array()) {
        if(!$this->_stores) {
            $this->_stores = array();
            foreach (Mage::app()->getStores() as $store) {
                $this->_stores[$store->getStoreId()] = $store->getName();
            }
        }
        $data = array();
        foreach ($ids as $_id) {
            $data[$_id] = isset($this->_stores[$_id])?$this->_stores[$_id]:"NaN";
        }
        return $data;
    }
    
    /**
     * Define customer groups to object. If Ids exists, return data
     * @param array $ids
     * @return array
     */
    private function _prepareCustomerGroups($ids=array()) {
        if(!$this->_customergroups) {
            $this->_customergroups = array();
            foreach (Mage::getResourceModel('customer/group_collection')->toOptionArray() as $group) {
                $this->_customergroups[$group['value']] = $group['label'];
            }
        }
        $data = array();
        foreach ($ids as $_id) {
            $data[$_id] = isset($this->_customergroups[$_id])?$this->_customergroups[$_id]:"NaN";
        }
        return $data;
    }
    
    /**
     * Define carrier methods to object. If Ids exists, return data
     * @param array $ids
     * @return array
     */
    private function _prepareMethods($ids=array()) {
         if(!$this->_methods) {
            $this->_methods = array();
            $methods = Mage::getSingleton('deliveryzone/system_adminhtml_carrier')->toOptionArray();
            foreach ($methods as $k=>$carrier) {
                foreach ($carrier['value'] as $method) {
                    $this->_methods[$method['value']] = $carrier['label'].":".trim($method['label']);
                }
            }
        }
        $data = array();
        foreach ($ids as $_id) {
            $data[$_id] = isset($this->_methods[$_id])?$this->_methods[$_id]:"NaN";
        }
        return $data;
    }
    
    /**
     * Return prepared info about attribute. If attribute is not defined, load it and all options.
     * @param string $code
     * @param string $value
     * @return string
     */
    private function _prepareAttribute($code,$value) {
     
        if(isset($this->_attributes[$code]) && isset($this->_attributes[$code][$value])) {
            return $this->_attributes[$code][$value];
        }
        if(!isset($this->_attributes[$code])) {
            $this->_attributes[$code] = array();
        }
        $attribute = Mage::getModel('catalog/resource_eav_attribute')->load($code,"attribute_code");
            
        if(!$attribute->getAttributeCode()) {
            return $this->_getNonAttributeValue($code,$value);
        }
        if($attribute->getFrontendInput() == 'text') {
            $this->_attributes[$code][$value] = $value;
            return $value;
        }
        $attributeOptions = $attribute ->getSource()->getAllOptions();
        foreach ($attributeOptions as $_option) {
            if($_option['label']) {
                $this->_attributes[$code][$_option['value']] = $_option['label'];
            }
        }
        if(isset($this->_attributes[$code][$value])) {
            return $this->_attributes[$code][$value];
        }
        
        return "*";
    }

    /**
     * If no system attribute, do something custom 
     * @param string $code
     * @param string $value
     * @return string
     */
    private function _getNonAttributeValue($code,$value) {
        switch ($code) {
            case 'payment_method':
                $allAvailablePaymentMethods = Mage::getSingleton('adminhtml/system_config_source_payment_allmethods')->toOptionArray();
                foreach ($allAvailablePaymentMethods as $_option) {
                    if(is_array($_option['value'])) {
                        foreach ($_option['value'] as $_value) {
                            $this->_attributes[$code][$_value['value']] = $_value['label'];
                        }
                    } else {
                        $this->_attributes[$code][$_option['value']] = $_option['label'];
                    }
                }
                break;
        }
        if(isset($this->_attributes[$code][$value])) {
            return $this->_attributes[$code][$value];
        }
        return "*";
    }

    /**
     * Generate CSV file
     * @param string $file_path
     * @param array $data
     */
    private function _generateCsv($file_path,$data=array()) {
       // 
        $mage_csv = new Varien_File_Csv(); //mage CSV  
           //write to csv file
        $mage_csv->saveData($file_path, $data); //note $products_row will be two dimensional array
    }
    
    /**
     * Upload Import file
     * 
     * @return string
     */
    public function uploadFile()
    {
        $uploader  = Mage::getModel('core/file_uploader', self::FIELD_NAME_SOURCE_FILE);
        $uploader->skipDbProcessing(true);
        $result    = $uploader->save(self::getWorkingDir());
        $uploadedFile = $result['path'] . $result['file'];
        if(!$uploadedFile) {
            $this->errors[] = $this->__("File can't uploaded");
        }
        return $uploadedFile;
    }
    
    /**
     * Read file content
     * 
     * @param string $filename
     * @return array
     */
    public function readFile($filename = '') 
    {
        if(!$filename) return;
        $content = array();
        ini_set("auto_detect_line_endings", true);
        $fp = fopen($filename, "r");
        while (!feof($fp))
        {
            $line = fgetcsv($fp, 10000);
            $content[] = $line;
        
        }
        //array_shift($content);
        $content = array_filter($content);
        ini_set("auto_detect_line_endings", false);
        if(!sizeof($content)) {
            $this->errors[] = $this->__("File is empty");
        }
        return $content;
    }
}
