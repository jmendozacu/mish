<?php

/**
 * Shipping Total Row Renderer
 *
 */

class VES_VendorsShipping_Block_Checkout_Shipping extends Mage_Tax_Block_Checkout_Shipping
{
    
    public function setQuote($quote){
        $this->_quote = $quote;
    }
    protected $_template = 'ves_vendorsshipping/checkout/shipping.phtml';
}
