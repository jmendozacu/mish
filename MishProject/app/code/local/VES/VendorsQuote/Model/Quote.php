<?php

class VES_VendorsQuote_Model_Quote extends Mage_Core_Model_Abstract
{
    const STATUS_CREATED        = 10; /*When the quote is created*/
    const STATUS_CREATED_NOT_SENT   = 11; /*When vendor create new quote.*/
    const STATUS_PROCESSING     = 20; /*When the customer submit the quote request*/
    const STATUS_HOLD           = 30; /*Quote is currently on hold*/
    const STATUS_CANCELLED      = 40;
    const STATUS_SENT           = 50; /*When vendor send quote back to customer.*/
    const STATUS_ACCEPTED       = 60;
    const STATUS_REJECTED       = 70;
    const STATUS_EXPIRED        = 80;
    const STATUS_ORDERED        = 90;
    
	/**
	 * Customer object
	 * @var Mage_Customer_Model_Customer 
	 */
    protected $_customer;
	
    /**
     * Quote store
     * @var Mage_Core_Model_Store
     */
	protected $_store;
	
	protected $_vendor;
	
	
	public function _construct()
    {
        parent::_construct();
        $this->_init('vendorsquote/quote');
    }
    
    /**
     * Save related items
     *
     * @return Mage_Sales_Model_Quote
     */
    protected function _afterSave()
    {
        parent::_afterSave();

        if (null !== $this->_items) {
            $this->getItemsCollection()->save();
        }
    
        return $this;
    }
    /**
     * Get reserved order id
     *
     * @return string
     */
    public function getReservedQuoteId(){
        $prefix     = Mage::helper('vendorsquote')->getQuotePrefix($this->getStoreId());
        $currentNum = Mage::helper('vendorsquote')->getCurrentNumber($this->getStoreId());
        $increment  = Mage::helper('vendorsquote')->getIncrementNumber($this->getStoreId());
        $padLength  = Mage::helper('vendorsquote')->getPadLength($this->getStoreId());
        
        $result     = $prefix;
        for($i = 0; $i < ($padLength - strlen($currentNum.'')); $i ++){
            $result .="0";
        }
        
        $result .= $currentNum;
        $currentNum +=$increment;
        $resource = Mage::getModel('core/config')->getResourceModel();
        $resource->saveConfig('vendors/quote/config_quote_current_number', $currentNum, 'default', 0);
		Mage::app()->getStore()->resetConfig();
        return $result;
    }
    /**
     * Processing object before save data
     *
     * @return VES_VendorsQuote_Model_Quote
     */
    protected function _beforeSave()
    {
        if($this->isObjectNew() || !$this->getId()){
            /*Set quote increment id.*/
            $this->setData('increment_id',$this->getReservedQuoteId());
            $this->setData('created_at',Mage::getModel('core/date')->date());
            $this->setData('updated_at',Mage::getModel('core/date')->date());
            
            if(!$this->getExpiryDate() || !$this->getReminderDate()){
                $helper         = Mage::helper('vendorsquote');
                $expirationTime = $helper->getExpirationTime();
                $reminderTime   = $helper->getReminderTime();
                $now            = date('Y-m-d');
                if(!$this->getExpiryDate()) $this->setData('expiry_date',date('Y-m-d',strtotime($now." +".$expirationTime." days")));
                if($reminderTime && !$this->getReminderDate()) $this->setData('reminder_date',date('Y-m-d',strtotime($now." +".$reminderTime." days")));
            }
            if(!$this->getStatus()) $this->setData('status',self::STATUS_CREATED);
        }
        return parent::_beforeSave();
    }
    
    
    /**
     * Set customer
     * @param Mage_Customer_model_Customer $customer
     */
    public function setCustomer(Mage_Customer_model_Customer $customer){
        $this->_customer = $customer;
        $this->setData('customer_id',$customer->getId());
        $this->setData('firstname',$customer->getFirstname());
        $this->setData('lastname',$customer->getLastname());
        $this->setData('email',$customer->getEmail());
        return $this;
    }
    
