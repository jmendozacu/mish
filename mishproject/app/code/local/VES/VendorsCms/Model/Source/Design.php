<?php

class VES_VendorsCms_Model_Source_Design extends Mage_Core_Model_Design_Source_Design
{
	public function toOptionArray()
    {
    	return $this->getAllOptions(false);
    }
}