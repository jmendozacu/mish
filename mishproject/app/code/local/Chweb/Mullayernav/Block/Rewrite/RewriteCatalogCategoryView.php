<?php
/**
* Chweb Multi Layered Navigation 
* 
* @category     Chweb
* @package      Chweb_Mullayernav 
* @copyright    Copyright (c) 2014-2015 chweb (http://www.chaudharyweb.com/)
* @author       Chweb (Rajesh chaudhary)  
* @version      Release: 1.0.0
* @Class        Chweb_Mullayernav_Block_Rewrite_RewriteCatalogCategoryView   
*/
 

class Chweb_Mullayernav_Block_Rewrite_RewriteCatalogCategoryView extends Mage_Catalog_Block_Category_View
{ 

  
		
    public function getProductListHtml()
    {
		
		//echo 'fffffffffffffff';die;
        $html = parent::getProductListHtml();
        if ($this->getCurrentCategory()->getIsAnchor()){
            $html = Mage::helper('mullayernav')->wrapProducts($html);
        }
        return $html;
    }   
} 
