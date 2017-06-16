<?php
class VES_VendorsLivechat_Block_Head extends Mage_Core_Block_Template
{
    protected function _prepareLayout(){
        parent::_prepareLayout();
        if(!$this->isEnable()) return;

        $headBlock = $this->getLayout()->getBlock('head');

        if($headBlock){
                $headBlock->addItem('link_rel', Mage::helper("vendorslivechat")->getUrl("vendorslivechat/css/index"),'rel="stylesheet" type="text/css"');
        }
    }
    public function isEnable(){
        $modules = Mage::getConfig()->getNode('modules')->asArray();
        if(isset($modules['VES_VendorsGroup']) && isset($modules['VES_VendorsGroup']['active']) && $modules['VES_VendorsGroup']['active']=='true') {
            if($vendor = Mage::registry("vendor")){
                $groupId = $vendor->getGroupId();
                $subAccountEnableConfig = Mage::helper('vendorsgroup')->getConfig('livechat/enabled',$groupId);
                return $subAccountEnableConfig;
            }
			 else{
                if($product = Mage::registry("product")){
                    if($product->getVendorId()){
                        $vendor = Mage::getModel("vendors/vendor")->load($product->getVendorId());
                        $groupId = $vendor->getGroupId();
                        $subAccountEnableConfig = Mage::helper('vendorsgroup')->getConfig('livechat/enabled',$groupId);
                        return $subAccountEnableConfig;
                    }
                }
            }
        }
        return true;
    }

}