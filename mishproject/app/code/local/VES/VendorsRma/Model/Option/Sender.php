<?php

class VES_VendorsRma_Model_Option_Sender extends Varien_Object
{
    const EMAIL_GENERAL ='general';
    const EMAIL_SALES   ='sales';
    const EMAIL_SUPPORT ='support';
    const EMAIL_CUSTOM1 ='custom1';
    const EMAIL_CUSTOM2 ='custom2';

    static public function getOptionArray()
    {
        return array(
            self::EMAIL_GENERAL => Mage::helper('vendorsrma')->__('General Contact'),
            self::EMAIL_SALES  => Mage::helper('vendorsrma')->__('Sales Representative'),
            self::EMAIL_SUPPORT => Mage::helper('vendorsrma')->__('Customer Support'),
            self::EMAIL_CUSTOM1   => Mage::helper('vendorsrma')->__('Custom Email 1'),
            self::EMAIL_CUSTOM2   => Mage::helper('vendorsrma')->__('Custom Email 2')
        );
    }


    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => self::EMAIL_GENERAL, 'label'=>Mage::helper('vendorsrma')->__('General Contact')),
            array('value' => self::EMAIL_SALES, 'label'=>Mage::helper('vendorsrma')->__('Sales Representative')),
            array('value' => self::EMAIL_SUPPORT, 'label'=>Mage::helper('vendorsrma')->__('Customer Support')),
            array('value' => self::EMAIL_CUSTOM1, 'label'=>Mage::helper('vendorsrma')->__('Custom Email 1')),
            array('value' => self::EMAIL_CUSTOM2, 'label'=>Mage::helper('vendorsrma')->__('Custom Email 2')),

        );
    }


    static public function getEmailSender($email){
        return array(Mage::getStoreConfig('trans_email/ident_'.$email.'/email')=>Mage::getStoreConfig('trans_email/ident_'.$email.'/name'));
    }
}


