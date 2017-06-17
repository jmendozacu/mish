<?php
class VES_VendorsLiveChat_Block_Catalog_Product_View extends VES_VendorsLiveChat_Block_Profile_Sidebar {

	public function getVendor(){
		$product = Mage::registry('product');
		$vendor  = null;
		if($product->getVendorId())
		$vendor = Mage::getModel("vendors/vendor")->load($product->getVendorId());
		return $vendor;
	}
	public function checkProductIsVendor(){
		$product = Mage::registry('product');
		if($product->getVendorId() && $this->isEnable()) return true;
		return false;
	}


    public function isEnable(){
        $modules = Mage::getConfig()->getNode('modules')->asArray();
        if(isset($modules['VES_VendorsGroup']) && isset($modules['VES_VendorsGroup']['active']) && $modules['VES_VendorsGroup']['active']=='true') {
            if(Mage::registry("product")->getVendorId()){
                $vendor = Mage::getModel("vendors/vendor")->load(Mage::registry("product")->getVendorId());
                $groupId = $vendor->getGroupId();
                $subAccountEnableConfig = Mage::helper('vendorsgroup')->getConfig('livechat/enabled',$groupId);
                return $subAccountEnableConfig;
            }
        }
        return true;
    }

}