<?php
class VES_VendorsLiveChat_Block_Catalog_Product_List extends VES_VendorsLiveChat_Block_Bottom_Box
{
    public function getVendor(){
        $product = $this->getProduct();
        if(!$product) return null;
		$vendor  = null;
		if($product->getVendorId())
		$vendor = Mage::getModel("vendors/vendor")->load($product->getVendorId());
		return $vendor;
    }
	

	protected function _prepareLayout(){
		if(!$this->isEnable() || Mage::registry("vendor_id")){
            if($this->getLayout()->getBlock('product.form.chat')){
			    $this->getLayout()->getBlock('product.form.chat')->setTemplate('');
            }
		}
	}

     public function isEnable(){
		$modules = Mage::getConfig()->getNode('modules')->asArray();
		
		$subAccountEnableConfig = true;
		
        if(isset($modules['VES_VendorsGroup']) && isset($modules['VES_VendorsGroup']['active']) && $modules['VES_VendorsGroup']['active']=='true') {
            if($vendor = $this->getVendor()){
                $groupId = $vendor->getGroupId();
                $subAccountEnableConfig = Mage::helper('vendorsgroup')->getConfig('livechat/enabled',$groupId);
            }
        }
		
        return $subAccountEnableConfig;
    }
}