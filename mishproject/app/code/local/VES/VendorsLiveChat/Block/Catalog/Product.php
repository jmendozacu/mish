<?php
class VES_VendorsLiveChat_Block_Catalog_Product extends VES_VendorsLiveChat_Block_Bottom_Box
{
    public function getVendor(){
        $product = Mage::registry('product');
		$vendor  = null;
		if($product->getVendorId())
		$vendor = Mage::getModel("vendors/vendor")->load($product->getVendorId());
		return $vendor;
    }
	
	public function checkProductIsVendor(){
		$product = Mage::registry('product');
        $vendor = Mage::registry("vendor_id");
		if($product->getVendorId() && !$vendor && $this->isEnable()) return true;
		return false;
	}


	protected function _prepareLayout(){
		if(!$this->checkProductIsVendor()){
            if($this->getLayout()->getBlock('product.form.chat')){
			    $this->getLayout()->getBlock('product.form.chat')->setTemplate('');
            }
		}
	}

     public function isEnable(){
		  $modules = Mage::getConfig()->getNode('modules')->asArray();
		
        if(isset($modules['VES_VendorsGroup']) && isset($modules['VES_VendorsGroup']['active']) && $modules['VES_VendorsGroup']['active']=='true') {
            if($vendor = $this->getVendor()){
                $groupId = $vendor->getGroupId();
                $subAccountEnableConfig = Mage::helper('vendorsgroup')->getConfig('livechat/enabled',$groupId);
                return $subAccountEnableConfig;
            }
        }
		
        return Mage::getStoreConfig("vendors/vendorslivechat/show_button_product_page");
    }
}