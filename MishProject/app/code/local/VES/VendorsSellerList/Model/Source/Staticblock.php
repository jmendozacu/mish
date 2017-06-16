<?php

class VES_VendorsSellerList_Model_Source_Staticblock extends Varien_Object
{
    static function getOptionArray() {
        $collection = Mage::getModel('cms/block')->getCollection()
        ->addFieldToFilter('is_active','1')
        ->addStoreFilter(Mage::app()->getStore()->getId(),false);
        $options = array();
        $options[] = array('label'=>Mage::helper('core')->__('-- Please Select --'),'value'=>'');;
        foreach($collection as $_block){
            $options[] = array('label'=>Mage::helper('core')->__($_block->getTitle()),'value'=>$_block->getData('identifier'));
        }
        return $options;
    }

    public function toOptionArray() {
        return self::getOptionArray();
    }
}