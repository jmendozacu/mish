<?php

class VES_VendorsQuote_Block_Vendor_Quote_View_Tab_Info extends Mage_Adminhtml_Block_Widget
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * ######################## TAB settings #################################
     */
    public function getTabLabel(){
        return Mage::helper('vendorsquote')->__('Quote Request');
    }
    
    public function getTabTitle(){
        return Mage::helper('vendorsquote')->__('Quote Request');
    }
    
    public function canShowTab(){
        return true;
    }
    
    public function isHidden(){
        return false;
    }
    
    protected function _prepareLayout(){
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
        parent::_prepareLayout();
    }
    /**
     * Get current quote
     * @return VES_VendorsQuote_Model_Quote
     */
    public function getQuote(){
        return Mage::registry('quote');
    }
    
    /**
     * Get all quote status
     */
    public function getAllQuoteStatus(){
        return Mage::getModel('vendorsquote/source_status')->getOptionArray(true);
    }
    
    /**
     * Format the comment
     * @param string $comment
     */
    public function formatComment($comment){
        $comment = strip_tags($comment);
        $comment = str_replace("\n", "<br />", $comment);
        return $comment;
    }
    
    /**
     * Check if the quote is read only (when quote is on hold)
     * @return boolean
     */
    public function isReadOnly(){
        return $this->getQuote()->getStatus() == VES_VendorsQuote_Model_Quote::STATUS_HOLD;
    }
    
    /**
     * Get show field config
     */
    function showField($field){
        return Mage::helper('vendorsquote')->getConfig($field);
    }
    
    /**
     * Check if a field is required based on showing configuration.
     * @param int $value
     * @return boolean
     */
    public function isRequiredField($value){
        return $value == 2;
    }
}