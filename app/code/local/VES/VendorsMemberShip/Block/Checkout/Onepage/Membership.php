<?php

class VES_VendorsMemberShip_Block_Checkout_Onepage_Membership extends Mage_Core_Block_Template{
	protected function _prepareLayout(){
		$layout 		= $this->getLayout();
		$onepageBlock 	= $layout->getBlock('checkout.onepage');
		if($onepageBlock) $onepageBlock->setTemplate('ves_membership/checkout/onepage.phtml');
		
		$loginBlock 	= $layout->getBlock('checkout.onepage.login');
		if($loginBlock) $loginBlock->setTemplate('ves_membership/checkout/onepage/login.phtml');
		
		$billingBlock 	= $layout->getBlock('checkout.onepage.billing');
		if($billingBlock) $billingBlock->setTemplate('ves_membership/checkout/onepage/billing.phtml');
		
		
	}
} 