<?php
class VES_VendorsCatalogSearch_Block_Product_List_Toolbar extends Mage_Catalog_Block_Product_List_Toolbar
{
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