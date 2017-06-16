<?php

class VES_VendorsSellerList_Model_Source_Columns extends Varien_Object
{
    static function getOptionArray() {
        return array(
            array('label'=>Mage::helper('core')->__('Empty'),'value'=>'empty'),
            array('label'=>Mage::helper('core')->__('1 column'),'value'=>'one_column'),
            array('label'=>Mage::helper('core')->__('2 columns left'),'value'=>'two_columns_left'),
            array('label'=>Mage::helper('core')->__('2 columns right'),'value'=>'two_columns_right'),
            array('label'=>Mage::helper('core')->__('3 columns'),'value'=>'three_columns'),
        );
    }

    public function toOptionArray() {
        return self::getOptionArray();
    }
}