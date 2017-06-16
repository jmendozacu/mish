<?php
class VES_VendorsVacation_Block_Vacation extends Mage_Core_Block_Template {
    public function getCurrentVendor() {
        if(Mage::registry('current_vendor')) return Mage::registry('current_vendor');
        else {
            //not vendor page mode
            $current_product = Mage::registry('current_product');
            $vendor_id = $current_product->getVendorId();
            $vendor = Mage::getModel('vendors/vendor')->load($vendor_id);
            return $vendor;
        }
    }

    public function getVacationContent() {
        return $this->helper('vendorsvacation')->getVacationContent($this->getCurrentVendor()->getId());
    }

    protected function _toHtml() {
        if(!Mage::registry('current_vendor') && Mage::registry('product') && Mage::registry('product')->getData('vendor_relation_key') && !Mage::registry('product')->getData('vendor_child_product')) return '';
        
        if($this->helper('vendorsvacation')->checkInVacation($this->getCurrentVendor()->getId())) {
            return parent::_toHtml();
        } else {
            return '';
        }
    }
}