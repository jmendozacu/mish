<?php

class VES_VendorsCms_Model_Template_Filter_Url extends VES_VendorsCms_Model_Template_Filter_Abstract
{
	/**
     * Retrieve Static Block html directive
     *
     * @param array $construction
     * @return string
     */
    public function urlDirective($construction)
    {
        $skipParams = array('id');
        $urlParameters = $this->_getIncludeParameters($construction[2]);
        $layout = Mage::app()->getLayout();
		$path = trim($urlParameters['path']);
    	if($vendor = Mage::registry('vendor')){
    		return Mage::helper('vendorspage')->getUrl($vendor,$path);
    	}
    	return '';
    }
}