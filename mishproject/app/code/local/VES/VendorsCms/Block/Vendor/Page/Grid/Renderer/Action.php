<?php

class VES_VendorsCms_Block_Vendor_Page_Grid_Renderer_Action extends Mage_Adminhtml_Block_Cms_Page_Grid_Renderer_Action{
    public function render(Varien_Object $row)
    {
        $vendor = Mage::getSingleton('vendors/session')->getVendor();
        $href = Mage::helper('vendorspage')->getUrl($vendor,$row->getIdentifier());
        return '<a href="'.$href.'" target="_blank">'.$this->__('Preview').'</a>';
    }
}
