<?php
class Mercadolibre_Items_Block_Adminhtml_Itempublishing extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  private $root_id ='';
  private $revise = '';
 
  public function __construct()
  {
    $this->_controller = 'adminhtml_itempublishing';
    $this->_blockGroup = 'items';
    $this->_headerText = Mage::helper('items')->__('Item Publishing');
    $this->_addButtonLabel = Mage::helper('items')->__('Add Item');
	$this->_addButton('button_id', array(
            'label'     => Mage::helper('items')->__('Import CSV'),
            'onclick'   => "setLocation('".$this->getUrl('*/*/importCsv')."')",
            'class'     => 'go'
        ), 0, 100, 'header', 'header');
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
	if($this->getRequest()->getParam('revise')){
		$this->revise = $this->getRequest()->getParam('revise');
	}
	$this->setData('root_id',$this->root_id);
	$this->setData('revise',$this->revise);
	$this->setData('mageRootCollection',$optionsMageCatRoot);
	$this->setTemplate('items/mapping/contentItemPublishing.phtml');
	
	
  }
}