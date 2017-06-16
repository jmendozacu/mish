<?php
class VES_VendorsRelatedCustomerAccount_Block_Links extends Mage_Core_Block_Template
{
    /**
     * Add Vendor login link
     *
     * @return Mage_Checkout_Block_Links
     */
    public function addVendorLink()
    {
    	if(!Mage::helper('vendors')->moduleEnabled()){
    		return $this;
    	}
    	$parentBlock = $this->getParentBlock();
    	/*Remove store manage link*/
	    $parentBlock->removeLinkByUrl(Mage::helper('vendors')->getStoreManageUrl());
		if(Mage::getSingleton('customer/session')->isLoggedIn()){
	        if ($parentBlock && Mage::helper('core')->isModuleOutputEnabled('VES_Vendors')) {
	            $parentBlock->addLink(Mage::helper('vendors')->__('Vendor'),Mage::helper('relatedcustomer')->getVendorUrl(),Mage::helper('vendors')->__('Vendor'),null,null,10);
	        }
		}
        return $this;
    }

}
