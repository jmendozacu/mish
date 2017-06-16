<?php
class Mercadolibre_Items_Block_Adminhtml_Itemlisting extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_itemlisting';
    $this->_blockGroup = 'items';
    $this->_headerText = Mage::helper('items')->__('Create Listing');
    $this->_addButtonLabel = Mage::helper('items')->__('Add Item');
    parent::__construct();
	$this->_removeButton('add');
	
	/* Get Root Mage Categories */
	$commonModel = Mage::getModel('items/common');
	$rootcatId= Mage::app()->getStore()->getRootCategoryId(); 
	$categories = Mage::getModel('catalog/category')
				->getCollection()
				//->addFieldToFilter('parent_id', array('eq'=>$rootcatId))
				->addFieldToFilter('level', array('eq'=>'2'))
				->addFieldToFilter('is_active', array('eq'=>'1'))
				->addAttributeToSort('position', 'ASC')
				->addAttributeToSelect('id')
				->addAttributeToSelect('name');
	$MageCateData =  $commonModel->getMageRootCategories($categories); 
	$arrayMageCat = explode('=>',$MageCateData);
	foreach($arrayMageCat as $key => $value){
			$valArr = explode('##MLCATID##',$value);
			if(isset($valArr['1']) && trim($valArr['1'])!=''){
				$mageRootCollection[$key] = array($valArr['0'],$valArr['1']);
				$mageCateIdCollection[] = $valArr['0'];
			}
	}	
	/*-------------------------------------------------------------*/
	$optionsMageCatRoot = array(''=>'Please Select');
	foreach($mageRootCollection as $row){
		$optionsMageCatRoot[$row['0']] =  $row['1'];
	}
	if($this->getRequest()->getParam('root_id')){
		$this->root_id = $this->getRequest()->getParam('root_id');
	}
	$this->setData('root_id',$this->root_id);
	$this->setData('mageRootCollection',$optionsMageCatRoot);
	$this->setTemplate('items/mapping/contentItemListing.phtml');
	
	
  }
}