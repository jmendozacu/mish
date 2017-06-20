<?php
class VES_VendorsCatalogSearch_Block_Page_Html_Pager extends Mage_Page_Block_Html_Pager
{
	
		/**
		 * Return current URL with rewrites and additional parameters
		 *
		 * @param array $params
		 *        	Query parameters
		 * @return string
		 */
		public function getPagerUrl($params=array())
		{
			$urlParams = array();
			$urlParams['_current']  = true;
			$urlParams['_escape']   = true;
			$urlParams['_use_rewrite']   = true;
			$urlParams['_query']    = $params;
			return Mage::helper('vendorspage')->getUrl(Mage::registry('vendor'),'searchresult',$urlParams);
		}
}