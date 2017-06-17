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


class Mirasvit_Seo_Model_Observer extends Varien_Object
{
    protected $isProductTitlePrinted = false;

    public function getConfig()
    {
    	return Mage::getSingleton('seo/config');
    }

	public function applyMeta()
    {
        $headBlock = Mage::app()->getLayout()->getBlock('head');
        if ($headBlock) {
            if (!$seo = Mage::helper('seo')->getCurrentSeo()) {
               return;
            }
            //support of Amasty xLanding pages
            if (Mage::app()->getRequest()->getModuleName() == 'amlanding') {
                return;
            }

            //support of Amasty Shopby pages
            if (Mage::app()->getRequest()->getModuleName() == 'amshopby' && $headBlock->getTitle() != '') {
                return;
            }

            //support of FISHPIG Attribute Splash Pages http://www.magentocommerce.com/magento-connect/fishpig-s-attribute-splash-pages.html
            if (Mage::registry('splash_page')) {
                return;
            }

            if ($seo->getMetaTitle()) {
        	    $headBlock->setTitle(Mage::helper('seo')->cleanMetaTag($seo->getMetaTitle()), '1');
            }

            if ($seo->getMetaDescription()) {
                //Removes HTML tags and unnecessary whitespaces from Description Meta Tag
                $description = $seo->getMetaDescription();
                $description = Mage::helper('seo')->cleanMetaTag($description);
        	    $headBlock->setDescription($description);
            }

            if ($seo->getMetaKeywords()) {
        	    $headBlock->setKeywords(Mage::helper('seo')->cleanMetaTag($seo->getMetaKeywords()));
            }
        }
    }


    public function addCustomAttributeOutputHandler(Varien_Event_Observer $observer)
    {
        $outputHelper = $observer->getEvent()->getHelper();
        $outputHelper->addHandler('productAttribute', $this);
        $outputHelper->addHandler('categoryAttribute', $this);
    }

    public function categoryAttribute(Mage_Catalog_Helper_Output $outputHelper, $outputHtml, $params)
    {
    	if (!Mage::registry('current_category')) {
    		return $outputHtml;
    	}

		$seo = Mage::helper('seo')->getCurrentSeo();
        switch ($params['attribute']) {
            case 'name':
                $outputHtml = $seo->getTitle();
                break;
            case 'description':
                //hide description in layered navigation results
                $layer = Mage::getSingleton('catalog/layer');
                $state = $layer->getState();
                if (count($state->getFilters()) > 0) {
                    $outputHtml = '';
                }
                break;
        }

        return $outputHtml;
    }

    public function productAttribute(Mage_Catalog_Helper_Output $outputHelper, $outputHtml, $params)
    {
    	if (!Mage::registry('current_product') || $this->isProductTitlePrinted) {
    		return $outputHtml;
    	}

        $seo = Mage::helper('seo')->getCurrentSeo();
        switch ($params['attribute']) {
            case 'name':
                $this->isProductTitlePrinted = true;
                $outputHtml = $seo->getTitle();
        }

        return $outputHtml;
    }

	public function addFieldsToCmsEditForm($e)
    {
	    $form = $e->getForm();

        $fieldset = $form->addFieldset('seo_fieldset', array('legend' => Mage::helper('seo')->__('SEO Data'), 'class' => 'fieldset-wide'));

        $fieldset->addField('meta_title', 'text', array(
            'name' => 'meta_title',
            'label' => Mage::helper('seo')->__('Meta Title'),
            'title' => Mage::helper('seo')->__('Meta Title'),
        ));

        $fieldset->addField('description', 'textarea', array(
            'name' => 'description',
            'label' => Mage::helper('seo')->__('SEO Description'),
            'title' => Mage::helper('seo')->__('SEO Description'),
        ));
	}

