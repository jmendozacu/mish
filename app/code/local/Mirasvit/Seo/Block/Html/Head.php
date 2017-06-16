<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Advanced SEO Suite
 * @version   1.1.0
 * @revision  551
 * @copyright Copyright (C) 2014 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_Seo_Block_Html_Head extends Mage_Page_Block_Html_Head
{
    protected function _construct()
    {
        parent::_construct();
        $this->setupCanonicalUrl();
    }

    public function getConfig()
    {
    	return Mage::getSingleton('seo/config');
    }

    public function getRobots()
    {
        if (!$this->getAction()) {
            return;
        }
        if ($product = Mage::registry('current_product')) {
            if ($robots = Mage::helper('seo')->getMetaRobotsByCode($product->getSeoMetaRobots())) {
                return $robots;
            }
        }
    	$fullAction = $this->getAction()->getFullActionName();
        foreach ($this->getConfig()->getNoindexPages() as $record) {
            if (Mage::helper('seo')->checkPattern($fullAction, $record->getPattern())
                || Mage::helper('seo')->checkPattern(Mage::helper('seo')->getBaseUri(), $record['pattern'])) {
                return Mage::helper('seo')->getMetaRobotsByCode($record->getOption());
            }
        }

        return parent::getRobots();
    }

    public function setupCanonicalUrl()
    {

    	if (!$this->getConfig()->isAddCanonicalUrl()) {
    		return;
    	}

        if (!$this->getAction()) {
            return;
        }

    	$fullAction = $this->getAction()->getFullActionName();
        foreach ($this->getConfig()->getCanonicalUrlIgnorePages() as $page) {
            if (Mage::helper('seo')->checkPattern($fullAction, $page)
                || Mage::helper('seo')->checkPattern(Mage::helper('seo')->getBaseUri(), $page)) {
                return;
            }
        }

        $productActions = array(
            'catalog_product_view',
            'review_product_list',
            'review_product_view',
            'productquestions_show_index',
        );

        if (in_array($fullAction, $productActions)) {
            $product = Mage::registry('current_product');

            $collection = Mage::getModel('catalog/product')->getCollection()
                ->addFieldToFilter('entity_id', $product->getId())
                ->addStoreFilter()
                ->addUrlRewrite();

            $product      = $collection->getFirstItem();
            $canonicalUrl = $product->getProductUrl();
        } elseif ($fullAction == 'catalog_category_view') {
            $category     = Mage::registry('current_category');
            $canonicalUrl = $category->getUrl();
        } else {
			$canonicalUrl = Mage::helper('seo')->getBaseUri();
			$canonicalUrl = Mage::getUrl('', array('_direct' => ltrim($canonicalUrl, '/')));
            $canonicalUrl = strtok($canonicalUrl, '?');
        }

        //setup crossdomian URL if this option is enabled
        if ($crossDomainStore = $this->getConfig()->getCrossDomainStore()) {
            $mainBaseUrl = Mage::app()->getStore($crossDomainStore)->getBaseUrl();
            $currentBaseUrl = Mage::app()->getStore()->getBaseUrl();
            $canonicalUrl = str_replace($currentBaseUrl, $mainBaseUrl, $canonicalUrl);
        }

        if (false && isset($product)) { //Ð²Ð¾Ð·Ð¼Ð¾Ð¶Ð½Ð¾ Ð² Ð¿ÐµÑÑÐ¿ÐµÐºÑÐ¸Ð²Ðµ Ð²ÑÐ²ÐµÑÑÐ¸ ÑÑÐ¾ Ð² ÐºÐ¾Ð½ÑÐ¸Ð³ÑÑÐ°ÑÐ¸Ñ. Ñ.Ðº. ÑÑÐ¾ Ð½ÑÐ¶Ð½Ð¾ ÑÐ¾Ð»ÑÐºÐ¾ Ð² Ð½ÐµÐºÐ¾ÑÐ¾ÑÑÑ ÑÐ»ÑÑÐ°ÑÑ.
            // ÐµÑÐ»Ð¸ ÑÑÐµÐ´Ð¸ ÐºÐ°ÑÐµÐ³Ð¾ÑÐ¸Ð¹ Ð¿ÑÐ¾Ð´ÑÐºÑÐ° ÐµÑÑÑ ÐºÐ¾ÑÐ½ÐµÐ²Ð°Ñ ÐºÐ°ÑÐµÐ³Ð¾ÑÐ¸Ñ, ÑÐ¾ ÑÑÑÐ°Ð½Ð°Ð²Ð»Ð¸Ð²Ð°ÐµÐ¼ ÐµÐµ Ð´Ð»Ñ ÐºÐ°Ð½Ð¾Ð½Ð¸ÐºÐ°Ð»
            $categoryIds = $product->getCategoryIds();
            $collection = Mage::getModel('catalog/category')->getCollection()
                    ->addFieldToFilter('entity_id', $categoryIds)
                    ->addFieldToFilter('level', 1);
            if ($collection->count()) {
                $rootCategory = $collection->getFirstItem();
                foreach (Mage::app()->getStores() as $store) {
                      if ($store->getRootCategoryId() == $rootCategory->getId()) {
                        $mainBaseUrl = $store->getBaseUrl();
                        $currentBaseUrl = Mage::app()->getStore()->getBaseUrl();
                        $canonicalUrl = str_replace($currentBaseUrl, $mainBaseUrl, $canonicalUrl);
                      }
                }
            }
        }

        $curUrlWithoutGet = strtok(Mage::helper('core/url')->getCurrentUrl(), '?');
        if (strpos($curUrlWithoutGet, 'shopby') !== false
            && strpos($curUrlWithoutGet, 'price') === false) {
            $canonicalUrl = $curUrlWithoutGet;
        }
        $page = Mage::app()->getRequest()->getParam('p');
        if ($page > 1) {
            $canonicalUrl .= "?p=$page";
        }

        $this->addLinkRel('canonical', $canonicalUrl);
    }
}
