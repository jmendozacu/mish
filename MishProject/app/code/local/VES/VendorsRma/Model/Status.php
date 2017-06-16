<?php

class VES_VendorsRma_Model_Status extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('vendorsrma/status');
    }


    /** save Template Status */
    public function saveTemplate($data){
       // var_dump($data);exit;
        $count = sizeof($data["title"]);
        $store_news =array();
        for($i=0 ; $i < $count ; $i++){
            if($data["title"][$i] == "") continue;
            if($i == 0){
                $tem_store = Mage::getModel("vendorsrma/template")->getCollection()->addFieldToFilter("store_id",0)
                    ->addFieldToFilter("status_id",$this->getId())->getFirstItem();
            }else{
                $tem_store = Mage::getModel("vendorsrma/template")->getCollection()->addFieldToFilter("store_id",$data["store_id"][$i - 1])
                    ->addFieldToFilter("status_id",$this->getId())->getFirstItem();
            }

            $base =  array(
                "title" => $data["title"][$i],
                "store_id" => $i == 0 ? 0 : $data["store_id"][$i - 1],
                "template_notify_customer" => $data["template_notify_customer"][$i],
                "template_notify_vendor" => $data["template_notify_vendor"][$i],
                "template_notify_history" => $data["template_notify_history"][$i],
                "template_notify_admin" => $data["template_notify_admin"][$i],
                "status_id"=>$this->getId(),
            );

            $temple = Mage::getModel("vendorsrma/template")->setData($base)->setId();
            if($tem_store->getId()) $temple->setId($tem_store->getId());
            try {
                $temple->save();
                $store_news[] = (int)$temple->getData("store_id");
            } catch (Exception $e) {
                throw new Mage_Core_Exception( $e->getMessage());
            }
        }

        /**  $delete old template */
       // var_dump(array_unique($store_news));exit;
        $deleteOlds =  Mage::getModel("vendorsrma/template")->getCollection()//->addFieldToFilter("store_id",array("nin",array_unique($store_news)))
            ->addFieldToFilter("status_id",$this->getId());
        //var_dump($deleteOlds->getData());exit;
        if($deleteOlds->count()){
            foreach($deleteOlds as $old){
                if(!in_array($old->getData("store_id"),array_unique($store_news))) $old->delete();
                //$old->delete();
            }
        }

        return $this;
    }
    /** get All status Collection  */
    public function getOptions($type = false){
        $collections = $this->getCollection();

        $collections->addFieldToFilter("store_id",array("IN"=>array($this->getStore()->getId(),0)));
        if($type) $collections->addFieldToFilter("type",array("IN"=>array(VES_VendorsRma_Model_Option_Status_Type::TYPE_VENDOR,VES_VendorsRma_Model_Option_Status_Type::TYPE_VENDOR_CUSTOMER)));
        $datas = array();
        foreach ($collections as $ps) {
            $datas[] = array(
                'value'     => $ps->getId(),
                'label'     => $ps->getTitle(),
            );
        }
        return $datas;
    }

    /** get All status Collection  */

    public function getToOptions($type = false){
        $collections = $this->getCollection();
        $collections->addFieldToFilter("store_id",array("IN"=>array($this->getStore()->getId(),0)));
        if($type) $collections->addFieldToFilter("type",array("IN"=>array(VES_VendorsRma_Model_Option_Status_Type::TYPE_VENDOR,VES_VendorsRma_Model_Option_Status_Type::TYPE_VENDOR_CUSTOMER)));
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