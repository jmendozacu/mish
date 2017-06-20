<?php

class VES_VendorsQuote_Helper_Data extends Mage_Core_Helper_Abstract
{
    const DISABLED_NOTIFICATION_EMAIL   = 'disabled';
    const XML_PATH_NEW_REQUEST_EMAIL_TEMPLATE               = 'vendors/quote/email_template_new_request';
    const XML_PATH_NEW_PROPOSAL_EMAIL_TEMPLATE              = 'vendors/quote/email_template_new_proposal';
    const XML_PATH_CANCEL_QUOTE_EMAIL_TEMPLATE              = 'vendors/quote/email_template_request_cancelled';
    const XML_PATH_REJECT_PROPOSAL_EMAIL_TEMPLATE           = 'vendors/quote/email_template_request_rejected';
    const XML_PATH_PROPOSAL_EXPIRED_EMAIL_TEMPLATE          = 'vendors/quote/email_template_proposal_expired';
    const XML_PATH_QUOTE_REMINDER_EMAIL_TEMPLATE            = 'vendors/quote/email_template_proposal_reminder';
    const XML_PATH_CRM_MESSAGE_CUSTOMER_EMAIL_TEMPLATE      = 'vendors/quote/email_template_quote_message';
    const XML_PATH_CRM_MESSAGE_VENDOR_EMAIL_TEMPLATE        = 'vendors/quote/email_template_quote_message_vendor';
    
    const XML_PATH_EMAIL_IDENTITY			    = 'vendors/quote/email_sales_representatives_sender';
    /**
     * product is enabled for quotation
     * @param Mage_Catalog_Model_Product $product
     * @return boolean
     */
	public function isAllowedToQuote(Mage_Catalog_Model_Product $product){
		return $product->getData('ves_enable_quotation');
	}
	
	
	/**
	 * product is enabled for quotation
	 * @param Mage_Catalog_Model_Product $product
	 * @return boolean
	 */
	public function isAllowedToOrder(Mage_Catalog_Model_Product $product){
	    return $product->getData('ves_enable_order');
	}
	
	/**
	 * Is required customer to login to use quotation function
	 * @return boolean
	 */
	public function requireCustomerLogin(){
	    return $this->getConfig('frontend_login_required');
	}
	
	/**
	 * Get add to quote URL for products
	 * @param Mage_Catalog_Model_Product $product
	 * @return string
	 */
	public function getAddToQuoteUrl(Mage_Catalog_Model_Product $product){
	    return Mage::getUrl('vquote/index/additem',array('id'=>$product->getId()));
	}
	
	/**
	 * Get the config of the quotation extension based on config id.
	 * @param string $configId
	 * @return Ambigous <mixed, string, NULL, multitype:, multitype:Ambigous <string, multitype:, NULL> >
	 */
	public function getConfig($configId,$store=null){
	    if($store === null) $store = Mage::app()->getStore()->getId();
	    
	    return Mage::getStoreConfig('vendors/quote/'.$configId,$store);
	}
	
	/**
	 * Get quote prefix
	 * @param mixed $store
	 * @return string
	 */
	public function getQuotePrefix($store){
	    return $this->getConfig('config_quote_prefix',$store);
	}
	
	
	/**
	 * Get the current number of quote increment
	 * @param mixed $store
	 * @return int
	 */
	public function getCurrentNumber($store){
	    return $this->getConfig('config_quote_current_number',$store);
	}
	
	
	/**
	 * Get increment number
	 * @param mixed $store
	 * @return int
	 */
	public function getIncrementNumber($store){
	    return $this->getConfig('config_quote_increment_number',$store);
	}
	
	/**
	 * Get pad length
	 * @param mixed $store
	 * @return int
	 */
	public function getPadLength($store){
	    return $this->getConfig('config_quote_pad_length',$store);
	}
	
	/**
	 * The number of days it takes for a quote proposal to be expired.
	 * @return int
	 */
	public function getExpirationTime(){
	    return $this->getConfig('config_expiration_time');
	}
	
	
	/**
	 * How many days a reminder will be send for the active quotes
	 * @return int
	 */
	public function getReminderTime(){
	    return $this->getConfig('config_reminder_time');
	}
	
