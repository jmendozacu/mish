<?php

class VES_VendorsQuote_Model_Source_Status extends Varien_Object
{   
    
    public function toOptionArray()
    {
        return array(
            VES_VendorsQuote_Model_Quote::STATUS_CREATED            => Mage::helper('vendorsquote')->__('Starting'),
            VES_VendorsQuote_Model_Quote::STATUS_CREATED_NOT_SENT   => Mage::helper('vendorsquote')->__('Proposal created, not sent'),
            VES_VendorsQuote_Model_Quote::STATUS_PROCESSING => Mage::helper('vendorsquote')->__('Processing'),
            VES_VendorsQuote_Model_Quote::STATUS_EXPIRED    => Mage::helper('vendorsquote')->__('Proposal Expired'),
            VES_VendorsQuote_Model_Quote::STATUS_HOLD       => Mage::helper('vendorsquote')->__('Proposal on Hold'),
            VES_VendorsQuote_Model_Quote::STATUS_SENT       => Mage::helper('vendorsquote')->__('Proposal Sent'),
            VES_VendorsQuote_Model_Quote::STATUS_CANCELLED  => Mage::helper('vendorsquote')->__('Proposal Cancelled'),
            VES_VendorsQuote_Model_Quote::STATUS_REJECTED   => Mage::helper('vendorsquote')->__('Proposal Rejected'),
            VES_VendorsQuote_Model_Quote::STATUS_ACCEPTED   => Mage::helper('vendorsquote')->__('Proposal Accepted'),
            VES_VendorsQuote_Model_Quote::STATUS_ORDERED    => Mage::helper('vendorsquote')->__('Ordered'),
        );
    }
    
    public function getOptionArray($notIncludeCreatedStatus = true){
        $options = $this->toOptionArray();

        return $options;
    }
}