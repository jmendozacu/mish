<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoBase_Model_Hreflang_Product extends MageWorx_SeoBase_Model_Hreflang_Abstract
{
    protected function _getHreflangUrls()
    {
        $itemId        = '';
        $hreflangUrls  = array();

        $product = Mage::registry('current_product');
        if (is_object($product) && $product->getId()) {

            $itemId = $product->getId();
            $hreflangCodes = Mage::helper('mageworx_seobase/alternate')->getAlternateFinalCodes('product');
            if (empty($hreflangCodes)) {
                return $hreflangUrls;
            }
            $categoryId = $product->getCategoryId();

            $hreflangResource = Mage::helper('mageworx_seobase/factory')->getProductAlternateUrlResource();
            $hreflangUrlsCollection = $hreflangResource->getAllProductUrls(array_keys($hreflangCodes), false, $itemId, $categoryId);

            if (empty($hreflangUrlsCollection[$itemId]['alternateUrls'])) {
                return $hreflangUrls;
            }

            foreach ($hreflangUrlsCollection[$itemId]['alternateUrls'] as $store => $altUrl) {
                $hreflang = $hreflangCodes[$store];
                $hreflangUrls[$hreflang] = $this->_helperTrailingSlash->trailingSlash('product', $altUrl, $store);
            }
        }
        return $hreflangUrls;
    }
}