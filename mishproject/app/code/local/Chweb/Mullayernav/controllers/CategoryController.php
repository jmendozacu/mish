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
class Chweb_Mullayernav_CategoryController extends Mage_Core_Controller_Front_Action {
	
	

    public function viewAction() {
          // init category
		  //echo 'dddddddddd';die;
        $categoryId = (int) $this->getRequest()->getParam('id', false);
        if (!$categoryId) {
            $this->_forward('noRoute');
            return;
        }

        $category = Mage::getModel('catalog/category')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($categoryId);
        Mage::register('current_category', $category);


        $this->getLayout()->createBlock('mullayernav/catalog_layer_view');
        $this->loadLayout();
        $this->renderLayout();
    }

}
