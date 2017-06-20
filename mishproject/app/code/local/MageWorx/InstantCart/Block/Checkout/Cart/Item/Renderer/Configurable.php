<?php

class MageWorx_InstantCart_Block_Checkout_Cart_Item_Renderer_Configurable extends MageWorx_InstantCart_Block_Checkout_Cart_Item_Renderer_Configurable_Amasty_Pure
{
	public function getProductThumbnail()
    {
        $product = $this->getChildProduct();
		if(!$product->getData('thumbnail') && $product->getId()){
			$product = Mage::getModel('catalog/product')->load($product->getId());
		}
        if (!$product || !$product->getData('thumbnail')
            || ($product->getData('thumbnail') == 'no_selection')
            || (Mage::getStoreConfig(self::CONFIGURABLE_PRODUCT_IMAGE) == self::USE_PARENT_IMAGE)) {
            $product = $this->getProduct();
        }
        return $this->helper('catalog/image')->init($product, 'thumbnail');
    }
}