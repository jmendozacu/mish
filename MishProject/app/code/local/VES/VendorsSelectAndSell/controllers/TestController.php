<?php
class VES_VendorsSelectAndSell_TestController extends Mage_Core_Controller_Front_Action {
    public function indexAction() {
        $product = Mage::getModel('catalog/product')->load(170);
        var_dump($product->getTypeId());
    }
}