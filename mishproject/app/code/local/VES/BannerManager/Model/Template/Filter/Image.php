<?php

class VES_BannerManager_Model_Template_Filter_Image extends VES_VendorsCms_Model_Template_Filter_Abstract
{
	/**
     * Retrieve Static Block html directive
     *
     * @param array $construction
     * @return string
     */
    public function imageDirective($construction)
    {
        $skipParams = array('id');
        $blockParameters = $this->_getIncludeParameters($construction[2]);
        $layout = Mage::app()->getLayout();
    	$block = $layout->createBlock('bannermanager/vendor_widget_image')->setVendor(Mage::registry('vendor'));
        if ($block) {
        	$block->setImageId($blockParameters['id']);
        }
        if ($block) {
            $block->setBlockParams($blockParameters);
            foreach ($blockParameters as $k => $v) {
                if (in_array($k, $skipParams)) {
                    continue;
                }
                $block->setDataUsingMethod($k, $v);
            }
        }
        if (!$block) {
            return '';
        }

        return $block->toHtml();
    }
}