<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoBase_Model_Hreflang_HomePage extends MageWorx_SeoBase_Model_Hreflang_Abstract
{

    protected function _getHreflangUrls() {

        $itemId        = '';
        $hreflangUrls  = array();

        $page = Mage::getSingleton('cms/page');

        if (is_object($page) && $page->getPageId()) {

            $itemId = $page->getPageId();

            $hreflangCodes = Mage::helper('mageworx_seobase/alternate')->getAlternateFinalCodes('cms');
            if (empty($hreflangCodes)) {
                return null;
            }

            $hreflangUrlsCollection = Mage::getResourceModel('mageworx_seobase/alternate_cms_page')
                ->getAllCmsUrls(array_keys($hreflangCodes), Mage::app()->getStore()->getStoreId(), $itemId, true);

            if (empty($hreflangUrlsCollection[$itemId]['alternateUrls'])) {
                return null;
            }

            foreach ($hreflangUrlsCollection[$itemId]['alternateUrls'] as $store => $altUrl) {
                $hreflang = $hreflangCodes[$store];
                $hreflangUrls[$hreflang] = $this->_helperTrailingSlash->trailingSlash('home', $altUrl, $store);
            }
        }
        return (!empty($hreflangUrls)) ? $hreflangUrls : null;
    }
}

