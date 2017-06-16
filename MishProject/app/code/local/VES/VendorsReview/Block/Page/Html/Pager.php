<?php


class VES_VendorsReview_Block_Page_Html_Pager extends Mage_Page_Block_Html_Pager
{
	/**
	 * Return current URL with rewrites and additional parameters
	 *
	 * @param array $params Query parameters
	 * @return string
	 */
	public function getPagerUrl($params=array())
	{
		$url = $this->getUrl().Mage::helper('vendorsreview')->getVendorUrlKey().'/'.Mage::registry('vendor_id').'/rating/index/';
        $currentParams = $this->getRequest()->getParams();
        $params = array_merge($currentParams,$params);
        $paramStr = '';
        foreach($params as $key=>$value){
        	if(!$value) continue;
        	$paramStr[] =$key."=".$value;
        }
        $paramStr = trim(implode('&', $paramStr),'&');
        
        return $paramStr?$url.'?'.$paramStr:$url;
	}
}
