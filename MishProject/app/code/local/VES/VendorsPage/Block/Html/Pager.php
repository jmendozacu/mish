<?php
class VES_VendorsPage_Block_Html_Pager extends Mage_Page_Block_Html_Pager
{
	public function getPagerUrl($params=array())
    {
        $urlParams = array();
        $urlParams['_current']  = true;
        $urlParams['_escape']   = true;
        $urlParams['_use_rewrite']   = true;
        $urlParams['_query']    = $params;
        return Mage::helper('vendorspage')->getUrl(Mage::registry('vendor'),'',$urlParams);
    }
}
