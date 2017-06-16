<?php
class Mercadolibre_Items_Block_Adminhtml_Attributemapping extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_attributemapping';
    $this->_blockGroup = 'items';
    $this->_headerText = Mage::helper('items')->__('Attribute Mapping');
	 $this->_addButton('button_id', array(
            'label'     => Mage::helper('items')->__('Import CSV'),
            'onclick'   => "setLocation('".$this->getUrl('*/*/importCsv')."')",
            'class'     => 'go'
        ), 0, 100, 'header', 'header');
    $this->_addButtonLabel = Mage::helper('items')->__('Attribute Mapping');
    parent::__construct();
	$this->_removeButton('add');
	
	//get the store name
	$store_id = '';
	$store_id = Mage::helper('items')->_getStore()->getId();;
	/* Get Root Meli Categories */
	$meliCategoriesRoot = Mage::getModel('items/melicategoriesfilter')
						->getCollection()
						->addFieldToFilter('root_id','0')
						->addFieldToFilter('store_id',$store_id);
	$meliCategoriesRoot -> addFieldToSelect(array('meli_category_id','meli_category_name'));
	$optionsMeliCatRoot = array(''=>'Please Select');
	foreach($meliCategoriesRoot as $row){
		$optionsMeliCatRoot[$row['meli_category_id']] =  $row['meli_category_name'];
	}
	$this->root_id = '';
	if($this->getRequest()->getParam('root_id')){
		$this->root_id = $this->getRequest()->getParam('root_id');
	}
	$this->setData('root_id',$this->root_id);
	$this->setData('meliCategoriesRoot',$optionsMeliCatRoot);
	
	/* Get Categories of Mali Root  */
	$CommonModel = Mage::getModel('items/common');
	if($this->root_id != ''){
    	$optionsMeliCat = $CommonModel->getMLCategoriesToAttributeMap($categories = array($this->root_id)); // 
	} else {
		$optionsMeliCat = array('PLEASE_SELECT'=>'Please Select');
	}
	if($this->getRequest()->getParam('category_id')){
		$this->category_id = $this->getRequest()->getParam('category_id');
	}
	$this->setData('category_id',$this->category_id);
	$this->setData('meliCategories',$optionsMeliCat);

	/* Get Magento Attributes */
	
	$optionsArrributes = array(''=>'Please Select');
	$mageAttrIds = array();
	$mageSizeAttrIds = array();
	if(Mage::getStoreConfig("mlitems/globalattributesmapping/magesizeattr",Mage::app()->getStore($store_id))){
		$mageSizeAttrIds = Mage::getStoreConfig("mlitems/globalattributesmapping/magesizeattr",Mage::app()->getStore($store_id));
		$mageSizeAttrIds = explode(",", $mageSizeAttrIds);
	}
	
	
	$mageColorAttrIds = '';
	if(Mage::getStoreConfig("mlitems/globalattributesmapping/magecolorattr",Mage::app()->getStore($store_id))){
		$mageColorAttrIds = Mage::getStoreConfig("mlitems/globalattributesmapping/magecolorattr",Mage::app()->getStore($store_id));
		$mageColorAttrIds = explode(",", $mageColorAttrIds);
	}
	
	if(count($mageSizeAttrIds) > 0){
		$mageAttrIds = $mageSizeAttrIds;
	}
	if(count($mageColorAttrIds) > 0){
		$mageAttrIds = $mageColorAttrIds;
	}
	if(count($mageSizeAttrIds) > 0 && count($mageColorAttrIds) > 0 ){
		$mageAttrIds = array();
		$mageAttrIds = array_merge($mageSizeAttrIds, $mageColorAttrIds);
	}
	if(count($mageAttrIds) > 0){
		$mageAttrIds = implode(',', $mageAttrIds);
	}
	

	$optionsArrributes = array(''=>'Please Select');
				
	$currStoreName = Mage::getModel('core/store')->load($store_id)->getName();
	$write = Mage::getSingleton('core/resource')->getConnection('core_write');
	
	
	if(trim($mageAttrIds) != ''){
		$attrValueByStore = $write->fetchAll("SELECT eal.value, eal.attribute_id, ea.attribute_code from  eav_attribute_label as eal LEFT JOIN eav_attribute as ea on eal.attribute_id = ea.attribute_id WHERE eal.store_id = '".$store_id."' and eal.attribute_id  IN (".$mageAttrIds.")");
		
		foreach($attrValueByStore as $data) {
			$optionsArrributes[$data['attribute_id']] =  $data['value'].' ( '.$data['attribute_code'].' )';
		}
	}
	
	if($this->getRequest()->getParam('attribute_id')){
		$this->attribute_id = $this->getRequest()->getParam('attribute_id');
	}
	
	$this->setData('storeId',$store_id);
	$this->setData('attribute_id',$this->attribute_id);
	$this->setData('mageAttributeCollection',$optionsArrributes);
	$this->setTemplate('items/attribute/mapping.phtml');
  }
}