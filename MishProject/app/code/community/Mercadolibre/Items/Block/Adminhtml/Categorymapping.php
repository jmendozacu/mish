<?php
class Mercadolibre_Items_Block_Adminhtml_Categorymapping extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  private $root_id = '';	
  public function __construct()
  {
    $this->_controller = 'adminhtml_categorymapping';  // controller name
    $this->_blockGroup = 'items';   // module name
    $this->_headerText = Mage::helper('items')->__('Category Mapping');  
	$this->_addButton('button_id', array(
            'label'     => Mage::helper('items')->__('Import CSV'),
            'onclick'   => "setLocation('".$this->getUrl('*/*/importCsv')."')",
            'class'     => 'go'
        ), 0, 100, 'header', 'header');
    parent::__construct();
	$this->_removeButton('add');
	
	$this->storeId = Mage::helper('items')-> _getStore()->getId();
	/* Get Root Meli Categories */
	$meliCategoriesRoot = Mage::getModel('items/melicategoriesfilter')
						-> getCollection()
						->addFieldToFilter('root_id','0')
						->addFieldToFilter('store_id',$this->storeId);
	$meliCategoriesRoot -> addFieldToSelect(array('meli_category_id','meli_category_name'));
	$optionsMeliCatRoot = array(''=>'Please Select');
	foreach($meliCategoriesRoot as $row){
		$optionsMeliCatRoot[$row['meli_category_id']] =  $row['meli_category_name'];
	}
	if($this->getRequest()->getParam('root_id')){
		$this->root_id = $this->getRequest()->getParam('root_id');
	}	
	$this->setData('storeId',$this->storeId);
	$this->setData('root_id',$this->root_id);
	$this->setData('meliCategoriesRoot',$optionsMeliCatRoot);
	$this->setTemplate('items/mapping/content.phtml');
  }
}