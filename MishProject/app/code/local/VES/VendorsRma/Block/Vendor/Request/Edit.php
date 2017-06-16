<?php

class VES_VendorsRma_Block_Vendor_Request_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'vendorsrma';
        $this->_controller = 'vendor_request';

        $this->_updateButton('save', 'label', Mage::helper('vendorsrma')->__('Save'));

        $this->_updateButton('save', 'id', Mage::helper('vendorsrma')->__('vesrma-save-button'));
        $this->_updateButton('save', 'onclick', "formControl.validateForm()");

        $this->_removeButton("delete");

        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);


        if(!$this->getOrderRequest()){
            $this->_removeButton("save");
            $this->_removeButton("saveandcontinue");
            $this->_addButton('next', array(
                'label'     => Mage::helper('vendorsrma')->__('Next'),
                'onclick'   => 'setSettings(\'' . $this->getCreateUrl() .'\',\'order_id\',\'package_opened\')',
                'class'     => 'save',

            ), -100);
        }

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('vendorsrma_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'vendorsrma_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'vendorsrma_content');
                }
            }


            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }

        ";
        
      
        $request = $this->getRequestRma();
        
    
        
      
        /*
        $options = Mage::getModel("vendorsrma/status")->getOptions();
        
        if($request->getStatus() == $options[0]["value"]){
            $this->_addButton('approval', array(
                'label'     => Mage::helper('adminhtml')->__('Approval'),
                'onclick'   => 'setLocation(\'' . $this->getApprovalUrl() . '\')',
             ), -1);
        }
        
        if($request->getStatus() == $options[2]["value"]){
            $this->_addButton('confirmship', array(
                'label'     => Mage::helper('adminhtml')->__('Confirm Shipping'),
                'onclick'   => 'setLocation(\'' . $this->getConfirmShipUrl() . '\')',
            ), -1);
        }
        */
        
        
        if( Mage::registry('request_data') && Mage::registry('request_data')->getId() && !Mage::app()->getStore()->isAdmin()){
 
            
            if($this->isNoteRma() && $request->getState() == VES_VendorsRma_Model_Option_State::STATE_OPEN  ){
                $this->_addButton('escalate', array(
                    'label'     => Mage::helper('adminhtml')->__('Escalate'),
                    'onclick'   => 'setLocation(\'' . $this->getEscalateUrl() . '\')',
                ), -1);
            }
            
            $options = Mage::getModel("vendorsrma/type")->getOptions();
            if($request->getType() == $options[0]["value"] && $request->getState() == VES_VendorsRma_Model_Option_State::STATE_OPEN ){
                $this->_addButton('accept_refund.', array(
                    'label'     => Mage::helper('adminhtml')->__('Accept Refund'),
                    'onclick'   => 'setLocation(\'' . $this->getMarkVendorUrl() . '\')',
                ), -1);
            }
            
        
            $this->_removeButton("saveandcontinue");
      
            if($request->getState() == VES_VendorsRma_Model_Option_State::STATE_CLOSED){
                $this->_removeButton("save");
            }
            if($request->getState() != VES_VendorsRma_Model_Option_State::STATE_OPEN){
            
                $this->_removeButton("reset");
                $this->_removeButton("approval");
                $this->_removeButton("confirmship");
                $this->_removeButton("markasresolved");
            }
            
            
        }
        
        if($request->getState() == VES_VendorsRma_Model_Option_State::STATE_BEING && Mage::app()->getStore()->isAdmin()){
            $this->_addButton('markasresolved', array(
                'label'     => Mage::helper('adminhtml')->__('Mark as Resolved'),
                'onclick'   => 'setLocation(\'' . $this->getMarkUrl() . '\')',
            ), -100);
        }
        
        
    }
    
    public function isNoteRma($state){
        $check = false;
        if(!Mage::helper("vendorsrma/config")->allowSendRefund()) return $check;
    
        $note = Mage::getModel("vendorsrma/note")->getCollection()->addFieldToFilter("request_id",$this->getRequestRma()->getId())
        ->addFieldToFilter("type",VES_VendorsRma_Model_Source_Message_Type::TYPE_VENDOR)->getFirstItem();;
    
        if(!$note->getId() && $state != VES_VendorsRma_Model_Option_State::STATE_CLOSED) return true;

        return $check;
    }
    
    public function getEscalateUrl(){
        return $this->getUrl('*/*/escalate', array(
            'id'       => $this->getRequestRma()->getId(),
        ));
    }
    
    public function getConfirmShipUrl(){
            return $this->getUrl('*/*/confirmship', array(
                'id'       => $this->getRequestRma()->getId(),
            ));
    }

    public function getApprovalUrl(){
        return $this->getUrl('*/*/approval', array(
            'id'       => $this->getRequestRma()->getId(),
        ));
    }
    
    public function getMarkVendorUrl(){
        return $this->getUrl('*/*/mark', array(
            'id'       => $this->getRequestRma()->getId(),
        ));
    }
    
    public function getMarkUrl(){
        return $this->getUrl('*/*/mark', array(
            'id'       => $this->getRequestRma()->getId(),
        ));
    }

    public function getBackUrl()
    {
        return $this->getUrl('*/*/all');
    }

    public function getCreateUrl(){
        return $this->getUrl('*/*/new', array(
            '_current'  => true,
            'order_id'       => '{{order_id}}',
            'package_opened'      => '{{package_opened}}'
        ));
    }
    public function getHeaderText()
    {
        if( Mage::registry('request_data') && Mage::registry('request_data')->getId() ){
            return Mage::helper('vendorsrma')->__("RMA # '%s' - '%s'",Mage::registry('request_data')->getData("increment_id"),Mage::getModel("vendorsrma/option_state")->getTitleByKey(Mage::registry('request_data')->getData("state")));
        } else {
            return Mage::helper('vendorsrma')->__('Add New RMA');
        }
    }

    public function getRequestRma(){
        return Mage::registry("current_request");
    }

    public function getOrderRequest(){
        $order = false;
        if (!($order = $this->getRequestRma()->getData("order_incremental_id")) && $this->getRequest()) {
            $order = $this->getRequest()->getParam('order_id', null);
        }
        return $order;
    }
}