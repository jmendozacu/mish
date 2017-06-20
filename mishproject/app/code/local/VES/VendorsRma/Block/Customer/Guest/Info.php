<?php
class VES_VendorsRma_Block_Customer_Guest_Info extends Mage_Core_Block_Template
{
    public function getOrderByCustomerId(){
        $customer_id = Mage::getSingleton('customer/session') ->getCustomer()->getId();
        $orders = Mage::getModel('sales/order')->getCollection()->addAttributeToSelect('*')
            ->addAttributeToFilter("status",'complete')
            ->addAttributeToFilter('customer_id',array('eq'=>$customer_id));
        return $orders;
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
        return $this->getUrl('vesrma/rma_customer/ajaxproduct');
    }
}