	/**
	 * Send corresponding email template
	 *
	 * @param string $emailTemplate configuration path of email template
	 * @param string $emailSender configuration path of email identity
	 * @param array $templateParams
	 * @param int|null $storeId
	 * @param Varien_Object
	 * @return VES_VendorsQuote_Helper_Data
	 */
	protected function _sendEmailTemplate($template, $sender, $templateParams = array(), $storeId = null,Varien_Object $receiver)
	{
	    if(Mage::getStoreConfig($template) == self::DISABLED_NOTIFICATION_EMAIL) return;

	    /** @var $mailer Mage_Core_Model_Email_Template_Mailer */
	    $mailer = Mage::getModel('core/email_template_mailer');
	    $emailInfo = Mage::getModel('core/email_info');
	    $emailInfo->addTo($receiver->getEmail(), $receiver->getName());
	    $mailer->addEmailInfo($emailInfo);
	
	    // Set all required params and send emails
	    $mailer->setSender(Mage::getStoreConfig($sender, $storeId));
	    $mailer->setStoreId($storeId);
	    $mailer->setTemplateId(Mage::getStoreConfig($template, $storeId));
	    $mailer->setTemplateParams($templateParams);
	    $mailer->send();
	    return $this;
	}
	
	/**
	 * Send new quote request notification email to customer.
	 * @param VES_VendorsQuote_Model_Quote $quote
	 * @return VES_VendorsQuote_Helper_Data
	 */
	public function sendNewRequestEmail(VES_VendorsQuote_Model_Quote $quote){
	    $receiver = new Varien_Object(array('email'=>$quote->getEmail(),'name'=>$quote->getCustomerName()));
	    
	    $this->_sendEmailTemplate(self::XML_PATH_NEW_REQUEST_EMAIL_TEMPLATE, self::XML_PATH_EMAIL_IDENTITY,
	        array(
	            'quote'            => $quote,
	            'show_telephone'   => $this->getConfig('account_detail_telephone')>0,
	            'show_company'     => $this->getConfig('account_detail_company')>0,
	            'show_taxvat'      => $this->getConfig('account_detail_taxvat')>0
	        ),null,$receiver);
	    return $this;
	}
	
	/**
	 * Send quote message notification to customer.
	 * @param VES_VendorsQuote_Model_Quote_Message $message
	 * @param VES_VendorsQuote_Model_Quote $quote
	 * @return VES_VendorsQuote_Helper_Data
	 */
	public function sendQuoteNotificationMessage(VES_VendorsQuote_Model_Quote_Message $message, VES_VendorsQuote_Model_Quote $quote){
	    $receiver = new Varien_Object(array('email'=>$quote->getEmail(),'name'=>$quote->getCustomerName()));
	    $this->_sendEmailTemplate(self::XML_PATH_CRM_MESSAGE_CUSTOMER_EMAIL_TEMPLATE, self::XML_PATH_EMAIL_IDENTITY,
	        array(
	            'quote'    => $quote,
	            'message'  => $message,
	            'vendor'   => $quote->getVendor(),
	        ),null,$receiver);
	    return $this;
	}
	
	/**
	 * Send quote message notification to vendor.
	 * @param VES_VendorsQuote_Model_Quote_Message $message
	 * @param VES_VendorsQuote_Model_Quote $quote
	 * @return VES_VendorsQuote_Helper_Data
	 */
	public function sendQuoteNotificationMessageToVendor(VES_VendorsQuote_Model_Quote_Message $message, VES_VendorsQuote_Model_Quote $quote){
	    $vendor = $quote->getVendor();
	    $receiver = new Varien_Object(array('email'=>$vendor->getEmail(),'name'=>$vendor->getName()));
	    $this->_sendEmailTemplate(self::XML_PATH_CRM_MESSAGE_VENDOR_EMAIL_TEMPLATE, self::XML_PATH_EMAIL_IDENTITY,
	        array(
	            'quote'    => $quote,
	            'message'  => $message,
	            'vendor'   => $quote->getVendor(),
	        ),null,$receiver);
	    return $this;
	}
	/**
	 * Send new proposal notification email to customer
	 * @param VES_VendorsQuote_Model_Quote $quote
	 * @return VES_VendorsQuote_Helper_Data
	 */
	public function sendNewProposalNotificationEmailToCustomer(VES_VendorsQuote_Model_Quote $quote){
	    $receiver = new Varien_Object(array('email'=>$quote->getEmail(),'name'=>$quote->getCustomerName()));
	    $this->_sendEmailTemplate(self::XML_PATH_NEW_PROPOSAL_EMAIL_TEMPLATE, self::XML_PATH_EMAIL_IDENTITY,
	        array(
	            'quote'            => $quote,
	            'show_telephone'   => $this->getConfig('account_detail_telephone')>0,
	            'show_company'     => $this->getConfig('account_detail_company')>0,
	            'show_taxvat'      => $this->getConfig('account_detail_taxvat')>0
	        ),null,$receiver);
	    return $this;
	}
	
