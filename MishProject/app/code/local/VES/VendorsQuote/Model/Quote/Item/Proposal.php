<?php

class VES_VendorsQuote_Model_Quote_Item_Proposal extends Mage_Core_Model_Abstract
{
    protected $_item;
    
    public function _construct()
    {
        parent::_construct();
        $this->_init('vendorsquote/quote_item_proposal');
    }
    
    /**
     * Set Item
     * @param VES_VendorsQuote_Model_Quote_Item $item
     */
    public function setItem(VES_VendorsQuote_Model_Quote_Item $item){
        $this->_item = $item;
        $this->setItemId($item->getId());
        return $this;
    }

    /**
     * get item
     * @return VES_VendorsQuote_Model_Quote_Item
     */
    public function getItem(){
        if(!isset($this->_item)){
            $this->_item = Mage::getModel('vendorsquote/quote_item')->load($this->getItemId());
        }
        return $this->_item;
    }
}