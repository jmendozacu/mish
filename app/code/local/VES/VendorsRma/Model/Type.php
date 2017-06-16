<?php

class VES_VendorsRma_Model_Type extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('vendorsrma/type');
    }

    /** get All type  Collection  */
    public function getOptions(){
        $collections = $this->getCollection();
        $collections->addFieldToFilter("store_id",array(array("finset"=>$this->getStore()->getId()),array("finset"=>"0")));
        $collections->addFieldToFilter("active",VES_VendorsRma_Model_Option_Status::STATUS_ENABLED);
        $datas = array();
        foreach ($collections as $ps) {
            $datas[] = array(
                'value'     => $ps->getId(),
                'label'     => $ps->getTitle(),
            );
        }
        return $datas;
    }

    /** get All type Collection  */

    public function getToOptions(){
        $collections = $this->getCollection();
        $collections->addFieldToFilter("store_id",array(array("finset"=>$this->getStore()->getId()),array("finset"=>"0")));
        $collections->addFieldToFilter("active",VES_VendorsRma_Model_Option_Status::STATUS_ENABLED);
        $datas = array();
        foreach ($collections as $ps) {
            $datas[$ps->getId()] = $ps->getTitle();
        }
        return $datas;
    }

    public function getStore(){
        return Mage::app()->getStore();
    }
}