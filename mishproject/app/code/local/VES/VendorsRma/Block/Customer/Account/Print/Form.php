<?php
class VES_VendorsRma_Block_Customer_Account_Print_Form extends VES_VendorsRma_Block_Customer_Account_Print
{

    public function _prepareLayout()
    {
        return VES_VendorsRma_Block_Customer_Abstract::_prepareLayout();
    }

    public function getCustomerInfor(){
        return Mage::registry("customer_info");
    }

}