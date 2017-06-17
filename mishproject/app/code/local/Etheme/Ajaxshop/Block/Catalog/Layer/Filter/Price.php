<?php

class Etheme_Ajaxshop_Block_Catalog_Layer_Filter_Price extends Mage_Catalog_Block_Layer_Filter_Price
{

    public $_currentCategory;
    public $_searchSession;
    public $_productCollection;
    public $_maxPrice;
    public $_minPrice;
    public $_currMinPrice;
    public $_currMaxPrice;
    public $_imagePath;

    public function __construct(){

        $this->_currentCategory = Mage::registry('current_category');
        $this->_searchSession = Mage::getSingleton('catalogsearch/session');
        $this->setProductCollection();
        $this->setMinPrice();
        $this->setMaxPrice();
        $this->setCurrentPrices();
        $this->_imagePath = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'magehouse/slider/';

        parent::__construct();
    }

    public function getSliderStatus(){
        if(Mage::getStoreConfig('ajax_shop/price_slider_settings/slider_loader_active'))
            return true;
        else
            return false;
    }


    public function getHtml(){

        if($this->getSliderStatus()){
            $text='<div id="priceSlider" class="price-slider"></div>'.$this->getSliderJs();
            return $text;
        } else  return parent::_toHtml();
    }

    public function prepareParams(){
        $url="";

        $params=$this->getRequest()->getParams();
        foreach ($params as $key=>$val)
        {
            if($key=='id'){ continue;}
            if($key=='min'){ continue;}
            if($key=='max'){ continue;}
            $url.='&'.$key.'='.$val;
        }
        return $url;
    }

    public function getCurrencySymbol(){
        return Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
    }

    public function getCurrMinPrice(){
        if($this->_currMinPrice > 0){
            $min = $this->_currMinPrice;
        } else{
            $min = $this->_minPrice;
        }
        return $min;
    }

    public function getCurrMaxPrice(){
        if($this->_currMaxPrice > 0){
            $max = $this->_currMaxPrice;
        } else{
            $max = $this->_maxPrice;
        }
        return $max;
    }


    public function getCurrentUrlWithoutParams(){
        $baseUrl = explode('?',Mage::helper('core/url')->getCurrentUrl());
        $baseUrl = $baseUrl[0];
        return $baseUrl;
    }

    public function isAjaxSliderEnabled(){
        return $this->getConfig('ajax_shop/ajax_conf/slider');
    }


    public function getOnSlideCallbacks(){
        return $this->getConfig('ajax_shop/price_slider_conf/onSlide');
    }

    public function getSliderJs(){

        $baseUrl = $this->getCurrentUrlWithoutParams();

        if($this->isAjaxSliderEnabled()){
            $ajaxCall = 'sliderAjax(url);';
        }else{
            $ajaxCall = 'window.location=url;';
        }

       $html = '
        <script type="text/javascript">
			jQuery(function ($j) {
                "use strict";

                var newMinPrice, newMaxPrice, url, temp;
                var categoryMinPrice = '.$this->_minPrice.';
                var categoryMaxPrice = '.$this->_maxPrice.';
                function isNumber(n) {
                  return !isNaN(parseFloat(n)) && isFinite(n);
                }
                if ($j(".price-slider").length) {
                    var priceSlider = document.getElementById("priceSlider");

                    noUiSlider.create(priceSlider, {
                        start: [ '.$this->getCurrMinPrice().', '.$this->getCurrMaxPrice().' ],
                        connect: true,
                        step: 1,
                        range: {
                            "min": categoryMinPrice,
                            "max": categoryMaxPrice
                        }
                    });
                    var tipHandles = priceSlider.getElementsByClassName("noUi-handle"),
                        tooltips = [];

                    // Add divs to the slider handles.
                    for ( var i = 0; i < tipHandles.length; i++ ){
                        tooltips[i] = document.createElement("div");
                        tipHandles[i].appendChild(tooltips[i]);
                    }

                    tooltips[0].className += "tooltip top";
                    tooltips[0].innerHTML = "<div class=\'tooltip-inner\'><span></span><div class=\'tooltip-arrow\'></div></div>";
                    tooltips[0] = tooltips[0].getElementsByTagName("span")[0];
                    tooltips[1].className += "tooltip top";
                    tooltips[1].innerHTML = "<div class=\'tooltip-inner\'><span></span><div class=\'tooltip-arrow\'></div></div>";
                    tooltips[1] = tooltips[1].getElementsByTagName("span")[0];

                    // When the slider changes, write the value to the tooltips.
                    priceSlider.noUiSlider.on("update", function( values, handle ){
                        tooltips[handle].innerHTML = Math.round(values[handle]);
                    });

                    priceSlider.noUiSlider.on("change", function( values ){
                        url = getUrl(values[0],values[1]);
                        '.$ajaxCall.'
                    });

                }

                function getUrl(newMinPrice, newMaxPrice){
						return "'.$baseUrl.'"+"?min="+newMinPrice+"&max="+newMaxPrice+"'.$this->prepareParams().'";
			    }
            });
        </script>
                    ';
        return $html;
    }

    public function getConfig($key){
        return Mage::getStoreConfig($key);
    }


    public function setMinPrice(){
        if( (isset($_GET['q']) && !isset($_GET['min'])) || !isset($_GET['q'])){
            $this->_minPrice = $this->_productCollection->getMinPrice();
            $this->_searchSession->setMinPrice($this->_minPrice);
        }else{
            $this->_minPrice = $this->_searchSession->getMinPrice();
        }
    }


    public function setMaxPrice(){
        if( (isset($_GET['q']) && !isset($_GET['max'])) || !isset($_GET['q'])){
            $this->_maxPrice = $this->_productCollection->getMaxPrice();
            $this->_searchSession->setMaxPrice($this->_maxPrice);
        }else{
            $this->_maxPrice = $this->_searchSession->getMaxPrice();
        }
    }

    public function setProductCollection(){

        if($this->_currentCategory){
            $this->_productCollection = $this->_currentCategory
                ->getProductCollection()
                ->addAttributeToSelect('*')
                ->setOrder('price', 'ASC');
        }else{
            $this->_productCollection = Mage::getSingleton('catalogsearch/layer')->getProductCollection()
                ->addAttributeToSelect('*')
                ->setOrder('price', 'ASC');
        }
    }

    public function setCurrentPrices(){

        $this->_currMinPrice = $this->getRequest()->getParam('min');
        $this->_currMaxPrice = $this->getRequest()->getParam('max');
    }


    public function baseToCurrent($srcPrice){
        $store = $this->getStore();
        return $store->convertPrice($srcPrice, false, false);
    }


    public function setInSession($var, $value){
        $set = "set".$var;
        Mage::getSingleton('catalog/session')->$set($value);
    }

    public function getFromSession($var){
        $get = "get".$var;
        return Mage::getSingleton('catalog/session')->$get();
    }


    public function getStore(){
        return Mage::app()->getStore();
    }
}
