<?php
/**
* Chweb Multi Layered Navigation 
* 
* @category     Chweb
* @package      Chweb_Mullayernav 
* @copyright    Copyright (c) 2014-2015 Chweb (http://www.chaudharyweb.com/)
* @author       Chweb (Rajesh chaudhary)  
* @version      Release: 1.0.0
* @Class        Chweb_Mullayernav_FrontController   
*/ 
class Chweb_Mullayernav_FrontController extends Mage_Core_Controller_Front_Action {

    public function categoryAction() {
        // init category
		  $categoryId = (int) $this->getRequest()->getParam('id', false);
        if (!$categoryId) {
            $this->_forward('noRoute');
            return;
        }

        $category = Mage::getModel('catalog/category')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($categoryId);
        Mage::register('current_category', $category);


        $this->loadLayout();

        $response = array();
        $response['layer'] = $this->getLayout()->getBlock('layer')->toHtml();

        $response['products'] = $this->getLayout()->getBlock('root')->toHtml().'<input type="hidden" id="pagecount"  value="'.$this->getLayout()->getBlock('product_list_toolbar')->getLastPageNum().'"><input type="hidden" id="pageenable"  value="'.Mage::getStoreConfig('mullayernav/general/enabled').'">';
	
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
        
        
    }
    public function searchAction() {
        $this->loadLayout();
        $response = array();
        $response['layer'] = $this->getLayout()->getBlock('layer')->toHtml();
        $response['products'] = $this->getLayout()->getBlock('root')->setIsSearchMode()->toHtml().'<input type="hidden" id="pagecount"  value="'.$this->getLayout()->getBlock('product_list_toolbar')->getLastPageNum().'"><input type="hidden" id="pageenable"  value="'.Mage::getStoreConfig('mullayernav/general/enabled').'">';
        
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
    }

}
