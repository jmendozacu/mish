<?php

class VES_VendorsFeaturedProduct_Model_Source_Vendor_Template
{
    public function toOptionArray($vendorId, $nullItem = true){
    	$templates = array();
    	if($nullItem){
        	$templates[] = array(
                  'value'     => '',
                  'label'     => Mage::helper('bannermanager')->__('-- Select a Template --'),
            );
    	}
		$templates[] = array('value'=>"ves_vendorscategory/page/simple_vertical_nav.phtml",'label'=>"Grid Template");
		$templates[] = array('value'=>"ves_vendorscategory/page/simple_vertical_nav.phtml",'label'=>"List Template");
    	return $templates;
    }
    
	public function getOptionArray($vendorId, $nullItem = false){
    	$templates = array();
    	if($nullItem){
    		$templates[''] = Mage::helper('bannermanager')->__('-- Select a Template --');
    	}
    	$templates["ves_vendorsfeaturedproduct/grid.phtml"] ="Grid Template";
		$templates["ves_vendorsfeaturedproduct/list.phtml"] = "List Template";
    	return $templates;
    }
}