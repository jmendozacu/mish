<?php
class VES_VendorsContact_Block_Contact_Sidebar extends Mage_Core_Block_Template {
    protected $_map_collection;

    public function getVendorInfo() {
        return Mage::getModel('vendors/vendor')->loadByVendorId(Mage::registry('vendor_id'));
    }

    public function isEnable(){
        $is_enabel = Mage::helper("vendorscontact")->isEnabled();
        $email = Mage::helper("vendorscontact")->getEmailVendor();
        if($is_enabel && $email) return true;
        return false;
    }
    /**
     * Get current vendor
     * @return VES_Vendors_Model_Vendor
     */
    public function getVendor(){
        return Mage::registry('vendor');
    }

    /**
     * Get current vendor id
     * @return int
     */
    public function getVendorId() {
        return $this->getVendor()->getId();
    }
}