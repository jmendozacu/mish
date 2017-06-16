<?php
class VES_VendorsRma_Block_Customer_Guest_New_Info extends VES_VendorsRma_Block_Customer_Guest_New
{
    public function getOrderByCustomerId(){
        $orders = array();
        if(Mage::registry("order_continue"))
        $orders[] = Mage::registry("order_continue");

        return $orders;
    }
    public function getRequestData(){
        $data = Mage::getSingleton('core/session')->getRequestData() ? Mage::getSingleton('core/session')->getRequestData() : array();
        if(!$data["order_incremental_id"]) $data["order_incremental_id"] =  Mage::registry("order_continue")->getIncrementId();
        return $data;
    }
    public function getPackOpened(){
        return Mage::getModel("vendorsrma/option_pack")->getOptionArray();
    }

    public function getType(){
        return Mage::getModel("vendorsrma/type")->getToOptions();
    }

    public function getReason(){
        return Mage::getModel("vendorsrma/reason")->getToOptions();
    }

    public function getUrlFindProduct(){
        return $this->getUrl('vesrma/rma_guest/ajaxproduct');
    }
}