<?php

/**
* Chweb Multi Layered Navigation 
* 
* @category     Chweb
* @package      Chweb_Mullayernav 
* @copyright    Copyright (c) 2014-2015 Chweb (http://www.chaudharyweb.com/)
* @author       Chweb (Rajesh chaudhary)  
* @version      Release: 1.0.0
* @Class        Chweb_Mullayernav_Block_Layer_Filter_Price  
 * @Overwrite    Mage_Catalog_Block_Layer_Filter_Category
 */
class Chweb_Mullayernav_Block_Layer_Filter_Category extends Mage_Catalog_Block_Layer_Filter_Category {

    public function __construct() {  
        parent::__construct();
        //Load Custom PHTML of category 
        $this->setTemplate('mullayernav/filter_category.phtml');
        //Set Filter Model Name
        $this->_filterModelName= 'mullayernav/layer_filter_category';
    }

    public function getVar() {
        //Get request variable name which is used for apply filter
        return $this->_filter->getRequestVar();
    }

    public function getClearUrl() {
        //Get URL and rewrite with SEO frieldly URL
        $_seoURL = '';
        //Get request filters with URL 
        $query = Mage::helper('mullayernav')->getParams();
        if (!empty($query[$this->getVar()])) {
            $query[$this->getVar()] = null;
            $_seoURL = Mage::getUrl('*/*/*', array(
                        '_use_rewrite' => true,
                        '_query' => $query,
            ));
        }

        return $_seoURL;
    }

}