    /**
     * Check is Request from AJAX
     * Magento 1.4.1.1 does not have this function in core
     *
     * @return boolean
     */
    public function isAjax()
    {
        $request = Mage::app()->getRequest();
        if ($request->isXmlHttpRequest()) {
            return true;
        }

        if ($request->getParam('ajax') || $request->getParam('isAjax')) {
            return true;
        }

        return false;
    }


	public function checkUrl($e)
    {
	    $action  = $e->getControllerAction();
        $url     = $action->getRequest()->getRequestString();
        $fullUrl = $_SERVER['REQUEST_URI'];

		if (Mage::app()->getStore()->isAdmin()) {
    		return;
    	}

        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return;
        }

        if ($this->isAjax()){
            return;
        }

        $urlToRedirect = $this->getUrlWithCorrectEndSlash($url);

        if ($url != $urlToRedirect) {
            $this->redirect($urlToRedirect);
        }

        if (substr($fullUrl, -4, 4) == '?p=1') {
            $this->redirect(substr($fullUrl, 0, -4));
        }

        if (in_array(trim($fullUrl,'/'), array('home', 'index.php', 'index.php/home'))) {
            $this->redirect('/');
        }
	}

    protected function getUrlWithCorrectEndSlash($url)
    {
        $extension = substr(strrchr($url, '.'), 1);

        if (substr($url, -1) != '/' && $this->getConfig()->getTrailingSlash() == Mirasvit_Seo_Model_Config::TRAILING_SLASH) {
            if (!in_array($extension, array('html', 'htm', 'php', 'xml', 'rss'))) {
                $url .= '/';
                if ($_SERVER['QUERY_STRING']) {
                    $url .= '?'.$_SERVER['QUERY_STRING'];
                }
            }
        } elseif ($url != '/' && substr($url, -1) == '/' && $this->getConfig()->getTrailingSlash() == Mirasvit_Seo_Model_Config::NO_TRAILING_SLASH) {
            $url = rtrim($url, '/');
            if ($_SERVER['QUERY_STRING']) {
                $url .= '?'.$_SERVER['QUERY_STRING'];
            }
        }

        return $url;
    }

	protected function redirect($url)
    {
        Mage::app()->getFrontController()->getResponse()
            ->setRedirect($url, 301)
            ->sendResponse();
        die;
	}

    public function addCategorySeoTab($e)
    {
        $tabs = $e->getTabs();
        if (!is_object($tabs->getCategory())) {
            return;
        }
        $ids  = $tabs->getTabsIds();

        $attributeSetId     = $tabs->getCategory()->getDefaultAttributeSetId();
        $groupCollection    = Mage::getResourceModel('eav/entity_attribute_group_collection')
            ->setAttributeSetFilter($attributeSetId)
            ->addFieldToFilter('attribute_group_name', 'SEO')
            ->load();
        $group = $groupCollection->getFirstItem();

        if ($group) {
            $tabs->removeTab('group_'.$group->getAttributeGroupId());
        }

        $tabs->addTab('seo', array(
            'label'     => Mage::helper('seo')->__('SEO'),
            'content'   => Mage::app()->getLayout()->createBlock(
                'seo/adminhtml_catalog_category_tab_seo',
                'category.seo'
            )->toHtml(),
        ));
    }

	public function checkProductUrlRedirect($e)
    {
        $urlFormat = $this->getConfig()->getProductUrlFormat();

        if ($urlFormat != Mirasvit_Seo_Model_Config::URL_FORMAT_SHORT &&
            $urlFormat != Mirasvit_Seo_Model_Config::URL_FORMAT_LONG) {
                return;
        }

        if ($this->isAjax()){
            return;
        }

        $action = $e->getControllerAction();

        if ($action->getRequest()->getModuleName() != 'catalog') { //we redirecto only for catalog
            return;
        }
        if ($action->getRequest()->getControllerName() != 'product') { //we redirecto only for catalog
            return;
        }
        if ($action->getRequest()->getActionName() != 'view') { //we redirecto only from products page. not from images views.
            return;
        }

        $url = ltrim($action->getRequest()->getRequestString(), '/');
        $product = $e->getProduct();
        //we need this because we need to load url rewrites
        //maybe its possible to optimize
        $products = Mage::getModel('catalog/product')->getCollection()
            ->addFieldToFilter('entity_id', $product->getId());
        $product = $products->getFirstItem();
        $productUrl = str_replace(Mage::getBaseUrl(), '', $product->getProductUrl());
        $productUrl = $this->getUrlWithCorrectEndSlash($productUrl);

        if ($productUrl != $url) {
            $url = $product->getProductUrl();
            $url = $this->getUrlWithCorrectEndSlash($url);
            $this->redirect($url);
        }
	}

	public function setupProductUrls($e)
    {
        $collection = $e->getCollection();
        $this->_addUrlRewrite($collection);
	}

   /**
     * Add URL rewrites to collection
     *
     */
    protected function _addUrlRewrite($collection)
    {
        $urlFormat = $this->getConfig()->getProductUrlFormat();
        if ($urlFormat != Mirasvit_Seo_Model_Config::URL_FORMAT_SHORT &&
            $urlFormat != Mirasvit_Seo_Model_Config::URL_FORMAT_LONG) {
                return;
        }

        $urlRewrites = null;

        if (!$urlRewrites) {
            $productIds = array();
            foreach($collection->getItems() as $item) {
                $productIds[] = $item->getEntityId();
            }

            if (!count($productIds)) {
                return;
            }

            $storeId = Mage::app()->getStore()->getId();
            if ($collection->getStoreId()) {
                $storeId = $collection->getStoreId();
            }

            $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
            $tablePrefix = (string)Mage::getConfig()->getTablePrefix();
            $seoCatIds = array();
            foreach ($productIds as $prodId) {
                $seoCatIds[$prodId] = Mage::getResourceModel('catalog/product')->getAttributeRawValue($prodId, 'seo_category', $storeId);
            }

            $select = $connection->select()
                ->from($tablePrefix.'core_url_rewrite', array('product_id', 'request_path', 'category_id'))
                ->where('store_id = ?', $storeId)
                ->where('is_system = ?', 1)
                ->where('product_id IN(?)', $productIds)
                ->order('category_id desc'); // more priority is data with category id

            if ($urlFormat == Mirasvit_Seo_Model_Config::URL_FORMAT_SHORT) {
                $select->where('category_id IS NULL');
            }

            $urlRewrites = array();
            foreach ($connection->fetchAll($select) as $row) {
                if (!isset($urlRewrites[$row['product_id']])) {
                    if (! empty($seoCatIds[$row['product_id']])) {
                        if ($seoCatIds[$row['product_id']] == $row['category_id']) {
                            $urlRewrites[$row['product_id']] = $row['request_path'];
                        }
                    } else {
                        $urlRewrites[$row['product_id']] = $row['request_path'];
                    }
                }
            }
        }

        foreach($collection->getItems() as $item) {
            if (isset($urlRewrites[$item->getEntityId()])) {
                $item->setData('request_path', $urlRewrites[$item->getEntityId()]);
            } else {
                $item->setData('request_path', false);
            }
        }
    }

    public function setupPagingMeta()
    {
        if ($this->getConfig()->isPagingPrevNextEnabled()) {
            Mage::getModel('seo/paging')->createLinks();
        }
    }

    public function saveProductBefore($observer)
    {
        $product = $observer->getProduct();
        if ($product->getStoreId() == 0
        //~ && $product->getOrigData('url_key') != $product->getData('url_key')

        ) {
            Mage::getModel('seo/system_template_worker')->processProduct($product);
        }
    }

    public function httpResponseSendBeforeEvent($e)
    {
        Mage::getSingleton('seo/opengraph')->modifyHtmlResponse($e);
    }
}
