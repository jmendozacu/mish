<?php
class OTTO_AdvancedFaq_Block_Search extends OTTO_AdvancedFaq_Block_Abstract
{

	public function getUrlSearch(){
		$url= $this->getUrl($this->getUrlKey()."/search");
		if(Mage::registry("current_seller")){
			$seller = Mage::registry("current_seller");
			$url = Mage::helper("sellerspage")->getUrl($seller,$this->getUrlKey()."/search");
			//$url= $this->getUrl($seller->getData("seller_id")."/".$this->getUrlKey()."/search");
		}
		return $url;
	}
	
	public function getKeyQuery(){
		$key = "";
		if(Mage::registry("key_query")) $key = Mage::registry("key_query");
		return $key;
	}
	
}