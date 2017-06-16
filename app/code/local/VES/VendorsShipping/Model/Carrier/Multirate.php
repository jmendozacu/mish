<?php

class VES_VendorsShipping_Model_Carrier_Multirate
    extends Mage_Shipping_Model_Carrier_Abstract
    implements Mage_Shipping_Model_Carrier_Interface
{

    protected $_code = 'vendor_multirate';
    protected $_isFixed = true;

    
    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
            return false;
    }

    public function getAllowedMethods()
    {
        return array('vendor_multirate'=>$this->getConfigData('name'));
    }
}
