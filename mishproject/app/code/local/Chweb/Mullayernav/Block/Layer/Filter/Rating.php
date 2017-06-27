<?php
/**
* Chweb Multi Layered Navigation 
* 
* @category     Chweb
* @package      Chweb_Mullayernav 
* @copyright    Copyright (c) 2016-2017 Chweb (http://www.chaudharyweb.com/)
* @author       Chweb (Rajesh chaudhary)  
* @version      Release: 1.0.0
* @Class        Chweb_Mullayernav_Block_Layer_Filter_Rating   
*/ 
class Chweb_Mullayernav_Block_Layer_Filter_Rating extends Mage_Catalog_Block_Layer_Filter_Abstract
{

	  public function __construct() {
        parent::__construct();
        //Load Custom PHTML of attributes 
		
        $this->setTemplate('mullayernav/filter_rating.phtml');
        //Set Filter Model Name
        $this->_filterModelName = 'mullayernav/layer_filter_rating';
		
		
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

      public function getHtmlId($item) {
              return $this->getVar() . '-' . $item->getValue();
    }
  public function getUrlId($item) {
        return $this->getVar() . '=' . $item->getValue();
    }
    public function Selectedfilter($item) {
        $ids = (array) $this->_filter->getActiveState();
        return in_array($item->getValue(), $ids);
    }

    public function getFiltersArray() {
        	 $_filtersArray = array();
        if($this->getRequest()->getModuleName()=='catalog') {
		$urls=Mage::helper('core/url')->getHomeUrl().Mage::registry('current_category')->getUrlPath();
	   }else{
		$urls='';
		
	       }
        foreach ($this->getItems() as $_item) {


            $showSwatches = Mage::getStoreConfig('mullayernav/mullayernav/show_swatches');
            $_htmlFilters = 'id="' . $this->getHtmlId($_item) . '" ';
            $var_href = "#";

            //Create URL
             if($urls!=''){$var_href = html_entity_decode($urls.'?'.$this->getUrlId($_item));}
		   else{ $var_href='#';}            $_htmlFilters .= 'href="' . $var_href . '" ';

            $_htmlFilters .= 'class="chweb_layered_attribute '
                    . ($this->Selectedfilter($_item) ? 'chweb_layered_attribute_selected' : '') . '" ';

            //Check the number of products against filter
            $qty = '';
            if (!$this->getHideQty())
                $qty = '(' .  $_item->getCount() .')';

             $_filtersArray[] = '<a ' . $_htmlFilters . '>' . $_item->getLabel() . '</a>' . $qty;
        }

        return $_filtersArray;
    }

}