    /**
     * Get customer object
     * @return null, Mage_Customer_Model_Customer
     */
    public function getCustomer(){
        if(!$this->_customer && $this->getData('customer_id')){
            $this->_customer = Mage::getModel('customer/customer')->load($this->getData('customer_id'));
        }
        
        return $this->_customer;
    }
   
    
    /**
     * Get vendor
     * @return VES_Vendors_Model_vendor
     */
    public function getVendor(){
        if(!isset($this->_vendor)){
            $this->_vendor = Mage::getModel('vendors/vendor')->load($this->getVendorId());
        }
        
        return $this->_vendor;
    }
    /**
     * Set store
     * @param Mage_Core_Model_Store $store
     */
    public function setStore(Mage_Core_Model_Store $store){
        $this->_store = $store;
        $this->setData('store_id',$store->getId());
    }
    
    /**
     * Get store
     * @return Mage_Core_Model_Store
     */
    public function getStore(){
        if(!$this->_store){
            $this->_store = Mage::app()->getStore($this->getStoreId());
        }
        return $this->_store;
    }
    
    /**
     * Get Customer Name
     * @return string
     */
    
    public function getCustomerName(){
        return $this->getFirstname().' '.$this->getLastname();
    }
    
    /**
     * Get formated quote created date in store timezone
     *
     * @param   string $format date format type (short|medium|long|full)
     * @return  string
     */
    public function getCreatedAtFormated($format)
    {
        return Mage::helper('core')->formatDate($this->getCreatedAtStoreDate(), $format, true);
    }
    
    /**
     * Get object created at date affected with object store timezone
     *
     * @return Zend_Date
     */
    public function getCreatedAtStoreDate()
    {
        return Mage::app()->getLocale()->storeDate(
            $this->getStore(),
            Varien_Date::toTimestamp($this->getCreatedAt()),
            true
        );
    }
    
    /**
     * Get status label
     * @return string
     */
    public function getStatusLabel(){
        $options = Mage::getModel('vendorsquote/source_status')->getOptionArray();
        return isset($options[$this->getStatus()])?$options[$this->getStatus()]:'';
    }
    
    /**
     * Retrieve quote items collection
     *
     * @return  Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function getItemsCollection()
    {
        if (is_null($this->_items)) {
            $this->_items = Mage::getModel('vendorsquote/quote_item')->getCollection();
            $this->_items->setQuote($this);
        }
        return $this->_items;
    }
    
    /**
     * Retrieve quote items array
     *
     * @return array
     */
    public function getAllItems()
    {
        return $this->getItemsCollection();
    }
    
    /**
     * Get all quote messages
     * @return VES_VendorsQuote_Model_Resource_Quote_Message_Collection
     */
    public function getMessages(){
        $collection = Mage::getModel('vendorsquote/quote_message')->getCollection()
            ->setQuote($this)
            ->addOrder('message_id','DESC')
        ;
        return $collection;        
    }
    /**
     * Retrieve item model object by item identifier
     *
     * @param   int $itemId
     * @return  Mage_Sales_Model_Quote_Item
     */
    public function getItemById($itemId)
    {
        return $this->getItemsCollection()->getItemById($itemId);
    }
    
    /**
     * Retrieve quote item by product id
     *
     * @param   Mage_Catalog_Model_Product $product
     * @return  VES_VendorsQuote_Model_Quote_Item || false
     */
    public function getItemByProduct($product,$request)
    {
        foreach ($this->getAllItems() as $item) {
            if ($item->representProduct($product,$request)) {
                return $item;
            }
        }
        return false;
    }
    
    
    /**
     * Add product to quote
     * @param Mage_Catalog_Model_Product $product
     * @param string $request
     */
    public function addProduct(Mage_Catalog_Model_Product $product, $request = null){
        /*check if the product is already added to quote*/
        $qty = $request->getQty()?$request->getQty():1;
        if($item = $this->getItemByProduct($product,$request)){
            /*If the product is already added to quote just increase the qty of the exist item*/
            $item->updateRequestedQty($item->getRequestedQty()+$qty)->save();
            
        }else{
            /*if the product is not exist just add new item*/
            $itemData = array(
                'product_id'    => $product->getId(),
                'sku'           => $product->getSku(),
                'name'          => $product->getName(),
                'buy_request'   => json_encode($request->getData()),
            );
            
            $item = Mage::getModel('vendorsquote/quote_item')
                ->setData($itemData)
                ->setQuote($this)
                ->save();
            $item->addProposal($qty);
            $item->calculatePrice()->save();
        }
    }
    
