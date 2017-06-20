<?php
class VES_VendorsRma_Block_Adminhtml_Request_Edit_Renderer_Clamber extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface {
    public function __construct()
    {
        $this->setTemplate('ves_vendorsrma/request/edit/admin/clamber.phtml');
    }

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        return $this->toHtml();
    }

    public function getValue($value = null){
        if($value) return Mage::registry('request_data')->getData($value);
        return Mage::registry('request_data')->getData();
    }

    public function getClassIcon($file){
        return Mage::helper('vendorsrma')->getClassIcon($file);
    }

    public function getNoteRma(){
        $check = false;
        if(!Mage::helper("vendorsrma/config")->allowSendRefund()) return $check;

        $note = Mage::getModel("vendorsrma/note")->getCollection()->addFieldToFilter("request_id",Mage::registry('request_data')->getId());

        if(count($note) == 0) return $check;
        return $note;
    }

    

    public function getNameAttachment($file){
        $files = explode("/",$file);
        return $files[count($files)-1];
    }
    
    
    
    public function getNoteRmaCustomer(){
        $check = false;
        if(!Mage::helper("vendorsrma/config")->allowSendRefund()) return $check;

        $note = Mage::getModel("vendorsrma/note")->getCollection()->addFieldToFilter("request_id",Mage::registry('request_data')->getId())
            ->addFieldToFilter("type",VES_VendorsRma_Model_Source_Message_Type::TYPE_CUSTOMER)->getFirstItem();

        if(!$note->getId()) return $check;
        return $note;
    }

    public function getNoteRmaVendor(){
        $check = false;
        if(!Mage::helper("vendorsrma/config")->allowSendRefund()) return $check;

        $note = Mage::getModel("vendorsrma/note")->getCollection()->addFieldToFilter("request_id",Mage::registry('request_data')->getId())
            ->addFieldToFilter("type",VES_VendorsRma_Model_Source_Message_Type::TYPE_VENDOR)->getFirstItem();

        if(!$note->getId()) return $check;
        return $note;
    }



}