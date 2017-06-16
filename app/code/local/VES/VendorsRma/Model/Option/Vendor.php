<?php

class VES_VendorsRma_Model_Option_Vendor extends Varien_Object
{


    static public function getOptionArray()
    {
        $collections = Mage::getModel("vendors/vendor")->getCollection();
        $datas = array();
        foreach ($collections as $ps) {
            $datas[$ps->getId()] =  $ps->getVendorId();
        }
        return $datas;
    }

    static public function getOptions(){
        $collections = Mage::getModel("vendors/vendor")->getCollection();
        $datas = array();
        foreach ($collections as $ps) {
            $datas[] = array(
                'value'     => $ps->getId(),
                'label'     => $ps->getVendorId(),
            );
        }
        return $datas;
    }

}