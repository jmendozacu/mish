<?php

class VES_VendorsPriceComparison2_Model_Source_Status extends Varien_Object
{
    static public function getOptionArray()
    {
    	$result = array(
    	    VES_VendorsPriceComparison2_Model_Pricecomparison::STATUS_PENDING => Mage::helper('pricecomparison2')->__('Pending'),
    	    VES_VendorsPriceComparison2_Model_Pricecomparison::STATUS_APPROVED => Mage::helper('pricecomparison2')->__('Approved'),
    	    VES_VendorsPriceComparison2_Model_Pricecomparison::STATUS_UNAPPROVED => Mage::helper('pricecomparison2')->__('Unapproved'),
    	);
    	return $result;
    }
    
	static public function toOptionArray()
    {
    	$result = array(
    	    array('label'=> Mage::helper('pricecomparison2')->__('-- Please select --'),'value'=>''),
    	    array('label'	=> Mage::helper('pricecomparison2')->__('Pending'),'value' => VES_VendorsPriceComparison2_Model_Pricecomparison::STATUS_PENDING),
    	    array('label'	=> Mage::helper('pricecomparison2')->__('Approved'),'value' => VES_VendorsPriceComparison2_Model_Pricecomparison::STATUS_APPROVED),
    	    array('label'	=> Mage::helper('pricecomparison2')->__('Unapproved'),'value' => VES_VendorsPriceComparison2_Model_Pricecomparison::STATUS_UNAPPROVED),
    	);
    	return $result;
    }

}