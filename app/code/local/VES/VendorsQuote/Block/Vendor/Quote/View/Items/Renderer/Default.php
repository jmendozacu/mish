<?php

class VES_VendorsQuote_Block_Vendor_Quote_View_Items_Renderer_Default extends VES_VendorsQuote_Block_Vendor_Quote_View_Items
{
    public function getItem()
    {
        return $this->_getData('item');
    }
    
    /**
     * Retrieve real html id for field
     *
     * @param string $name
     * @return string
     */
    public function getFieldId($id)
    {
        return $this->getFieldIdPrefix() . $id;
    }
    
    /**
     * Retrieve field html id prefix
     *
     * @return string
     */
    public function getFieldIdPrefix()
    {
        return 'quote_item_' . $this->getItem()->getId() . '_';
    }
    
    /**
     * Indicate that block can display container
     *
     * @return boolean
     */
    public function canDisplayContainer()
    {
        return $this->getRequest()->getParam('reload') != 1;
    }
    
    /**
     * Get the cost of products
     * @return Ambigous <unknown, string, string, multitype:>
     */
    public function getCost(){
        $cost = $this->getItem()->getProduct()->getCost();
        return $cost?$cost:Mage::helper('vendorsquote')->__('N/A');
    }
}