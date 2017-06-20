<?php

class VES_VendorsQuote_Block_Vendor_Quote_View_Items extends Mage_Adminhtml_Block_Template
{
    protected $_isReadOnly;
    
    protected $_canEditItems;
    /**
     * Renderers with render type key
     * block    => the block name
     * template => the template file
     * renderer => the block object
     *
     * @var array
     */
    protected $_itemRenders = array();
    
    /**
     * Renderers for other column with column name key
     * block    => the block name
     * template => the template file
     * renderer => the block object
     *
     * @var array
    */
    protected $_columnRenders = array();
    
    /**
     * Init block
     *
     */
    protected function _construct()
    {
        $this->addColumnRender('qty', 'adminhtml/sales_items_column_qty', 'sales/items/column/qty.phtml');
        $this->addColumnRender('name', 'adminhtml/sales_items_column_name', 'sales/items/column/name.phtml');
        parent::_construct();
    }
    /**
     * Add item renderer
     *
     * @param string $type
     * @param string $block
     * @param string $template
     * @return Mage_Adminhtml_Block_Sales_Items_Abstract
     */
    public function addItemRender($type, $block, $template)
    {
        $this->_itemRenders[$type] = array(
            'block'     => $block,
            'template'  => $template,
            'renderer'  => null
        );
        return $this;
    }
    
    /**
     * Add column renderer
     *
     * @param string $column
     * @param string $block
     * @param string $template
     * @return Mage_Adminhtml_Block_Sales_Items_Abstract
     */
    public function addColumnRender($column, $block, $template, $type=null)
    {
        if (!is_null($type)) {
            $column .= '_' . $type;
        }
        $this->_columnRenders[$column] = array(
            'block'     => $block,
            'template'  => $template,
            'renderer'  => null
        );
        return $this;
    }
    
    /**
     * Retrieve item renderer block
     *
     * @param string $type
     * @return Mage_Core_Block_Abstract
     */
    public function getItemRenderer($type)
    {
        if (!isset($this->_itemRenders[$type])) {
            $type = 'default';
        }
        if (is_null($this->_itemRenders[$type]['renderer'])) {
            $this->_itemRenders[$type]['renderer'] = $this->getLayout()
            ->createBlock($this->_itemRenders[$type]['block'])
            ->setTemplate($this->_itemRenders[$type]['template']);
            foreach ($this->_columnRenders as $columnType=>$renderer) {
                $this->_itemRenders[$type]['renderer']->addColumnRender($columnType, $renderer['block'], $renderer['template']);
            }
        }
        return $this->_itemRenders[$type]['renderer'];
    }
    
    /**
     * Retrieve column renderer block
     *
     * @param string $column
     * @param string $compositePart
     * @return Mage_Core_Block_Abstract
     */
    public function getColumnRenderer($column, $compositePart='')
    {
        if (isset($this->_columnRenders[$column . '_' . $compositePart])) {
            $column .= '_' . $compositePart;
        }
        if (!isset($this->_columnRenders[$column])) {
            return false;
        }
        if (is_null($this->_columnRenders[$column]['renderer'])) {
            $this->_columnRenders[$column]['renderer'] = $this->getLayout()
            ->createBlock($this->_columnRenders[$column]['block'])
            ->setTemplate($this->_columnRenders[$column]['template'])
            ->setRenderedBlock($this);
        }
        return $this->_columnRenders[$column]['renderer'];
    }
    
    /**
     * Retrieve rendered item html content
     *
     * @param Varien_Object $item
     * @return string
     */
    public function getItemHtml(Varien_Object $item)
    {
        $type = $item->getProduct()->getTypeId();
    
        return $this->getItemRenderer($type)
        ->setItem($item)
        ->toHtml();
    }
    
    
    /**
     * Retrieve rendered column html content
     *
     * @param Varien_Object $item
     * @param string $column the column key
     * @param string $field the custom item field
     * @return string
     */
    public function getColumnHtml(Varien_Object $item, $column, $field = null)
    {
        $block = $this->getColumnRenderer($column, $item->getProduct()->getTypeId());
        if ($block) {
            $block->setItem($item);
            if (!is_null($field)) {
                $block->setField($field);
            }
            return $block->toHtml();
        }
        return '&nbsp;';
    }
    
