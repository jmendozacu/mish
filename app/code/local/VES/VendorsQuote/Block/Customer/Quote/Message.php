<?php

class VES_VendorsQuote_Block_Customer_Quote_Message extends Mage_Adminhtml_Block_Widget
{
    /**
     * Get current quote
     * @return VES_VendorsQuote_Model_Quote
     */
    public function getQuote(){
        return Mage::registry('quote');
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
     * Get message css class
     * @param VES_VendorsQuote_Model_Quote_Message $message
     * @return string
     */
    public function getMessageClass(VES_VendorsQuote_Model_Quote_Message $message){
        return ($message->getMessageType() == VES_VendorsQuote_Model_Quote_Message::TYPE_VENDOR)?'vendor-message':'customer-message';
    }
}