<?php
class VES_VendorsRma_Block_Customer_Account_New_Info extends VES_VendorsRma_Block_Customer_Account_New
{
    
    public function getOrderByCustomerId(){
        $date = Mage::helper("vendorsrma/config")->orderExpiryDay();
        $lastdate = Mage::getModel('core/date')->date('Y-m-d', strtotime("-".$date." days"));
        $customer_id = Mage::getSingleton('customer/session') ->getCustomer()->getId();
        $orders = Mage::getModel('sales/order')->getCollection()->addAttributeToSelect('*')
            ->addAttributeToFilter("status",array("IN"=>array(Mage_Sales_Model_Order::STATE_COMPLETE,Mage_Sales_Model_Order::STATE_PROCESSING)))
            ->addAttributeToFilter('created_at', array('from'  => $lastdate))
            ->addAttributeToFilter('customer_id',array('eq'=>$customer_id));
        return $orders;
    }

    public function getRequestData(){
        $data = Mage::getSingleton('core/session')->getRequestData() ? Mage::getSingleton('core/session')->getRequestData() : array();
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
        return $this->getUrl('vesrma/rma_customer/ajaxproduct');
    }
    
}