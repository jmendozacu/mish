<?php
class VES_VendorsSelectAndSell_SelectandsellController extends VES_Vendors_Controller_Action
{
	/**
     * Initialize product from request parameters
     *
     * @return Mage_Catalog_Model_Product
     */
    protected function _initProduct()
    {
        $this->_title($this->__('Catalog'))->_title($this->__('Manage Products'));

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
     * Load edit form
     */
	public function indexAction(){
		$product	= $this->_initProduct();	
		$block = $this->getLayout()->createBlock('selectandsell/product_edit','selectandsell')->setTemplate('ves_selectandsell/product/edit.phtml');
		$this->getResponse()->setBody($block->toHtml());
	}
	
	public function saveAction(){
		$product = $this->_initProduct();
		$product->setVendorId($this->_getSession()->getVendor()->getVendorId());
		$productData 	= $this->getRequest()->getParam('vendor_product');
		unset($productData['stock_data']);
        try {
        	$product->addData($productData);
        	$product->setVendorRelatedProduct($product->getId());
            $newProduct = $product->duplicate();
            $this->_getSession()->addSuccess($this->__('The product has been saved.'));
            $this->_redirect('vendors/catalog_product/edit',array('id'=>$newProduct->getId()));
        } catch (Exception $e) {
        	echo $e->getMessage();exit;
            Mage::logException($e);
            $result = array('success'=>false,'msg'=>$this->__($e->getMessage()));
        }
	}
}