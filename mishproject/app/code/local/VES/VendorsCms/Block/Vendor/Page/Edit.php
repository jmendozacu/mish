<?php

class VES_VendorsCms_Block_Vendor_Page_Edit extends Mage_Adminhtml_Block_Cms_Page_Edit
{
	/**
     * Initialize cms page edit block
     *
     * @return void
     */
    public function __construct()
    {
    	parent::__construct();
    	$this->updateButton('saveandcontinue', 'class','save-and-continue');
    	return $this;
    }
	/**
     * Check permission for passed action
     *
     * @param string $action
     * @return bool
     */
    protected function _isAllowedAction($action)
    {
    	return true;
        //return Mage::getSingleton('admin/session')->isAllowed('cms/page/' . $action);
    }
}