    /**
     * Load quote by system increment identifier
     *
     * @param string $incrementId
     * @return VES_VendorsQuote_Model_Quote
     */
    public function loadByIncrementId($incrementId)
    {
        return $this->loadByAttribute('increment_id', $incrementId);
    }

    /**
     * Load quote by custom attribute value. Attribute value should be unique
     *
     * @param string $attribute
     * @param string $value
     * @return VES_VendorsQuote_Model_Quote
     */
    public function loadByAttribute($attribute, $value)
    {
        $this->load($value, $attribute);
        return $this;
    }
    
    /**
     * Loading quote data by customer
     *
     * @return Mage_Sales_Model_Quote
     */
    public function loadQuoteByCustomer($customer, $vendorId)
    {
        if ($customer instanceof Mage_Customer_Model_Customer) {
            $customerId = $customer->getId();
        }
        else {
            $customerId = (int) $customer;
        }
        $this->_getResource()->loadQuoteByCustomerId($this, $customerId,$vendorId);
        $this->_afterLoad();
        return $this;
    }
    
    /**
     * Get quote items count
     */
    public function getItemsCount(){
        return $this->getItemsCollection()->count();
    }
    
    /**
     * Get total items qty.
     * @return number
     */
    public function getItemsQty(){
        $qty = 0;
        foreach($this->getAllItems() as $item){
            $qty += $item->getRequestedQty();
        }
        
        return $qty;
    }
    
    /**
     * Collect totals
     * @return VES_VendorsQuote_Model_Quote
     */
    public function collectTotals(){
        foreach($this->getItemsCollection() as $item){
            $item->calculatePrice();
        }
        return $this;
    }
    
    /**
     * Merge other quote to this quote
     * @param VES_VendorsQuote_Model_Quote $quote
     */
    public function merge(VES_VendorsQuote_Model_Quote $quote){
        foreach ($quote->getAllItems() as $item) {
            $found = false;
            foreach ($this->getAllItems() as $quoteItem) {
                if ($quoteItem->compare($item)) {
                    $quoteItem->updateRequestedQty($quoteItem->getRequestedQty() + $item->getRequestedQty())->save();
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $item->setQuote($this)->save();
            }
        }
        return $this;
    }
    
    /**
     * Set the quote to be on hold.
     * @return VES_VendorsQuote_Model_Quote
     */
    public function hold(){
        $this->setStatusBeforeHold($this->getStatus());
        $this->setStatus(self::STATUS_HOLD);
        $this->save();
        return $this;
    }
    /**
     * Unhold the quote
     * @return VES_VendorsQuote_Model_Quote
     */
    public function unhold(){
        $this->setStatus($this->getStatusBeforeHold());
        $this->save();
        return $this;
    }
    
    /**
     * Cancel the quote
     * @return VES_VendorsQuote_Model_Quote
     */
    public function cancel(){
        $this->setStatus(self::STATUS_CANCELLED);
        $this->save();
        return $this;
    }
    
    /**
     * reject the quote
     * @return VES_VendorsQuote_Model_Quote
     */
    public function reject(){
        $this->setStatus(self::STATUS_REJECTED);
        $this->save();
        return $this;
    }
    
    /**
     * Submit the quote to customer
     * @return VES_VendorsQuote_Model_Quote
     */
    public function submit(){
        $this->setStatus(self::STATUS_SENT);
        $this->save();
        return $this;
    }
    
    /**
     * Add a message for the quote
     * @param string $name
     * @param string $message
     * @param int $messageType
     * @param boolean $isNotifyCustomer
     * @return VES_VendorsQuote_Model_Quote_Message
     */
    public function addMessage($name, $message,$messageType, $isNotifyCustomer=false){
        $messageObject = Mage::getModel('vendorsquote/quote_message');
        $messageObject->setData(array(
            'name'      => $name,
            'message'   => $message,
            'message_type'  => $messageType,
            'customer_notify' => $isNotifyCustomer
        ))->setQuote($this);
        $messageObject->save();
        return $messageObject;
    }
}