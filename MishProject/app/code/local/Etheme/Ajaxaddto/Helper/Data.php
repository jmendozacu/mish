<?php

class Etheme_Ajaxaddto_Helper_Data extends Mage_Core_Helper_Abstract
{
    public $theme;

    function __construct()
    {
        $this->theme=Mage::helper('welldone');
    }
    public function addToCartLink($tpl,$_product,$el)
    {
        if($this->theme->getConfigOption('general/catalog_mode')) return;
        $output = '';
        if($this->theme->getConfigOption('products/show_add_to_cart')){
            if (Mage::getStoreConfig('ajax_addto/product_btns/to_cart')) {
                if (!($_product->getTypeInstance(true)->hasRequiredOptions($_product) || $_product->isGrouped())) {
                    $output = str_replace('href="#"','title="'.$el->__('Add to cart').'" onclick="setLocationAjax(\'' . $el->getAddToCartUrl($_product) . '\',\'' . $_product->getId() . '\')"',$tpl);
                } else {
                    $output = str_replace('href="#"','title="'.$el->__('Add to cart').'" href="' . $el->getAddToCartUrl($_product) . '"',$tpl);
                }
            } else {
                $output = str_replace('href="#"','title="'.$el->__('Add to cart').'" href="' . $el->getAddToCartUrl($_product) . '"',$tpl);
            }
            return $output;
        } else return;
    }


    public function compareLink($tpl,$_product,$el,$ajax=true)
    {
        if($this->theme->getConfigOption('general/catalog_mode'))return;
        $output = '';
        if($this->theme->getConfigOption('products/show_add_to_compare')) {
            $_compareUrl = $el->getAddToCompareUrl($_product);
            if (Mage::getStoreConfig('ajax_addto/product_btns/to_compare') && $ajax) {
                if ($_compareUrl) {
                    $output = str_replace('href="#"', 'title="' . $el->__('Add to compare') . '" href="#" onclick="ajaxCompare(\'' . $_compareUrl . '\',' . $_product->getId() . ');return false;"', $tpl);
                }
            } else {
                $output = str_replace('href="#"', 'title="' . $el->__('Add to compare') . '" href="' . $_compareUrl . '"', $tpl);
            }
            return $output;
        } else  return;
    }

    public function wishlistLink($tpl,$_product,$el,$ajax=true)
    {

        if($this->theme->getConfigOption('general/catalog_mode'))return;
        $output = '';
        if (Mage::helper('wishlist')->isAllow() && $this->theme->getConfigOption('products/show_add_to_wishlist'))
        {
            if (Mage::getStoreConfig('ajax_addto/product_btns/to_wishlist') && $ajax)
            {
                $output = str_replace('href="#"','title="'.$el->__('Add to wishlist').'" href="#"  onclick="ajaxWishlist(\'' . $el->helper('wishlist')->getAddUrlWithParams($_product,array('_secure'=>false)) . '\',' . $_product->getId() . ');return false;"',$tpl);
            }else{
                $output = str_replace('href="#"','title="'.$el->__('Add to wishlist').'" href="'.Mage::helper('wishlist')->getAddUrlWithParams($_product,array('_secure'=>false)).'"',$tpl);
            }
            return $output;
        } else  return;
    }
}