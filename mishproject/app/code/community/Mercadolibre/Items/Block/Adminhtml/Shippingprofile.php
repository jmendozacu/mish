<?php
class Mercadolibre_Items_Block_Adminhtml_Shippingprofile extends Mage_Adminhtml_Block_Widget_Grid_Container
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
	$this->_controller = 'adminhtml_shippingprofile';
    $this->_blockGroup = 'items';
    $this->_headerText = Mage::helper('items')->__('Shipping Profiles');
    $this->_addButtonLabel = Mage::helper('items')->__('Add Shipping Profile');
	$this->_addButton('shipping_profile_save', array(
        'label' => $this->__('Add Shipping Profile'),
        'onclick' => "setLocation('{$this->getUrl('*/*/new/store/'.$storeId)}')",
		'class' =>'add'
    ));
    parent::__construct();
	$this->removeButton('add');
	
  }
}