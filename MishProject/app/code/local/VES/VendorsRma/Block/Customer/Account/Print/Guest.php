<?php
class VES_VendorsRma_Block_Customer_Account_Print_Guest extends VES_VendorsRma_Block_Customer_Account_Print
{
    public function _prepareLayout()
    {
        return VES_VendorsRma_Block_Customer_Abstract::_prepareLayout();
    }

    public function getPrintUrl(){
        return $this->getUrl('vesrma/rma_guest/printform/',array('id'=>$this->getRequestRma()->getId()));
    }

}