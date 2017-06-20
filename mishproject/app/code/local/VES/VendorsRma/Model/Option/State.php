<?php

class VES_VendorsRma_Model_Option_State extends Varien_Object
{
    const STATE_OPEN	= 0;
    const STATE_AWAITING	= 1;
    const STATE_BEING	= 2;
    const STATE_CLOSED	= 3;

    static public function getOptionArray()
    {
        return array(
            self::STATE_OPEN    => Mage::helper('vendorsrma')->__('Open'),
            self::STATE_AWAITING   => Mage::helper('vendorsrma')->__("Awaiting Other Party's Response"),
            self::STATE_BEING    => Mage::helper('vendorsrma')->__('Being Reviewed By Admin'),
            self::STATE_CLOSED   => Mage::helper('vendorsrma')->__('Closed'),
        );
    }

    static public function getOptions(){
        return
            array(
                array(
                    'value'     => self::STATE_OPEN,
                    'label'     => Mage::helper('vendorsrma')->__('Open'),
                ),

                array(
                    'value'     => self::STATE_AWAITING,
                    'label'     => Mage::helper('vendorsrma')->__("Awaiting Other Party's Response"),
                ),
                array(
                    'value'     => self::STATE_BEING,
                    'label'     => Mage::helper('vendorsrma')->__('Being Reviewed By Admin'),
                ),
                array(
                    'value'     => self::STATE_CLOSED,
                    'label'     => Mage::helper('vendorsrma')->__('Closed'),
                ),
            );
    }

    public function getTitleByKey($key, $flagState , $flag = true){
        if($key == self::STATE_OPEN) return Mage::helper('vendorsrma')->__('Open');
        if($key == self::STATE_AWAITING){
            if(Mage::app()->getStore()->isAdmin()) return Mage::helper('vendorsrma')->__("Awaiting Other Party's Response");
            if($flagState == 5 ) return Mage::helper('vendorsrma')->__("Awaiting For Your Response");
            if($flag)
                if($flagState == 2)
                    return Mage::helper('vendorsrma')->__("Awaiting For Your Response");
                else 
                    return Mage::helper('vendorsrma')->__("Awaiting Other Party's Response");
            else 
                if($flagState == 1)
                    return Mage::helper('vendorsrma')->__("Awaiting For Your Response");
                else 
                    return Mage::helper('vendorsrma')->__("Awaiting Other Party's Response");
        }
        if($key == self::STATE_BEING) return Mage::helper('vendorsrma')->__('Being Reviewed By Admin');
        return Mage::helper('vendorsrma')->__('Closed');
    }
}