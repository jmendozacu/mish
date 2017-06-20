<?php
class VES_VendorsRma_Block_Customer_Guest_View extends VES_VendorsRma_Block_Customer_Account_View
{
    public function _prepareLayout()
    {
        return Mage_Core_Block_Template::_prepareLayout();
    }


    public function getCancelUrl(){
        return $this->getUrl('vesrma/rma_guest/cancel/',array('id'=>$this->getRequestRma()->getId()));
    }

    public function getPrintUrl(){
        return $this->getUrl('vesrma/rma_guest/print/',array('id'=>$this->getRequestRma()->getId()));
    }

    public function getConfirmShippingUrl(){
        return $this->getUrl('vesrma/rma_guest/confirmship/',array('id'=>$this->getRequestRma()->getId(),"back"=>"view"));
    }

    public function getNoteUrl(){
        return $this->getUrl('vesrma/rma_guest/note/',array('id'=>$this->getRequestRma()->getId(),"back"=>"view"));
    }

}