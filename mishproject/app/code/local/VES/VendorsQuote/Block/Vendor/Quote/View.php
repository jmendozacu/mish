<?php

class VES_VendorsQuote_Block_Vendor_Quote_View extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
        $this->_mode        = 'view';
        $this->_blockGroup  = 'vendorsquote';
        $this->_controller  = 'vendor_quote';
        $this->_objectId    = 'quote_id';
        
        $this->_updateButton('save', 'label', Mage::helper('vendors')->__('Save Quote'));
        $this->_removeButton('reset');
        $quote = Mage::registry('quote');
        
        if($quote->getStatus() != VES_VendorsQuote_Model_Quote::STATUS_CANCELLED){
            $this->addButton('cancelled', array(
                'label'     => Mage::helper('vendorsquote')->__('Cancel Quote'),
                'class'     => 'delete',
                'onclick'   => 'deleteConfirm(\''. Mage::helper('adminhtml')->__('Are you sure you want to do this?')
                    .'\', \'' . $this->getCancelUrl() . '\')',
            ));
        }
        
        if(!in_array($quote->getStatus(),array(
                VES_VendorsQuote_Model_Quote::STATUS_HOLD,
                VES_VendorsQuote_Model_Quote::STATUS_ORDERED,
                VES_VendorsQuote_Model_Quote::STATUS_CANCELLED,
            )
        )){
            $this->addButton('hold', array(
                'label'     => Mage::helper('vendorsquote')->__('Hold'),
                'class'     => 'hold',
                'onclick'   => 'setLocation(\'' . $this->getHoldUrl() . '\')',
            ));
        }elseif($quote->getStatus() == VES_VendorsQuote_Model_Quote::STATUS_HOLD){
            $this->addButton('unhold', array(
                'label'     => Mage::helper('vendorsquote')->__('Unhold'),
                'class'     => 'unhold',
                'onclick'   => 'setLocation(\'' . $this->getUnholdUrl() . '\')',
            ));
        }
        if(in_array($quote->getStatus(), array(
            VES_VendorsQuote_Model_Quote::STATUS_PROCESSING,
            VES_VendorsQuote_Model_Quote::STATUS_CREATED_NOT_SENT
        ))){
            $this->_addButton('submit', array(
                'label'     => Mage::helper('vendorsquote')->__('Submit Proposal'),
                'onclick'   => 'submitProposal();',
                'class'     => 'save-and-continue',
            ), 10);
        }
        
        $this->_formScripts[] = "        
        function submitProposal(){
            editForm.submit($('edit_form').action+'submit_proposal/1/');
        }
        ";
    }

    public function getHeaderText()
    {
        $quote = Mage::registry('quote');
        $date = Mage::app()->getLocale()->date(strtotime($quote->getCreatedAt()))->toString(Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_FULL));
        return Mage::helper('vendorsquote')->__("Quote Request #%s | %s", $quote->getIncrementId(),$date);
    }
    
    /**
     * Get hold URL
     * @return string
     */
    public function getHoldUrl(){
        return $this->getUrl('*/*/hold',array($this->_objectId => $this->getRequest()->getParam($this->_objectId)));
    }
    
    /**
     * Get Unhold Url
     * @return string
     */
    public function getUnholdUrl(){
        return $this->getUrl('*/*/unhold',array($this->_objectId => $this->getRequest()->getParam($this->_objectId)));
    }
    
    /**
     * Get Unhold Url
     * @return string
     */
    public function getCancelUrl(){
        return $this->getUrl('*/*/cancel',array($this->_objectId => $this->getRequest()->getParam($this->_objectId)));
    }
    
}