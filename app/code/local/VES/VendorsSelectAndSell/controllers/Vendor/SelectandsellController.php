<?php

class VES_VendorsSelectAndSell_Vendor_SelectandsellController extends VES_Vendors_Controller_Action {
    /**
     * The greatest value which could be stored in CatalogInventory Qty field
     */
    const MAX_QTY_VALUE = 99999999.9999;

    public function preDispatch() {
        parent::preDispatch();
        Mage::register('is_sell_pre_dispatch',1);
    }

    public function selectAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('catalog')->_title($this->__('Catalog'))->_title($this->__('Select and Sell'));
        $this->_addBreadcrumb(Mage::helper('vendors')->__('Catalog'), Mage::helper('vendors')->__('Select and Sell'));
        $this->_addBreadcrumb(Mage::helper('vendors')->__('Select and Sell'), Mage::helper('vendors')->__('Select and Sell'));
        $this->renderLayout();
    }

    public function saveAction() {
        $product = $this->_initProduct();

        $allowAttributes = Mage::helper('selectandsell')->getAllowAttributeForSell();
        $session = Mage::getSingleton('vendors/session');
        $product->setVendorId($session->getVendor()->getId());
        $product->setData('ves_vendor_featured',0);
        $productData 	= $this->getRequest()->getParam('product');

        $stockData = array();
        if(is_array($productData['stock_data'])) {
            $stockData = $productData['stock_data'];
        }
        $this->_filterStockData($stockData);
        $productData['stock_data'] = $stockData;
        try {
            $product->addData($productData);
            $product->setVendorRelatedProduct($product->getId());
            $newProduct = $this->duplicateProduct($product);
            if(is_array($stockData)) {
                if($newProduct->getId()) {
                    $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($newProduct->getId());
                    $stockItem->addData($stockData)->save();
                    //$newProduct->save();
                }
            }
            $session->addSuccess($this->__('The product has been added.Please submit for approval.'));
            $this->_redirect('vendors/catalog_product/edit',array('id'=>$newProduct->getId()));
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_getSession()->addError($e->getMessage());
            $this->_redirect('*/*/load', array(
                'product_id'    => $product->getId(),
                '_current'=>true
            ));
        }
    }

    /**
     * Product grid for AJAX request
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Load edit form
     */
    public function loadAction(){
        $product = $this->_initProduct();

        if($product->getId()) {
            if(!Mage::helper('selectandsell')->allowToSell($product)) {
                $this->_getSession()->addError($this->__('This product cannot to sell.'));
                $this->_redirect('*/*/select');
                return;
            }
        }
        $this->_title($this->__('New Sell Product'));

        Mage::dispatchEvent('catalog_product_edit_action', array('product' => $product));

        if ($this->getRequest()->getParam('popup')) {
            $this->loadLayout('popup');
        } else {
            $_additionalLayoutPart = '';
            if ($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE
                && !($product->getTypeInstance()->getUsedProductAttributeIds()))
            {
                $_additionalLayoutPart = '_new';
            }
            $this->loadLayout(array(
                'default',
                strtolower($this->getFullActionName()),
                'adminhtml_catalog_product_'.$product->getTypeId() . $_additionalLayoutPart
            ));
            $this->_addBreadcrumb(Mage::helper('vendors')->__('Catalog'), Mage::helper('vendors')->__('Catalog'))
                ->_addBreadcrumb(Mage::helper('vendors')->__('Select and Sell'), Mage::helper('vendors')->__('Select and Sell'),Mage::getUrl('vendors/vendor_selectandsell/select'))
                ->_addBreadcrumb(Mage::helper('vendors')->__('New Sell Product'), Mage::helper('vendors')->__('New Sell Product'));
            $this->_setActiveMenu('vendors/vendor_selectandsell/select');
        }

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $block = $this->getLayout()->getBlock('catalog.wysiwyg.js');
        if ($block) {
            $block->setStoreId($product->getStoreId());
        }

        $this->renderLayout();
    }

    /**
     * Initialize product from request parameters
     *
     * @return Mage_Catalog_Model_Product
     */
    protected function _initProduct()
    {
        $this->_title($this->__('Catalog'))->_title($this->__('Select and sell'));

        $productId  = (int) $this->getRequest()->getParam('product_id');
        $product    = Mage::getModel('catalog/product')
            ->setStoreId($this->getRequest()->getParam('store', 0));

        if (!$productId) {
            if ($setId = (int) $this->getRequest()->getParam('set')) {
                $product->setAttributeSetId($setId);
            }

            if ($typeId = $this->getRequest()->getParam('type')) {
                $product->setTypeId($typeId);
            }
        }

        $product->setData('_edit_mode', true);
        if ($productId) {
            try {
                $product->load($productId);
            } catch (Exception $e) {
                $product->setTypeId(Mage_Catalog_Model_Product_Type::DEFAULT_TYPE);
                Mage::logException($e);
            }
        }

        $attributes = $this->getRequest()->getParam('attributes');
        if ($attributes && $product->isConfigurable() &&
            (!$productId || !$product->getTypeInstance()->getUsedProductAttributeIds())) {
            $product->getTypeInstance()->setUsedProductAttributeIds(
                explode(",", base64_decode(urldecode($attributes)))
            );
        }

        Mage::register('product', $product);
        Mage::register('current_product', $product);
        Mage::getSingleton('cms/wysiwyg_config')->setStoreId($this->getRequest()->getParam('store'));
        return $product;
    }

    /**
     * Create duplicate
     *
     * @return Mage_Catalog_Model_Product
     */
    protected function duplicateProduct($product)
    {
        $product->getWebsiteIds();
        $product->getCategoryIds();
        $vendor = Mage::getSingleton('vendors/session')->getVendor();

       // var_dump($product->getStockData());exit;
        /* @var $newProduct Mage_Catalog_Model_Product */
        $newProduct = Mage::getModel('catalog/product')->setData($product->getData())
            ->setIsDuplicate(true)
            ->setOriginalId($product->getId())
            ->setSku($vendor->getVendorId().'_'.$product->getVendorSku())
            //->setStatus(Mage_Catalog_Model_Product_Status::STATUS_DISABLED)   //status depended data input
            ->setCreatedAt(null)
            ->setUpdatedAt(null)
            ->setId(null)
            ->setStoreId(0)
        ;

        Mage::dispatchEvent(
            'catalog_model_product_duplicate',
            array('current_product' => $product, 'new_product' => $newProduct)
        );

        /* Prepare Related*/
        $data = array();
        $product->getLinkInstance()->useRelatedLinks();
        $attributes = array();
        foreach ($product->getLinkInstance()->getAttributes() as $_attribute) {
            if (isset($_attribute['code'])) {
                $attributes[] = $_attribute['code'];
            }
        }
        foreach ($product->getRelatedLinkCollection() as $_link) {
            $data[$_link->getLinkedProductId()] = $_link->toArray($attributes);
        }
        $newProduct->setRelatedLinkData($data);

        /* Prepare UpSell*/
        $data = array();
        $product->getLinkInstance()->useUpSellLinks();
        $attributes = array();
        foreach ($product->getLinkInstance()->getAttributes() as $_attribute) {
            if (isset($_attribute['code'])) {
                $attributes[] = $_attribute['code'];
            }
        }
        foreach ($product->getUpSellLinkCollection() as $_link) {
            $data[$_link->getLinkedProductId()] = $_link->toArray($attributes);
        }
        $newProduct->setUpSellLinkData($data);

        /* Prepare Cross Sell */
        $data = array();
        $product->getLinkInstance()->useCrossSellLinks();
        $attributes = array();
        foreach ($product->getLinkInstance()->getAttributes() as $_attribute) {
            if (isset($_attribute['code'])) {
                $attributes[] = $_attribute['code'];
            }
        }
        foreach ($product->getCrossSellLinkCollection() as $_link) {
            $data[$_link->getLinkedProductId()] = $_link->toArray($attributes);
        }
        $newProduct->setCrossSellLinkData($data);

        /* Prepare Grouped */
        $data = array();
        $product->getLinkInstance()->useGroupedLinks();
        $attributes = array();
        foreach ($product->getLinkInstance()->getAttributes() as $_attribute) {
            if (isset($_attribute['code'])) {
                $attributes[] = $_attribute['code'];
            }
        }
        foreach ($product->getGroupedLinkCollection() as $_link) {
            $data[$_link->getLinkedProductId()] = $_link->toArray($attributes);
        }
        $newProduct->setGroupedLinkData($data);

        $newProduct->save();

        $product->getOptionInstance()->duplicate($product->getId(), $newProduct->getId());
        //$product->getResource()->duplicate($product->getId(), $newProduct->getId());

        // TODO - duplicate product on all stores of the websites it is associated with
        /*if ($storeIds = $this->getWebsiteIds()) {
            foreach ($storeIds as $storeId) {
                $this->setStoreId($storeId)
                   ->load($this->getId());

                $newProduct->setData($this->getData())
                    ->setSku(null)
                    ->setStatus(Mage_Catalog_Model_Product_Status::STATUS_DISABLED)
                    ->setId($newId)
                    ->save();
            }
        }*/
        return $newProduct;
    }

    public function validateAction()
    {
        $response = new Varien_Object();
        $response->setError(false);

        try {
            $productData = $this->getRequest()->getPost('product');

            if ($productData && !isset($productData['stock_data']['use_config_manage_stock'])) {
                $productData['stock_data']['use_config_manage_stock'] = 0;
            }
            /* @var $product Mage_Catalog_Model_Product */
            $product = Mage::getModel('catalog/product');
            $product->setData('_edit_mode', true);
            if ($storeId = $this->getRequest()->getParam('store')) {
                $product->setStoreId($storeId);
            }
            if ($setId = $this->getRequest()->getParam('set')) {
                $product->setAttributeSetId($setId);
            }
            if ($typeId = $this->getRequest()->getParam('type')) {
                $product->setTypeId($typeId);
            }
            if ($productId = $this->getRequest()->getParam('id')) {
                $product->load($productId);
            }

            $dateFields = array();
            $attributes = $product->getAttributes();
            foreach ($attributes as $attrKey => $attribute) {
                if ($attribute->getBackend()->getType() == 'datetime') {
                    if (array_key_exists($attrKey, $productData) && $productData[$attrKey] != ''){
                        $dateFields[] = $attrKey;
                    }
                }
            }
            $productData = $this->_filterDates($productData, $dateFields);

            $product->addData($productData);
            if(!$product->getVendorId()){
                $vendorId = $this->_getSession()->getVendor()->getId();
                $product->setData('vendor_id',$vendorId);
            }
            $product->validate();
            /**
             * @todo implement full validation process with errors returning which are ignoring now
             */
//            if (is_array($errors = $product->validate())) {
//                foreach ($errors as $code => $error) {
//                    if ($error === true) {
//                        Mage::throwException(Mage::helper('catalog')->__('Attribute "%s" is invalid.', $product->getResource()->getAttribute($code)->getFrontend()->getLabel()));
//                    }
//                    else {
//                        Mage::throwException($error);
//                    }
//                }
//            }
        }
        catch (Mage_Eav_Model_Entity_Attribute_Exception $e) {
            $response->setError(true);
            $response->setAttribute($e->getAttributeCode());
            $response->setMessage($e->getMessage());
        } catch (Mage_Core_Exception $e) {
            $response->setError(true);
            $response->setMessage($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->_initLayoutMessages('adminhtml/session');
            $response->setError(true);
            $response->setMessage($this->getLayout()->getMessagesBlock()->getGroupedHtml());
        }

        $this->getResponse()->setBody($response->toJson());
    }

    /**
     * Filter product stock data
     *
     * @param array $stockData
     */
    protected function _filterStockData(&$stockData) {
        $stockDataWithout = Mage::helper('selectandsell')->defaultInventoryWithoutStock();
        foreach($stockDataWithout as $item => $value) {
            if(!isset($stockData[$item])) $stockData[$item] = $value;
        }

        if (!isset($stockData['use_config_manage_stock'])) {
            $stockData['use_config_manage_stock'] = 0;
        }
        if (isset($stockData['qty']) && (float)$stockData['qty'] > self::MAX_QTY_VALUE) {
            $stockData['qty'] = self::MAX_QTY_VALUE;
        }
        if (isset($stockData['min_qty']) && (int)$stockData['min_qty'] < 0) {
            $stockData['min_qty'] = 0;
        }
        if (!isset($stockData['is_decimal_divided']) || $stockData['is_qty_decimal'] == 0) {
            $stockData['is_decimal_divided'] = 0;
        }
    }
}