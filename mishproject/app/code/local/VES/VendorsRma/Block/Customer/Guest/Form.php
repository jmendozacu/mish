<?php
class VES_VendorsRma_Block_Customer_Guest_Form extends VES_VendorsRma_Block_Customer_Account_Form
{

    public function getUrlReply(){
        return $this->getUrl('vesrma/rma_guest/postreply',array('id'=>$this->getRequestRma()->getId()));
    }

}