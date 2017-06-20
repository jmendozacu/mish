<?php

class VES_VendorsCms_Model_Source_Block
{
	public function toOptionArray($blankItem=true)
    {
    	$blockCollection = Mage::getModel('vendorscms/block')->getCollection()->addFieldToFilter('vendor_id',Mage::getSingleton('vendors/session')->getVendorId());
    	$options = array();
    	if($blankItem) $options[] = array('label'=>Mage::helper('vendorscms')->__('-- Please select --'),'value'=>'');
    	
    	foreach($blockCollection as $block){
    		$options[] = array('label'=>$block->getTitle(),'value'=>$block->getIdentifier());
    	}
    	return $options;
    }
    
	public function getOptionArray($blankItem=true)
    {
    	$blockCollection = Mage::getModel('vendorscms/block')->getCollection()->addFieldToFilter('vendor_id',Mage::getSingleton('vendors/session')->getVendorId());
    	$options = array();
    	if($blankItem) $options[''] = Mage::helper('vendorscms')->__('-- Please select --');
    	
    	foreach($blockCollection as $block){
    		$options[$block->getIdentifier()] = $block->getTitle();
    	}
    	return $options;
    }
}