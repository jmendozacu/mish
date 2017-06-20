<?php
/**
 * @category    Bubble
 * @package     Bubble_CmsTree
 * @version     2.0.1
 * @copyright   Copyright (c) 2016 BubbleShop (https://www.bubbleshop.net)
 */
class Bubble_CmsTree_Helper_Cms_Page extends Mage_Cms_Helper_Page
{
    /**
     * @param Mage_Core_Controller_Front_Action $action
     * @param null|int $pageId
     * @return bool
     */
    public function renderPage(Mage_Core_Controller_Front_Action $action, $pageId = null)
    {
        $storeId = Mage::app()->getStore()->getId();
        $customerGroupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
        if (!$this->isAllowed($storeId, $customerGroupId, $pageId)) {
            $redirectPageId = Mage::getStoreConfig('bubble_cmstree/general/cms_not_allowed_page');
            if ($redirectPageId && $redirectPageId != $pageId && !Mage::registry('cms_page_not_allowed_redirect')) {
                Mage::register('cms_page_not_allowed_redirect', true); // avoid infinite loop

                return $this->renderPage($action, $redirectPageId);
            }

            return false;
        }

        return parent::renderPage($action, $pageId);
    }

    /**
     * @param int $storeId
     * @param int $customerGroupId
     * @param int $pageId
     * @return array|bool
     */
    public function isAllowed($storeId, $customerGroupId, $pageId)
    {
        $page = Mage::getModel('cms/page')->load($pageId);
        if ($page->getStoreId() == 0 && !in_array($storeId, $page->getStores())) {
            return false;
        }

        if (!$this->isPermissionsEnabled($storeId)) {
            return true;
        }

        return Mage::getResourceModel('cms/page_permission')->exists($storeId, $customerGroupId, $pageId);
    }

    /**
     * @param mixed $store
     * @return bool
     */
    public function isPermissionsEnabled($store = null)
    {
        return Mage::getStoreConfigFlag('bubble_cmstree/general/permissions_enabled', Mage::app()->getStore($store));
    }

    /**
     * @param mixed $store
     * @return bool
     */
    public function isCreatePermanentRedirects($store = null)
    {
        return Mage::getStoreConfigFlag('bubble_cmstree/general/save_rewrites_history', Mage::app()->getStore($store));
    }
}