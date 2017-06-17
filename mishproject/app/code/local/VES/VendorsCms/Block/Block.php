<?php

/**
 * Vendor Cms block content block
 *
 * @category   VES
 * @package    VES_VendorCms
 * @author     Vnecoms Team <support@vnecoms.com>
 */
class VES_VendorsCms_Block_Block extends Mage_Core_Block_Abstract
{
    /**
     * Prepare Content HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        $blockId = $this->getBlockId();
        $html = '';
        if ($blockId) {
            $block = Mage::getModel('vendorscms/block')
                ->setVendorId(Mage::registry('vendor')->getId())
                ->loadBlock($blockId);
            if ($block->getIsActive()) {
                /* @var $helper Mage_Cms_Helper_Data */
                $processor = Mage::getModel('vendorscms/template_filter');
                $html = $processor->filter($block->getContent());
                $this->addModelTags($block);
            }
        }
        return $html;
    }
}
