<?php
class OTTO_AdvancedFaq_Block_Abstract extends Mage_Core_Block_Template
{
	public function getUrlKey(){
		return Mage::getStoreConfig('advancedfaq/config/url_key');
	}
	
	public function getCatSuffix(){
		return Mage::getStoreConfig('advancedfaq/config/category_suffix');
	}

	
	public function getHomeTitle(){
		if(Mage::registry("current_seller")){
			return Mage::helper('sellersconfig')->getSellerConfig('advancedfaq/advancedfaq/title',Mage::registry('current_seller')->getId());
		}
		else{
			return Mage::getStoreConfig('advancedfaq/config/title');
		}
	}
	
	public function canShowRatingForm($id){
		$cookie = Mage::getSingleton('core/cookie');
		$ratingIds = explode(',',$cookie->get('otto_advancedfaq_rating_ids'));
		return (!in_array($id, $ratingIds));
	}
	public function getUrlRating($id){
		if(Mage::helper('advancedfaq')->getSecureUrl() == 1){
			$url = $this->getUrl("faq/home/vote",array('id'=>$id,'_secure'=>true));
		}
		else{
			$url = $this->getUrl("faq/home/vote",array('id'=>$id));
		}
		return $url;
	}
	public function getUrlRatingAjax(){
		/*
		if(Mage::helper('advancedfaq')->getSecureUrl() == 1){
			$url = $this->getUrl("faq/home/voteajax",array('_secure'=>true));
		}
		else{
			$url = $this->getUrl("faq/home/voteajax");
		}
		*/
		$seller = Mage::registry("current_seller");
		$url = Mage::helper("sellerspage")->getUrl($seller,"home/voteajax");
		return $url;
	}
	/** check enable rating
	 *
	 */
	public function isEnableRating(){
		if(Mage::helper('advancedfaq')->getArticlesRating() == 1) return true;
		return false;
	}
	public function getRecapcharHtml(){
		return Mage::helper('advancedfaq/recapcha')->getRecapchaHtml();
	}
	/**
	 * check customer login
	 * 
	 * 
	 * 
	
	public function isCustomerLogin(){
		if(Mage::helper("advancedfaq")->getAllowAccess() == 1){
			if(Mage::getSingleton('customer/session')->getCustomer()->getId()){
				return true;
			}
			else{
				return false;
			}
		}
		return true;
	}
	 */
}