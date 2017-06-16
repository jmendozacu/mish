<?php
class VES_VendorsSubAccount_Block_Vendor_Links extends Mage_Core_Block_Template
{
	/**
	 * Remove configuration top link if sub account do not have permission.
	 * @see Mage_Core_Block_Abstract::_prepareLayout()
	 */
	protected function _prepareLayout(){
		if(!Mage::helper('vendorssubaccount')->moduleEnable()) return;
		$session = Mage::getSingleton('vendors/session');
		if($session->getIsSubAccount()){
			$topLinkBlock = $this->getLayout()->getBlock('toplinks');
			$account = $session->getSubAccount();
			$resources = $account->getRole()->getRoleResources();
			if(!in_array('configuration', $resources)){
				$topLinkBlock->removeLinkByUrl($this->getUrl('vendors/config'));
			}
		}
	}
}