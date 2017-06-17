<?php
class VES_VendorsRma_Block_Customer_Request extends Mage_Core_Block_Template
{
	public function _prepareLayout(){
	    $this->getLayout()->getBlock('head')
	    ->setTitle(Mage::helper('vendorsrma')->__('RMA'));
		$this->getLayout()->getBlock('customer_account_navigation')->addLink('request','vesrma/rma_customer/list','RMA');
		return parent::_prepareLayout();
	}
}