<?php
class VES_VendorsRma_Block_Vendor_Request_Edit_Renderer_Clamber extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface {
    public function __construct()
    {
       // var_dump($this->getNoteRma());exit;
        if($this->getNoteRma() == false){
            $this->setTemplate('ves_vendorsrma/request/edit/form/clamber.phtml');
        }
        else{
            $this->setTemplate('ves_vendorsrma/request/edit/clamber.phtml');
        }
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

    public function getSaveNote(){
        return $this->getUrl('*/*/saveNote/',array('id'=>Mage::registry('request_data')->getId()));
    }
    public function getSaveNoteAttachment(){
        return $this->getUrl('*/*/saveNoteAttacment/',array('id'=>Mage::registry('request_data')->getId(),'note_id'=>$this->getNoteRma()->getId()));
    }
    
    public function isNoteRma($statusId){
        $check = false;
        if(!Mage::helper("vendorsrma/config")->allowSendRefund()) return $check;

        $note = Mage::getModel("vendorsrma/note")->getCollection()->addFieldToFilter("request_id",Mage::registry('request_data')->getId())
            ->addFieldToFilter("type",VES_VendorsRma_Model_Source_Message_Type::TYPE_VENDOR)->getFirstItem();

        if(!$note->getId()) return $check;

        $status = Mage::getModel("vendorsrma/status")->load(Mage::registry('request_data')->getStatus());
        if(!$status->getId()) throw new Mage_Core_Exception(Mage::helper('vendorsrma')->__('Status do not exits ! '));
        $options = Mage::getModel("vendorsrma/status")->getOptions();
        if(!$status->getData("resolve")) $check = true;
        return $check;
    }

    public function isCloseRequest(){
        return Mage::registry('request_data')->getState() == VES_VendorsRma_Model_Option_State::STATE_CLOSED ? true : false;
    }

    public function getNameAttachment($file){
        $files = explode("/",$file);
        return $files[count($files)-1];
    }
    
    public function getClassIcon($file){
        return Mage::helper('vendorsrma')->getClassIcon($file);
    }

    public function getNoteRma(){
        $check = false;
        if(!Mage::helper("vendorsrma/config")->allowSendRefund()) return $check;

        $note = Mage::getModel("vendorsrma/note")->getCollection()->addFieldToFilter("request_id",Mage::registry('request_data')->getId())
            ->addFieldToFilter("type",VES_VendorsRma_Model_Source_Message_Type::TYPE_VENDOR)->getFirstItem();

        if(!$note->getId()) return $check;
        return $note;
    }

}