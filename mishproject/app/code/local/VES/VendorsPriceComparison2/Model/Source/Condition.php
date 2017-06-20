<?php

class VES_VendorsPriceComparison2_Model_Source_Condition extends Varien_Object
{
    static public function getOptionArray()
    {
    	$result = array(
    	    VES_VendorsPriceComparison2_Model_Pricecomparison::CONDITION_NEW => Mage::helper('pricecomparison2')->__('New'),
    	    VES_VendorsPriceComparison2_Model_Pricecomparison::CONDITION_USED => Mage::helper('pricecomparison2')->__('Used'),
    	);
    	return $result;
    }
    
	static public function toOptionArray()
    {
    	$result = array(
    	    array('label'=> Mage::helper('pricecomparison2')->__('-- Please select --'),'value'=>''),
    	    array('label'	=> Mage::helper('pricecomparison2')->__('New'),'value' => VES_VendorsPriceComparison2_Model_Pricecomparison::CONDITION_NEW),
    	    array('label'	=> Mage::helper('pricecomparison2')->__('Used'),'value' => VES_VendorsPriceComparison2_Model_Pricecomparison::CONDITION_USED),
    	);
    	return $result;
    }

}