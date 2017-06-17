<?php 
/**
 * Evirtual_Autoimport extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category   	Evirtual
 * @package		Evirtual_Autoimport
 * @copyright  	Copyright (c) 2013
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * store selection tab
 *
 * @category	Evirtual
 * @package		Evirtual_Autoimport
 * @author Ultimate Module Creator
 */
class Evirtual_Autoimport_Block_Adminhtml_Entry_Edit_Tab_Mapping extends Mage_Adminhtml_Block_Widget_Form
{ 
	
	protected $_storeModel;
    protected $_attributes;
    protected $_addMapButtonHtml;
    protected $_removeMapButtonHtml;
    protected $_shortDateFormat;
	protected $_entrytype;
	protected $_entryUrl;
	protected $_catalogtype;
	protected $_externalFields = array();
	/**
     * Set the template for the block
     *
     */
    public function _construct()
    {
        parent::_construct();
		//Zend_Debug::dump($_COOKIE);
       	$this->_entryUrl=Mage::getModel('core/cookie')->get('entry_url');
		$this->_entrytype=Mage::getModel('core/cookie')->get('entry_type');
		$this->_catalogtype=Mage::getModel('core/cookie')->get('entry_catalogtype');  
        $this->setTemplate('autoimport/entry/mapping.phtml');
    }
	
	public function getAttributes($entityType)
    {
        if (!isset($this->_attributes[$entityType])) {
            switch ($entityType) {
                case 'product':
                    $attributes = Mage::getSingleton('catalog/convert_parser_product')
                        ->getExternalAttributes();
					$attributes['subcategory_ids']='subcategory_ids';	
                    break;

                case 'category':
                    $attributes = $this->getExternalAttributes();
                    break;
					
				case 'stockupdate':
                    $attributes = $attributes = Mage::getSingleton('catalog/convert_parser_product')
                        ->getExternalAttributes();
                    break;	
            }

            array_splice($attributes, 0, 0, array(''=>$this->__('Choose an attribute')));
            $this->_attributes[$entityType] = $attributes;
        }
        return $this->_attributes[$entityType];
    }
	
	public function getExternalAttributes()
    {
        $productAttributes  = Mage::getResourceModel('catalog/category_attribute_collection')->load();
        $attributes         = $this->_externalFields;

        foreach ($productAttributes as $attr) {
            $code = $attr->getAttributeCode();
            if (in_array($code, $this->_internalFields) || $attr->getFrontendInput() == 'hidden') {
                continue;
            }
            $attributes[$code] = $code;
        }
		
		$attributes['id']='id';
		
		
        return $attributes;
    }

    public function getValue($key, $default='', $defaultNew = null)
    {
        if (null !== $defaultNew) {
            if (0 == $this->getProfileId()) {
                $default = $defaultNew;
            }
        }

        $value = $this->getData($key);
        return $this->htmlEscape(strlen($value) > 0 ? $value : $default);
    }
	public function getAddMapButtonHtml()
    {
        if (!$this->_addMapButtonHtml) {
            $this->_addMapButtonHtml = $this->getLayout()->createBlock('adminhtml/widget_button')->setType('button')
                ->setClass('add')->setLabel($this->__('Add Field Mapping'))
                ->setOnClick("addFieldMapping()")->toHtml();
        }
        return $this->_addMapButtonHtml;
    }

    public function getRemoveMapButtonHtml()
    {
        if (!$this->_removeMapButtonHtml) {
            $this->_removeMapButtonHtml = $this->getLayout()->createBlock('adminhtml/widget_button')->setType('button')
                ->setClass('delete')->setLabel($this->__('Remove'))
                ->setOnClick("removeFieldMapping(this)")->toHtml();
        }
        return $this->_removeMapButtonHtml;
    }
	
	public function getFileData(){
		
		
		if($this->_entrytype=="xml"){
			
			return $this->getXmlFileds();
			
		}else if($this->_entrytype=="csv"){
			
			return $this->getCsvFileds();
			
		}else if($this->_entrytype=="xlsx"){
			
			return $this->getXlsxFileds();
		}
		
	}
	
	public function getXlsxFileds(){
		
		$xlsxArray=Mage::helper('autoimport')->ArrayData($this->_entryUrl);
			
		return $xlsxArray[0];
	}
	
	public function getCsvFileds(){
		
		$arrayReturn=array();
		
			$row = 1;
			if (($handle = fopen($this->_entryUrl, "r")) !== FALSE) {
				while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
					$num = count($data);
					
					$row++;
					for ($c=0; $c < $num; $c++) {
						array_push($arrayReturn,$data[$c]);
					}
					
					break;
				}
				fclose($handle);
			}
			
		return $arrayReturn;		
			
	}
	public function getXmlFileds(){
		
		$arrayReturn=array();
		
		$flashRAW = simplexml_load_file($this->_entryUrl);
			
				foreach($flashRAW->children() as $parts){
											
						if($i<1){
								
								if(is_object($parts)){
																	
									if(count($parts)>0){
										
										foreach($parts as $key => $value){	
												
												array_push($arrayReturn,$key);
										}
									
									}else{
										
										foreach($sections as $key => $value){
												
												array_push($arrayReturn,$key);
													
										}
									}
								}
								//Zend_Debug::dump($arrayReturn);
							$i++;
						}
					
								
					}
			
			return $arrayReturn;		
			
	}
  
}