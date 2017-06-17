<?php

class VES_VendorsQuote_Block_links extends Mage_Core_Block_Template{
    /**
     * Get Quote session
     * @return VES_VendorsQuote_Model_Session
     */
    protected function _getSession(){
        return Mage::getSingleton('vendorsquote/session');
    }
    
    /**
     * Get all quotes
     * @return Ambigous <Mage_Sales_Model_Quote, unknown>
     */
    public function getQuotes(){
        if(!isset($this->_quotes)){
            $this->_quotes = $this->_getSession()->getQuotes();
        }
        return $this->_quotes;
    }
    
    /**
     * Get quote item count
     */
    public function getItemsCount(){
        $quotes = $this->getQuotes();
        $itemsQty = 0;
        if($quotes && is_array($quotes)) foreach($quotes as $quote){
            $itemsQty += $quote->getItemsQty();
        }
        return $itemsQty;
    }
    /**
     * Add shopping cart link to parent block
     *
     * @return Mage_Checkout_Block_Links
     */
    public function addQuoteLink()
    {
        $parentBlock = $this->getParentBlock();
        if ($parentBlock && Mage::helper('core')->isModuleOutputEnabled('VES_VendorsQuote')) {
            $count = $this->getItemsCount();
            if ($count == 1) {
                $text = $this->__('Quote (%s item)', $count);
            } elseif ($count > 0) {
                $text = $this->__('Quote (%s items)', $count);
            } else {
                $text = $this->__('Quote');
            }
    
            $parentBlock->removeLinkByUrl($this->getUrl('vquote/index'));
            $parentBlock->addLink($text, 'vquote/index', $text, true, array(), 50, null, 'class="top-link-quote"');
        }
        return $this;
    }
}