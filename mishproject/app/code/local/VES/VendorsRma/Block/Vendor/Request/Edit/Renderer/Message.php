<?php
class VES_VendorsRma_Block_Vendor_Request_Edit_Renderer_Message extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface {
    public function __construct()
    {
        $this->setTemplate('ves_vendorsrma/request/message.phtml');
    }
    public function isShowButtonReply(){
        
        if (Mage::app()->getStore()->isAdmin()) return true;
        
        if($this->getValue("state") != VES_VendorsRma_Model_Option_State::STATE_OPEN){
            return false;
        }
        /*
        $options = Mage::getModel("vendorsrma/status")->getOptions();
        if($this->getValue("status") == $options[5]["value"]){
            return false;
        }
        */
        return true;
    }
    
    public function getNameAttachment($file){
        $files = explode("/",$file);
        return $files[count($files)-1];
    }
    
    
    
    public function getClassHeaderLink(){
        return "";
    }
    public function isEnableCloseTicket(){
        return true;
    }
    public function isShowButtonEdit(){
        if (!Mage::app()->getStore()->isAdmin()) {
            return false;
        }
        return true;
    }
    public function _prepareLayout()
    {
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'label' => Mage::helper('vendorsrma')->__('Submit'),
                'onclick'   => 'editForm.submit();',
                'class' => 'save'
            ));
        $button->setName('send_request');
        $this->setChild('button_submit', $button);
        //if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
        //$this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        // }
        return parent::_prepareLayout();;
    }
    public function getChildButtonHtml(){
        return $this->getChildHtml('button_submit');
    }

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        return $this->toHtml();
    }

    public function getValue($value = null){
        if($value) return Mage::registry('current_request')->getData($value);
        return Mage::registry('current_request')->getData();
    }

    public function getOrderId($incrementId){
        $order=Mage::getModel('sales/order')->loadByIncrementId($incrementId);
        return $order->getId();
    }

    public function getMessage(){
        $messages=Mage::getModel('vendorsrma/message')->getCollection()->addFieldToFilter('request_id',$this->getValue('entity_id'))->setOrder('created_time', 'ASC');
        return $messages;
    }

    public function isHtmlMessage($message){
        return ($message != strip_tags($message));
    }

    public function getEditorHtml($atttribute){
        $editor = new Varien_Data_Form_Element_Editor($atttribute);
        return $editor->getElementHtml();
    }

    public function getClassIcon($file){
        return Mage::helper('vendorsrma')->getClassIcon($file);
    }


    public function getClassType($message){
        $type = $message->getData('type');
        $class = "";
        switch($type){
            case VES_VendorsRma_Model_Source_Message_Type::TYPE_VENDOR:
                $class = "message-box-department";
                break;
            case VES_VendorsRma_Model_Source_Message_Type::TYPE_ADMIN:
                $class = "message-box-admin";
                break;
            case VES_VendorsRma_Model_Source_Message_Type::TYPE_CUSTOMER:
                $class = "message-box-customer";
                break;
        }
        return $class;
    }

    public function getHtmlEditor(){

        $wysiwygConfig = Mage::getModel('vendorsrma/cms_wysiwyg_config')->getConfigSytem();
        $elementId = "content_message_reply";
        $config = array(
            'label'     => Mage::helper('vendorsrma')->__('Message'),
            'name'      => 'request[comment]',
            'config' => $wysiwygConfig,
            'wysiwyg' => true,
            'style' => 'width:98%; height:350px;',
            'enabled'=>true
        );
        $form = new Varien_Data_Form();
        $editor = new Varien_Data_Form_Element_Editor($config);
        $editor->setForm($form);
        $editor->setId($elementId);
        return $editor->getElementHtml();
    }

}

