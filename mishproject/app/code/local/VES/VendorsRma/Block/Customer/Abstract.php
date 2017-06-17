<?php
class VES_VendorsRma_Block_Customer_Abstract extends Mage_Core_Block_Template
{
    
    protected function _prepareLayout()
    {
       if($this->getLayout()->getBlock('head')){
           $this->getLayout()->getBlock('head')->setTitle(Mage::helper('vendorsrma')->__('RMA'));
       }
	    
    
        return parent::_prepareLayout();
    }
    
    /*
    public function isEnaleClaim($statusId){
        $check = false;
        if(!Mage::helper("vendorsrma/config")->allowSendRefund()) return $check;
        $status = Mage::getModel("vendorsrma/status")->load($statusId);
        if(!$status->getId()) throw new Mage_Core_Exception(Mage::helper('vendorsrma')->__('Status do not exits ! '));
        $options = Mage::getModel("vendorsrma/status")->getOptions();
        if($status->getId() == $options[1]["value"] || $status->getId() == $options[5]["value"]) $check = true;
        return $check;
    }
    */
    
    public function getRequestRma(){
        return Mage::registry('request');
    }

    public function getClassHeader(){
        return 'header_sp';
    }

    public function getCancelUrl(){
        return $this->getUrl('vesrma/rma_customer/cancel/',array('id'=>$this->getRequestRma()->getId(),"back"=>"view"));
    }

    public function getPrintUrl(){
        return $this->getUrl('vesrma/rma_customer/print/',array('id'=>$this->getRequestRma()->getId()));
    }

    public function getConfirmShippingUrl(){
        return $this->getUrl('vesrma/rma_customer/confirmship/',array('id'=>$this->getRequestRma()->getId(),"back"=>"view"));
    }

    public function getNoteUrl(){
        return $this->getUrl('vesrma/rma_customer/note/',array('id'=>$this->getRequestRma()->getId(),"back"=>"view"));
    }

    public function isPrintRma($statusId){
        $check = false;
        if(!Mage::helper("vendorsrma/config")->allowPrint()) return $check;
        $status = Mage::getModel("vendorsrma/status")->load($statusId);
        if(!$status->getId()) throw new Mage_Core_Exception(Mage::helper('vendorsrma')->__('Status do not exits ! '));
        $options = Mage::getModel("vendorsrma/status")->getOptions();
        if($status->getId() != $options[0]["value"]) $check = true;
        return $check;
    }


    
    public function getClassIcon($file){
        return Mage::helper('vendorsrma')->getClassIcon($file);
    }


    public function isCloseRequest(){
        return $this->getRequestRma()->getState() == VES_VendorsRma_Model_Option_State::STATE_CLOSED ? true : false;
    }
    
    
    public function isNoteRma($state){
        $check = false;
        if(!Mage::helper("vendorsrma/config")->allowSendRefund()) return $check;

        $note = Mage::getModel("vendorsrma/note")->getCollection()->addFieldToFilter("request_id",$this->getRequestRma()->getId())
            ->addFieldToFilter("type",VES_VendorsRma_Model_Source_Message_Type::TYPE_CUSTOMER)->getFirstItem();;
        $options = Mage::getModel("vendorsrma/type")->getOptions();
        if($state == VES_VendorsRma_Model_Option_State::STATE_BEING && $this->getRequestRma()->getType() == $options[0]["value"]
             && $this->getRequestRma()->getFlagState() == 6)
            return $check;
        if(!$note->getId() && $state != VES_VendorsRma_Model_Option_State::STATE_CLOSED) return true;

        return $check;
    }



    public function getNoteRma(){
        $check = false;
        if(!Mage::helper("vendorsrma/config")->allowSendRefund()) return $check;

        $note = Mage::getModel("vendorsrma/note")->getCollection()->addFieldToFilter("request_id",$this->getRequestRma()->getId())
            ->addFieldToFilter("type",VES_VendorsRma_Model_Source_Message_Type::TYPE_CUSTOMER)->getFirstItem();

        if(!$note->getId()) return $check;
        return $note;
    }

    public function isConfirmShippingRma($statusId){
        $check = false;
        if(!Mage::helper("vendorsrma/config")->confirmShipping()) return $check;
        $status = Mage::getModel("vendorsrma/status")->load($statusId);
        if(!$status->getId()) throw new Mage_Core_Exception(Mage::helper('vendorsrma')->__('Status do not exits ! '));
        $options = Mage::getModel("vendorsrma/status")->getOptions();
        if($status->getId() == $options[1]["value"]) $check = true;
        return $check;
    }

    public function getOrderId(){
        $order = Mage::getModel("sales/order")->loadByIncrementId($this->getRequestRma()->getData("order_incremental_id"));
        return $order->getId();
    }

    public function isResolveRma($state){
        $check = false;
        //$status = Mage::getModel("vendorsrma/status")->load($statusId);
       // if(!$status->getId()) throw new Mage_Core_Exception(Mage::helper('vendorsrma')->__('Status do not exits ! '));
       // if(!$status->getData("resolve")) $check = true;
        if($state == VES_VendorsRma_Model_Option_State::STATE_OPEN ) $check = true;
        return $check;
    }

    public function isCancelRma($state){
        $check = false;
        if($state == VES_VendorsRma_Model_Option_State::STATE_OPEN ) $check = true;
        return $check;
    }


}