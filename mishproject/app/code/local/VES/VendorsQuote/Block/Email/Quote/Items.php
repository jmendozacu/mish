<?php

class VES_VendorsQuote_Block_Email_Quote_Items extends Mage_Core_Block_Template{   
    protected $_itemRenders = array();
    
    /**
     * Get customer session
     * @return Mage_Customer_Model_Session
     */
    public function getCustomerSession(){
        return Mage::getSingleton('customer/session');
    }
    
    /**
     * Add renderer for item product type
     *
     * @param   string $productType
     * @param   string $blockType
     * @param   string $template
     * @return  Mage_Checkout_Block_Cart_Abstract
     */
    public function addItemRender($productType, $blockType, $template)
    {
        $this->_itemRenders[$productType] = array(
            'block' => $blockType,
            'template' => $template,
            'blockInstance' => null
        );
        return $this;
    }
    
    /**
     * Get renderer information by product type code
     *
     * @deprecated please use getItemRendererInfo() method instead
     * @see getItemRendererInfo()
     * @param   string $type
     * @return  array
     */
    public function getItemRender($type)
    {
        return $this->getItemRendererInfo($type);
    }
    
    /**
     * Get renderer information by product type code
     *
     * @param   string $type
     * @return  array
     */
    public function getItemRendererInfo($type)
    {
        if (isset($this->_itemRenders[$type])) {
            return $this->_itemRenders[$type];
        }
        return $this->_itemRenders['default'];
    }
    
    /**
     * Get renderer block instance by product type code
     *
     * @param   string $type
     * @return  array
     */
    public function getItemRenderer($type)
    {
        if (!isset($this->_itemRenders[$type])) {
            $type = 'default';
        }
        if (is_null($this->_itemRenders[$type]['blockInstance'])) {
            $this->_itemRenders[$type]['blockInstance'] = $this->getLayout()
            ->createBlock($this->_itemRenders[$type]['block'])
            ->setTemplate($this->_itemRenders[$type]['template'])
            ->setRenderedBlock($this);
        }
    
        return $this->_itemRenders[$type]['blockInstance'];
    }
    
    /**
     * Get item row html
     *
     * @param   VES_VendorsQuote_Model_Quote_Item $item
     * @return  string
     */
    public function getItemHtml(VES_VendorsQuote_Model_Quote_Item $item)
    {
        $renderer = $this->getItemRenderer($item->getProductType())->setItem($item)->setQuote($this->getQuote());
        return $renderer->toHtml();
    }
}