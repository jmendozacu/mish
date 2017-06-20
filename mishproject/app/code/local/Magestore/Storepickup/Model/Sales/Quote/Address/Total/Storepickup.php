<?php

class Magestore_Storepickup_Model_Sales_Quote_Address_Total_Storepickup extends Mage_Sales_Model_Quote_Address_Total_Abstract {

    public function collect(Mage_Sales_Model_Quote_Address $address) {
        $datashipping = Mage::getSingleton('checkout/session')->getData('storepickup_session');
        if ($datashipping['date'] || $datashipping['time'])
            $address->setShippingDescription($address->getShippingDescription() . '<br/>' . Mage::helper('storepickup')->__('Pickup Time:') . '<br/>' . $datashipping['date'] . ' ' . $datashipping['time']. '<br/>');
        return $this;
    }

}
