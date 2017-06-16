<?php
class VES_VendorsRma_Block_Customer_Account_Form extends VES_VendorsRma_Block_Customer_Account_View
{

    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    public function getUrlReply(){
        return $this->getUrl('vesrma/rma_customer/postreply',array('id'=>$this->getRequestRma()->getId()));
    }


    public function getClassHeader(){
        return  'header_op';
    }



}