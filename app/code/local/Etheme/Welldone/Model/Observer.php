<?php
class Etheme_Welldone_Model_Observer
{
    public function saving_welldone()
    {
        $website=Mage::app()->getRequest()->getParam('website');
        $store=Mage::app()->getRequest()->getParam('store');
        Mage::helper('welldone')->refreshCssFiles($store, $website);
    }
    public function saving_welldonecolors()
    {
        $website=Mage::app()->getRequest()->getParam('website');
        $store=Mage::app()->getRequest()->getParam('store');
        Mage::helper('welldone')->refreshCssFiles($store, $website);
    }
}