	/**
	 * Send cancel quote notification email
	 * @param VES_VendorsQuote_Model_Quote $quote
	 * @return VES_VendorsQuote_Helper_Data
	 */
	public function sendCancelQuoteNotificationEmail(VES_VendorsQuote_Model_Quote $quote){
	    $receiver = new Varien_Object(array('email'=>$quote->getEmail(),'name'=>$quote->getCustomerName()));
	    $this->_sendEmailTemplate(self::XML_PATH_CANCEL_QUOTE_EMAIL_TEMPLATE, self::XML_PATH_EMAIL_IDENTITY,
	        array(
	            'quote'            => $quote,
	            'show_telephone'   => $this->getConfig('account_detail_telephone')>0,
	            'show_company'     => $this->getConfig('account_detail_company')>0,
	            'show_taxvat'      => $this->getConfig('account_detail_taxvat')>0
	        ),null,$receiver);
	    return $this;
	}
	
	/**
	 * Send reject proposal notification email
	 * @param VES_VendorsQuote_Model_Quote $quote
	 * @return VES_VendorsQuote_Helper_Data
	 */
	public function sendRejectProposalNotificationEmail(VES_VendorsQuote_Model_Quote $quote){
	    $receiver = new Varien_Object(array('email'=>$quote->getEmail(),'name'=>$quote->getCustomerName()));
	    $this->_sendEmailTemplate(self::XML_PATH_REJECT_PROPOSAL_EMAIL_TEMPLATE, self::XML_PATH_EMAIL_IDENTITY,
	        array(
	            'quote'            => $quote,
	            'show_telephone'   => $this->getConfig('account_detail_telephone')>0,
	            'show_company'     => $this->getConfig('account_detail_company')>0,
	            'show_taxvat'      => $this->getConfig('account_detail_taxvat')>0
	        ),null,$receiver);
	    return $this;
	}
	
	/**
	 * Send expired quote notification email to customer
	 * @param VES_VendorsQuote_Model_Quote $quote
	 * @return VES_VendorsQuote_Helper_Data
	 */
	public function sendExpiredQuoteNotificationEmail(VES_VendorsQuote_Model_Quote $quote){
	    $receiver = new Varien_Object(array('email'=>$quote->getEmail(),'name'=>$quote->getCustomerName()));
	    $this->_sendEmailTemplate(self::XML_PATH_PROPOSAL_EXPIRED_EMAIL_TEMPLATE, self::XML_PATH_EMAIL_IDENTITY,
	        array(
	            'quote'            => $quote,
	            'show_telephone'   => $this->getConfig('account_detail_telephone')>0,
	            'show_company'     => $this->getConfig('account_detail_company')>0,
	            'show_taxvat'      => $this->getConfig('account_detail_taxvat')>0
	        ),null,$receiver);
	    return $this;
	}
	
	/**
	 * Send quote reminder notification email to customer
	 * @param VES_VendorsQuote_Model_Quote $quote
	 * @return VES_VendorsQuote_Helper_Data
	 */
	public function sendQuoteReminderNotificationEmail(VES_VendorsQuote_Model_Quote $quote){
	    $receiver = new Varien_Object(array('email'=>$quote->getEmail(),'name'=>$quote->getCustomerName()));
	    $this->_sendEmailTemplate(self::XML_PATH_QUOTE_REMINDER_EMAIL_TEMPLATE, self::XML_PATH_EMAIL_IDENTITY,
	        array(
	            'quote'            => $quote,
	            'show_telephone'   => $this->getConfig('account_detail_telephone')>0,
	            'show_company'     => $this->getConfig('account_detail_company')>0,
	            'show_taxvat'      => $this->getConfig('account_detail_taxvat')>0
	        ),null,$receiver);
	    return $this;
	}
	
}