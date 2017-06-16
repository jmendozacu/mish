<?php

class VES_VendorsQuote_Block_Quote extends Mage_Core_Block_Template{
    protected $_quotes;
    protected $_itemRenders = array();
    
    /**
     * Get Quote session
     * @return VES_VendorsQuote_Model_Session
     */
    protected function _getSession(){
        return Mage::getSingleton('vendorsquote/session');
    }
    
    
    /**
     * Choose template file
     */
    public function chooseTemplate()
    {
        $itemsCount = $this->getItemsCount() ? $this->getItemsCount() : 0;
        if ($itemsCount) {
            $this->setTemplate($this->getQuoteTemplate());
        } else {
            $this->setTemplate($this->getEmptyTemplate());
        }
    }
    
    /**
	 * Get quote item count
	 * @return int
	 */
	public function getItemsCount(){
		$quotes = $this->getQuotes();
		$itemCount = 0;
		foreach($quotes as $quote){
			$itemCount += $quote->getItemsCount();
		}
		return $itemCount;
	}
	
	/**
	 * Get all quotes
	 * @return array
	 */
	public function getQuotes(){
	    if(!isset($this->_quotes)){
	        $this->_quotes = $this->_getSession()->getQuotes();
	    }
		return $this->_quotes;
	}
	
	
	/**
	 * Add renderer for item product type
	 *
	 * @param   string $productType
	 * @param   string $blockType
	 * @param   string $template
	 * @return  VES_VendorsQuote_Block_Quote
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
	    $renderer = $this->getItemRenderer($item->getProductType())->setItem($item);
	    return $renderer->toHtml();
	}
	
	/**
	 * Get continue shopping url
	 * @return string $url
	 */
	public function getContinueShoppingUrl()
	{
	    $url = $this->getData('continue_shopping_url');
	    if (is_null($url)) {
	        $url = Mage::getSingleton('checkout/session')->getContinueShoppingUrl(true);
	        if (!$url) {
	            $url = Mage::getUrl();
	        }
	        $this->setData('continue_shopping_url', $url);
	    }
	    return $url;
	}
	
	/**
	 * Get submit quote url
	 * @return string
	 */
	public function getQuoteRequestUrl($quote){
	    return $this->getUrl('vquote/index/sendRequest',array('quote_id'=>$quote->getId()));
	}
	
	/**
	 * Can show telephone field
	 * @return boolean
	 */
	public function showTelephone(){
	    return Mage::helper('vendorsquote')->getConfig('account_detail_telephone');
	}
	
	/**
	 * Can show company field
	 * @return boolean
	 */
	public function showCompany(){
	    return Mage::helper('vendorsquote')->getConfig('account_detail_company');
	}
	
	/**
	 * Can show TAX Vat field
	 * @return boolean
	 */
	public function showTaxvat(){
	    return Mage::helper('vendorsquote')->getConfig('account_detail_taxvat');
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