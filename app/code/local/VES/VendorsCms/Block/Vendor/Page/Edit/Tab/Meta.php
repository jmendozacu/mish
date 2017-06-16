<?php

class VES_VendorsCms_Block_Vendor_Page_Edit_Tab_Meta extends Mage_Adminhtml_Block_Cms_Page_Edit_Tab_Meta
{
	/**
     * Check permission for passed action
     *
     * @param string $action
     * @return bool
     */
    protected function _isAllowedAction($action)
    {
        return true;
    }
}