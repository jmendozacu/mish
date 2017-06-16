<?php

class VES_VendorsQuickAddProduct_Model_Observer {
    public function ves_vendorsproduct_product_edit_tabs_prepare_after(Varien_Event_Observer $observer) {
        $tabsBlock = $observer->getTabsblock();
		$productId  = (int)  Mage::app()->getRequest()->getParam('id');
		$productIds  = (int)  Mage::app()->getRequest()->getParam('ids');
		if(!$productId && !$productIds)
        $tabsBlock->setTabData('set','content',Mage::app()->getLayout()->createBlock('vendorsqap/vendor_product_edit_tab_settings')->toHtml());
    }

    public function catalog_product_prepare_save(Varien_Event_Observer $ob) {
        $product = $ob->getProduct();
        $productId  = (int)  Mage::app()->getRequest()->getParam('id');
        if (!$productId) {
            if($data = Mage::app()->getRequest()->getParam('ids')) {
                $data = explode(',', $data);
                $product->setData('category_ids', $data);
            }
        }
    }
}