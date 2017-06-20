<?php

class OTTO_AdvancedFaq_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function isEnabled(){
		return Mage::getStoreConfig('advancedfaq/config/enable_faq');
	}
	
	public function wordLimiter($str,$maxlen=160,$limit=25,$end_char = '&#8230;'){
		$str = strip_tags($str);
		if (trim($str) == ''){
			return $str;
		}
		if(strlen($str) > $maxlen ) $str=substr($str, 0,$maxlen).$end_char;
		//if(strlen( $str ) > $limit ){
		preg_match('/^\s*+(?:\S++\s*+){1,'.(int) $limit.'}/', $str, $matches);
	
		if (strlen($str) == strlen($matches[0])){
			$end_char = '';
		}
	
		$text=trim(strip_tags($matches[0],'<blockquote>')).$end_char;
		$text=preg_split('/(<blockquote?>|<\/blockquote>)/i', $text, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
		//}
		return $text[0];
	}

	
	public function getArticlesRating(){
		return Mage::getStoreConfig('advancedfaq/config/articeles_rating');
	}
	public function getArticlesPage(){
		return Mage::getStoreConfig('advancedfaq/config/articeles_page');
	}
	
	public function getSecureUrl(){
		return Mage::getStoreConfig('advancedfaq/config/use_secure_url');
	}
	
	public function getMetaKeyword(){
		if(Mage::registry("current_seller")){
			return Mage::helper('sellersconfig')->getSellerConfig('advancedfaq/advancedfaq/meta_keyword',Mage::registry('current_seller')->getId());
		}
		else{
			return Mage::getStoreConfig('advancedfaq/config/meta_keyword');
		}
	}
	
	public function getMetaDescription(){
		if(Mage::registry("current_seller")){
			return Mage::helper('sellersconfig')->getSellerConfig('advancedfaq/advancedfaq/meta_description',Mage::registry('current_seller')->getId());
		}
		else{
			return Mage::getStoreConfig('advancedfaq/config/meta_description');
		}
	}

	
	public function getEnabelBlock(){
		return Mage::getStoreConfig('advancedfaq/block/enable_block');
	}
	public function getEnableSearchBlock(){
		return Mage::getStoreConfig('advancedfaq/block/enable_search_block');
	}
	
	public function getMaxTopic(){
		return Mage::getStoreConfig('advancedfaq/block/max_topic');
	}
	
	
	public function getEnableAccordion(){
		if(Mage::registry("current_seller")){
			$accordiion =  Mage::helper('sellersconfig')->getSellerConfig('advancedfaq/theme/enable_accordion',Mage::registry('current_seller')->getId());
			if($accordiion == 1) return true;
			return false;
		}
		else{
			if(Mage::getStoreConfig('advancedfaq/theme/enable_accordion') == 1) return true;
			return false;
		}
	}
	
	
	public function getThemeStyle(){
		return Mage::getStoreConfig('advancedfaq/theme/theme_style') ;
	}
	
	public function getEnableAccordionStyle(){
		return Mage::getStoreConfig('advancedfaq/theme/style') ;
	}
	
	public function getEnableAccordionDelay(){
		return Mage::getStoreConfig('advancedfaq/theme/delay') ;
	}
	
	public function getEnableAccordionDuration(){
		return Mage::getStoreConfig('advancedfaq/theme/duration') ;
	}
	
	public function getSpeedAccordion(){
		if(Mage::registry("current_seller")){
			return Mage::helper('sellersconfig')->getSellerConfig('advancedfaq/theme/speed',Mage::registry('current_seller')->getId());
		}
		else{
			return Mage::getStoreConfig('advancedfaq/theme/speed');
		}
	}
	
	public function getBindAccordion(){
		if(Mage::registry("current_seller")){
			return Mage::helper('sellersconfig')->getSellerConfig('advancedfaq/theme/bind',Mage::registry('current_seller')->getId());
		}
		else{
			return Mage::getStoreConfig('advancedfaq/theme/bind');
		}
	}
	public function getUrlBase(){
		$secure = Mage::getStoreConfig('advancedfaq/config/use_secure_url');
		if($secure == 1) {
			$url =  Mage::getUrl("",array('_secure'=>true));}
			else{
				$url =  Mage::getUrl("",array('_secure'=>false));
			}
			return $url;
	}
	public function getSellerUrlKey() {
		return Mage::getStoreConfig('sellers/seller_page/url_key');
	}
}