<?php
class OTTO_AdvancedFaq_Block_Page_Html_Pager extends Mage_Page_Block_Html_Pager
{
	public function getPagerUrl($params=array())
	{
		$url = $this->getUrl().Mage::helper('advancedfaq')->getSellerUrlKey().'/'.Mage::registry('seller_id').'/'.Mage::getStoreConfig('advancedfaq/config/url_key');
        $currentParams = $this->getRequest()->getParams();
        $params = array_merge($currentParams,$params);
        $paramStr = '';
        $catSuffix 		= Mage::getStoreConfig('advancedfaq/config/category_suffix');
        foreach($params as $key=>$value){
        	if(!$value) continue;
        	if($key == "identifier") $url .= "/".$value.$catSuffix;
        	else{
        	$paramStr[] =$key."=".$value;
        	}
        }
        $paramStr = trim(implode('&', $paramStr),'&');
        
        return $paramStr?$url.'?'.$paramStr:$url;
	}
}