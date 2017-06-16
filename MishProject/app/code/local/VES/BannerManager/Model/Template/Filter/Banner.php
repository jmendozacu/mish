<?php

class VES_BannerManager_Model_Template_Filter_Banner extends VES_VendorsCms_Model_Template_Filter_Abstract
{
	/**
     * Retrieve Static Block html directive
     *
     * @param array $construction
     * @return string
     */
    public function bannerDirective($construction)
    {
        $skipParams = array('id');
        $blockParameters = $this->_getIncludeParameters($construction[2]);
        $layout = Mage::app()->getLayout();
    	$block = $layout->createBlock('bannermanager/vendor_widget_banner')->setVendor(Mage::registry('vendor'));
        if ($block) {
        	$block->setBannerId($blockParameters['id']);
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