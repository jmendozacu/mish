<?php
class VES_VendorsCms_Model_App_Page_Group_Abstract
{
	/**
	 * Is enabled
	 */
	public function isEnabled(){
		return true;
	}
	/**
	 * Get template html for frontend app adding page
	 */
    public function getHtmlTemplate(){
    	return '';
    }
    
    public function getAvailablePositions(){
    	return array(
    		''							=> Mage::helper('vendorscms')->__('-- Please Select --'),
	    	//'vendorscms.left.top'		=> Mage::helper('vendorscms')->__('Left Column Top'),
    		'vendorscms.left.bottom'	=> Mage::helper('vendorscms')->__('Left Column'),
	    	//'vendorscms.content.top'	=> Mage::helper('vendorscms')->__('Main Content Top'),
    		'vendorscms.content.bottom'	=> Mage::helper('vendorscms')->__('Main Content'),
	    	'top.menu'					=> Mage::helper('vendorscms')->__('Top Navigation Bar'),
	    	'before_body_end'			=> Mage::helper('vendorscms')->__('Page Bottom'),
	    	'footer'					=> Mage::helper('vendorscms')->__('Page Footer'),
	    	'top.container'				=> Mage::helper('vendorscms')->__('Page Header'),
	    	'after_body_start'			=> Mage::helper('vendorscms')->__('Page Top'),
	    	//'vendorscms.right.top'		=> Mage::helper('vendorscms')->__('Right Column Top'),
    		'vendorscms.right.bottom'	=> Mage::helper('vendorscms')->__('Right Column'),
    	);
    }
    
    public function getAvailablePositionOptions(){
    	$html = '';
    	foreach($this->getAvailablePositions() as $key=>$position){
    		$html .='<option value="'.$key.'">'.$position.'</option>';
    	}
    	return $html;
    }
}