    /**
     * Retrieve available quote
     *
     * @return VES_VendorsQuote_Model_Quote
     */
    public function getQuote()
    {
        if ($this->hasQuote()) {
            return $this->getData('quote');
        }
        if (Mage::registry('current_quote')) {
            return Mage::registry('current_quote');
        }
        if (Mage::registry('quote')) {
            return Mage::registry('quote');
        }
        if ($this->getItem()->getQuote())
        {
            return $this->getItem()->getQuote();
        }
        Mage::throwException(Mage::helper('vendorsquote')->__('Cannot get quote instance'));
    }
    
    /**
     * Check if the quote is read only (when quote is on hold)
     * @return boolean
     */
    public function isReadOnly(){
        if(!isset($this->_isReadOnly)){
            $this->_isReadOnly = $this->getQuote()->getStatus() == VES_VendorsQuote_Model_Quote::STATUS_HOLD;
        }
        return $this->_isReadOnly;
    }
    
    /**
     * Retrieve order items collection
     *
     * @return unknown
     */
    public function getItemsCollection()
    {
        return $this->getQuote()->getItemsCollection();
    }
    
    /**
     * Retrieve current order currency code
     *
     * @return string
     */
    public function getCurrentCurrencyCode()
    {
        return Mage::app()->getStore()->getCurrentCurrencyCode();
    }
    /**
     * Retrieve curency name by code
     *
     * @param   string $code
     * @return  string
     */
    public function getCurrencySymbol($code)
    {
        $currency = Mage::app()->getLocale()->currency($code);
        return $currency->getSymbol() ? $currency->getSymbol() : $currency->getShortName();
    }
    
    /**
     * Data result config
     * @return string
     */
    public function getDataJSON(){
        $result = array(
            'currency_symbol'   => $this->getCurrencySymbol($this->getCurrentCurrencyCode()),
            'load_base_url'       => $this->getUrl('*/*/addProduct',array('quote'=>$this->getQuote()->getId())),
        );
        return json_encode($result);
    }
    
    /**
     * Set value for can edit item flag
     * @param boolean $value
     */
    public function setCanEditItems($value){
        $this->_canEditItems = $value;
    }
    /**
     * Can edit quote items
     */
    public function canEditItems(){
        if(!isset($this->_canEditItems)){
            $this->_canEditItems = in_array($this->getQuote()->getStatus(), array(
                VES_VendorsQuote_Model_Quote::STATUS_PROCESSING,
                VES_VendorsQuote_Model_Quote::STATUS_CREATED_NOT_SENT,
                VES_VendorsQuote_Model_Quote::STATUS_CREATED,
            ));
        }
        return $this->_canEditItems;
    }
    
    /**
     * Get Save Proposal URL
     * @return string
     */
    public function getSaveProposalUrl(){
        return $this->getUrl('*/*/saveProposal',array('_secure'=>true));
    }
    /**
     * Get save all proposal url
     */
    public function getSaveAllProposalUrl(){
        return $this->getUrl('*/*/saveAllProposal',array('_secure'=>true));
    }
    /**
     * Get save default proposal URL
     * @return string
     */
    public function getSaveDefaultProposalUrl(){
        return $this->getUrl('*/*/saveDefaultProposal',array('_secure'=>true));
    }
    /**
     * Get remove proposal URL
     * @return string
     */
    public function getRemoveProposalUrl(){
        return $this->getUrl('*/*/removeProposal',array('_secure'=>true));
    }
    
    /**
     * Remove quote item url
     * @return string
     */
    public function getRemoveQuoteItemUrl(){
        return $this->getUrl('*/*/removeQuoteItem',array('_secure'=>true));
    }
    
    /**
     * send quote message url
     * @return string
     */
    public function getSendQuoteMessageUrl(){
        return $this->getUrl('*/*/sendQuoteMessage',array('_secure'=>true));
    }
}