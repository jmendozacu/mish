<?php
class Mercadolibre_Items_Block_Adminhtml_Itemdetailprofile extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    if($this->getRequest()->getParam('store')){
		$storeId = (int) $this->getRequest()->getParam('store');
	} else if(Mage::helper('items')-> getMlDefaultStoreId()){
		$storeId = Mage::helper('items')-> getMlDefaultStoreId();
	} else {
		$storeId = $this->getStoreId();
	}
	
	$this->_controller = 'adminhtml_itemdetailprofile';
    $this->_blockGroup = 'items';
    $this->_headerText = Mage::helper('items')->__('Item Description Template');
    //$this->_addButtonLabel = Mage::helper('items')->__('Create Description Template');
	$this->_addButton('description_template_save', array(
        'label' => $this->__('Create Description Template'),
        'onclick' => "setLocation('{$this->getUrl('*/*/new/store/'.$storeId)}')",
		'class' =>'add'
    ));
    parent::__construct();
	$this->removeButton('add');
	
  }
}
