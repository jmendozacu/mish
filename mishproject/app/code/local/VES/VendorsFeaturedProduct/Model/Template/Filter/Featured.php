<?php

class VES_VendorsFeaturedProduct_Model_Template_Filter_Featured extends VES_VendorsCms_Model_Template_Filter_Abstract
{
	/**
     * Retrieve Static Block html directive
     *
     * @param array $construction
     * @return string
     */
    public function featuredDirective($construction)
    {
        $skipParams = array('id');
        $blockParameters = $this->_getIncludeParameters($construction[2]);
        $layout = Mage::app()->getLayout();
    	$block = $layout->createBlock('vendorsfeaturedproduct/vendor_widget_feature')->setVendor(Mage::registry('vendor'));
        if ($block) {
            $block->setBlockParams($blockParameters);
            foreach ($blockParameters as $k => $v) {
                if (in_array($k, $skipParams)) {
                    continue;
                }
                $block->setDataUsingMethod(trim($k), $v);
            }
        }
        if (!$block) {
            return '';
        }
        return $block->toHtml();
    }
}