<?php

class VES_VendorsQuote_Model_Source_System_Config_Source_Email_Template extends Mage_Adminhtml_Model_System_Config_Source_Email_Template
{   
    
    public function toOptionArray()
    {
        $options = parent::toOptionArray();
        array_unshift($options,array('value'=> 'disabled','label' => Mage::helper('vendorsquote')->__('Disable Email Notification')));
        return $options;
    }
}