<?php
class VES_VendorsContact_Block_Contact extends Mage_Core_Block_Template {
        public function getFormAction(){
            $vendor =  Mage::registry("current_vendor");
            $url = Mage::helper("vendorspage")->getUrl($vendor,$this->getUrlKey()."contact/post");
            return $url;
            //return Mage::getUrl('vendorscontact/contact/post', array('vendor' => Mage::registry('vendor_id')));
        }

        public function _prepareLayout()
        {
            parent::_prepareLayout();
            if ($breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs')) {
                $vendorId = Mage::registry('vendor_id');
                $breadcrumbsBlock->addCrumb('vendor', array(
                    'label'=>Mage::registry('vendor')->getTitle(),
                    'title'=>Mage::registry('vendor')->getTitle(),
                    'link'=>Mage::helper('vendorspage')->getUrl($vendorId),
                ));

                $breadcrumbsBlock->addCrumb('vendor_contact', array(
                    'label'=>$this->__('Contact'),
                    'title'=>$this->__('Contact'),
                ));

                if ($headBlock = $this->getLayout()->getBlock('head')) {
                    $headBlock->setTitle('Contact - ' .Mage::registry('vendor')->getTitle());
                }
            }
        }

       public function _toHtml(){
           $is_enabel = Mage::helper("vendorscontact")->isEnabled();
           $email = Mage::helper("vendorscontact")->getEmailVendor();
           if(!$is_enabel || !$email) return '';
           return parent::_toHtml();
       }
}