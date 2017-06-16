<?php

class VES_VendorsPaypal_Model_Api_Standard extends Mage_Paypal_Model_Api_Standard
{
    protected $_orders;
    protected $_lineItemExportItemsFormat = array(
        'id'     => 'item_number_%d',
        'name'   => 'item_name_%d',
        'qty'    => 'quantity_%d',
        'amount' => 'amount_%d',
    );
    
    /**
     * Line items export mapping settings
     * @var array
     */
    protected $_lineItemTotalExportMap = array(
        'base_grand_total'      => 'amount',
        'base_discount_amount'  => 'discount_amount',
        'base_tax_amount'       => 'tax',
        'base_shipping_amount'  => 'shipping',
    );
    
    /**
     * Set orders
     * @param array $orders
     */
    public function setOrders(array $orders){
        $this->_orders = $orders;
        return $this;
    }
    /**
     * Add shipping total as a line item.
     * For some reason PayPal ignores shipping total variables exactly when line items is enabled
     * Note that $i = 1
     *
     * @param array $request
     * @param int $i
     * @return true|null
     */
    protected function _exportLineItems(array &$request, $i = 1)
    {
        if (!$this->_cart) {
            return;
        }
        if ($this->getIsLineItemsEnabled()) {
            $this->_cart->isShippingAsItem(true);
        }
        // always add cart totals, even if line items are not requested
        if ($this->_lineItemTotalExportMap) {
            foreach($this->_lineItemTotalExportMap as $key=>$paypalKey){
                foreach ($this->_orders as $order){
                    $request[$paypalKey] += $this->_filterAmount($order->getData($key));
                }
            }
        }

        // add cart line items
        if (empty($this->_orders) || !$this->getIsLineItemsEnabled()) {
            return;
        }
        $result = null;
        $i = 1;
        foreach ($this->_orders as $order) {
            $result = true;
            $request[sprintf('item_number_%d', $i)] = $order->getIncrementId();
            $request[sprintf('item_name_%d', $i)]   = Mage::helper('vendorspaypal')->__('Payment for order %s',$order->getIncrementId());;
            $request[sprintf('quantity_%d', $i)]    = 1;
            $request[sprintf('amount_%d', $i)]      = $this->_filterAmount($order->getBaseGrandTotal());
            $i++;
        }
        return $result;
    }
    
    /**
     * Generate PayPal Standard checkout request fields
     * Depending on whether there are cart line items set, will aggregate everything or display items specifically
     * Shipping amount in cart line items is implemented as a separate "fake" line item
     */
    public function getStandardCheckoutRequest()
    {
        $request = $this->_exportToRequest($this->_commonRequestFields);
        $request['charset'] = 'utf-8';
    
        $isLineItems = $this->_exportLineItems($request);
        
        if ($isLineItems) {
            $request = array_merge($request, array(
                'cmd'    => '_cart',
                'upload' => 1,
            ));
            if (isset($request['tax'])) {
                $request['tax_cart'] = $request['tax'];
            }
            if (isset($request['discount_amount'])) {
                $request['discount_amount_cart'] = $request['discount_amount'];
            }
        } else {
            $request = array_merge($request, array(
                'cmd'           => '_ext-enter',
                'redirect_cmd'  => '_xclick',
            ));
        }
    
        // payer address
        $this->_importAddress($request);
        $this->_debug(array('request' => $request)); // TODO: this is not supposed to be called in getter
        return $request;
    }
}