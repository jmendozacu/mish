<?php

class VES_VendorsRma_Model_Request extends Mage_Core_Model_Abstract
{
    const ENTITY = 'vendor_rma';
    const XML_PATH_VENDOR_EMAIL_TEMPLATE = 'vendorsrma/email/base_template_vendor';
    const XML_PATH_CUSTOMER_EMAIL_TEMPLATE = 'vendorsrma/email/base_template_customer';
    const XML_PATH_MARK_EMAIL_TEMPLATE = 'vendorsrma/email/base_template_mark';
    
    const XML_PATH_ADMIN_EMAIL_TEMPLATE = 'vendorsrma/email/base_template_admin';
    const XML_PATH_REGISTER_EMAIL_IDENTITY 		= 'vendorsrma/email/sender';

    protected $_eventPrefix = 'vendor_rma';
    protected $_resourceName = "vendorsrma/resoucre_request";

    protected $_templateFilter = null;


    public function _construct()
    {
        $this->_init('vendorsrma/request');
    }


    protected function _afterSave(){
        parent::_afterSave();

        /* save uid */
        if ($this->getIncrementId() && !$this->getData("uid")) {
            $this->setData("uid",md5($this->getIncrementId().time()))->save();
        }
    }

    /* save address customer for each request rma */

    public function saveAddress($address){
        //echo $this->getId();exit;
        if(!$address instanceof VES_VendorsRma_Model_Address)  throw new Mage_Core_Exception(Mage::helper('vendorsrma')->__('Object Address not found !'));
        $address->setData("request_id",$this->getId());
        try{
            $address->save();
        }
        catch (Exception $e) {
            throw new Mage_Core_Exception($e->getMessage());
            return;
        }
        return $this;
    }

    /* save item order for each request rma */

    public function saveItems($items){
        $order = Mage::getModel("sales/order")->loadByIncrementId($this->getData("order_incremental_id"));
        $validate = Mage::helper("vendorsrma")->validateQtyOrder($items,$order,$this->getId());
        
        if($validate != VES_VendorsRma_Helper_Data::QTY_APECPT){
            switch($validate){
                case VES_VendorsRma_Helper_Data::EXEPTION_QTY_ZERO:
                    $mesage = Mage::helper("vendorsrma")->__("Request should have at least one item");
                    break;
                case VES_VendorsRma_Helper_Data::EXEPTION_QTY_MUCH:
                    $mesage = Mage::helper("vendorsrma")->__("Wrong Qty Item");
                    break;
            }
            throw new Mage_Core_Exception($mesage);
        }

        $check_item =  Mage::getModel("vendorsrma/item")->getCollection()->addFieldToFilter("request_id",$this->getId());
        if($check_item->count()){
            foreach($check_item as $item){
                foreach ($items as $id => $qty) {
                    if($item->getData("order_item_id") == $id){
                        if($qty == 0) $item->delete();
                        else
                        $item->setData("qty",$qty)->save();
                    }
                }
            }
        }
        else{
            foreach ($items as $id => $qty) {
                if($qty == 0) continue;
                $itemR = Mage::getModel("vendorsrma/item");
                $data = array(
                    "order_item_id" => $id,
                    "qty"=>$qty,
                    "request_id"=>$this->getId()
                );
                $itemR->setData($data);
                try{
                    $itemR->save();
                }
                catch (Exception $e) {
                    throw new Mage_Core_Exception($e->getMessage());
                    return;
                }
            }
        }
        return $this;
    }

    public function getStore(){
        return Mage::app()->getStore();
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
    
    public function getRequestStateName(){
        return Mage::getModel("vendorsrma/option_state")->getTitleByKey($this->getState());
    }
    
    public function getItemCollections(){
        $items = Mage::getModel('vendorsrma/item')->getCollection()->addFieldToFilter('request_id',$this->getId());
        return $items;
    }
    
    public function getItemById($itemId){
        $item = Mage::getModel("sales/order_item")->load($itemId);
        return $item;
    }
    
	public function getPrintLabelUrl(){
		return Mage::getUrl("vesrma/download/print", array("id"=>$this->getId()));
	}
	
    protected function _processedResultHtml($content){
    	if(!$content) return $this;
		
    	$vendor = Mage::getModel("vendors/vendor")->load($this->getVendorId());
        
        $variables = array("request"=>$this , "vendor"=> $vendor);
        
        $processor = $this->getTemplateFilter();
        $processor->setUseSessionInUrl(false)
        ->setPlainTemplateMode(false);
        $processor->setIncludeProcessor(array($this, 'getInclude'))
        ->setVariables($variables);
    
        try{
            $processedResult = $processor->filter($content);
        }
        catch (Exception $e) {
            throw new Mage_Core_Exception($e->getMessage());
        }
        return $processedResult;
    }

    /** save comment notify status  */

    public function saveNotify($type = "vendor"){
        return;
        $vendor = Mage::getModel("vendors/vendor")->load($this->getVendorId());
        
        if($type == "vendor"){
        	$sender = array("from"=>Mage::helper("vendorsrma/config")->getContactNameVendor($vendor),"to"=>$this->getCustomerName());
        	$type = VES_VendorsRma_Model_Source_Message_Type::TYPE_VENDOR;
        }
        else{
        	$sender = array("from"=>Mage::helper("vendorsrma/config")->getContactNameAdmin(),"to"=>$this->getCustomerName());
        	$type = VES_VendorsRma_Model_Source_Message_Type::TYPE_ADMIN;
        }
      
        
        $templates = Mage::getModel("vendorsrma/template")->getCollection()->addFieldToFilter("store_id",array("IN"=>array($this->getStore()->getId()),0))

            ->addFieldToFilter("status_id",$this->getStatus());
        $templates->getSelect()->order('store_id DESC');

        $template =$templates->getFirstItem();
        
        
        $content = $template->getData("template_notify_history");
        
        if(!$content) return;
        
     	$processedResult = $this->_processedResultHtml($content);

        $message=array(
            'message'=> $processedResult,
            'attachment'=> null,
            'created_time'=> now(),
            'attachment'=>serialize(array()),
            'type'=> $type,
            'from'=>$sender["from"],
            'to'=>$sender["to"],
            'request_id'=>$this->getId(),
        );
        try{
            $model = Mage::getModel('vendorsrma/message');
            $model->setData($message);
            $model->save();
        }
        catch (Exception $e) {
            throw new Mage_Core_Exception($e->getMessage());
        }

        return $this;
    }


    /**
     * Get filter object for template processing logi
     *
     * @return VES_VendorsRma_Model_Source_Filter
     */
    public function getTemplateFilter()
    {
        if (empty($this->_templateFilter)) {
            $this->_templateFilter = Mage::getModel('vendorsrma/source_filter');
        }
        return $this->_templateFilter;
    }

    /* save Comment for each request Rma */

    public function saveMessage($post,$type,$sender){

       // $attachments = Mage::getModel("vendorsrma/source_file")->getActachment($post["filename"]);
        $files = Mage::getModel("vendorsrma/source_file")->uploadFile($post["filename"]);
        if(!$post["comment"] && count($files) == 0) return $this;
        $message=array(
            'message'=>$post["comment"],
            'attachment'=>implode(",", $files),
            'created_time'=> now(),
            'type'=>$type,
            'from'=>$sender["from"],
            'to'=>$sender["to"],
            'request_id'=>$this->getId(),
        );

        try{
            $model = Mage::getModel('vendorsrma/message');
            $model->setData($message);
            $model->save();
        }

        catch (Exception $e) {
            throw new Mage_Core_Exception($e->getMessage());
        }
        return $this;
    }

    /** save status history */

    public function saveStatusHistory($type,$change){
        $history = array(
            'status'=> $this->getStatus(),
            'created_time'=> now(),
            'type'=>$type,
            'change_by'=>$change,
            'request_id'=>$this->getId(),
        );

        $model = Mage::getModel('vendorsrma/history');
        $model->setData($history)->setId();

        try{
            $model->save();

            // send mail notify to vendor customer and admin
            
            $templates = Mage::getModel("vendorsrma/template")->getCollection()->addFieldToFilter("store_id",array("IN"=>array($this->getStore()->getId()),0))
            ->addFieldToFilter("status_id",$this->getStatus());
            $templates->getSelect()->order('store_id DESC');
            $template = $templates->getFirstItem();
            
            $emails = array("vendor"=>$template->getData("template_notify_vendor"),"customer"=>$template->getData("template_notify_customer"),"admin"=>$template->getData("template_notify_admin"));
            foreach($emails as $key=>$template){
            	if(!$template) continue;
            	$this->sendEmailNotify($key,$template,$this->getStore()->getId());
            }
            
        }

        catch (Exception $e) {
            throw new Mage_Core_Exception($e->getMessage());
        }
        return $this;
    }


    /**
     * Get either first store ID from a set website or the provided as default
     *
     * @param int|string|null $storeId
     *
     * @return int
     */
    protected function _getWebsiteStoreId($defaultStoreId = null)
    {
        if ($this->getWebsiteId() != 0 && empty($defaultStoreId)) {
            $storeIds = Mage::app()->getWebsite($this->getWebsiteId())->getStoreIds();
            reset($storeIds);
            $defaultStoreId = current($storeIds);
        }
        return $defaultStoreId;
    }

    public function getNotifyOrderVendorLink(){
    	$order = Mage::getModel("sales/order")->loadByIncrementId($this->getData("order_incremental_id"));
    	return Mage::getUrl("vendors/sales_order/view",array("order_id"=>$order->getId()));
    }
    
    
    public function getRmaGuestLink(){
    	return Mage::getUrl('vesrma/rma_guest/view/',array('sc'=>$this->getData('uid')));
    }
    
    public function getNotifyOrderAdminLink(){
    	$order = Mage::getModel("sales/order")->loadByIncrementId($this->getData("order_incremental_id"));
    	return Mage::getUrl("adminhtml/sales_order/view",array("order_id"=>$order->getId()));
    }
    
    public function getFormattedCreatedAt(){
    	return Mage::getModel('core/date')->date('F j, Y, g:i a',$this->getData('created_at'));
    }
	

    public function getRequestTypeName(){
    	return Mage::getModel("vendorsrma/source_type")->getTitleById($this->getType());
    }
    
    public function getStatusName(){
    	return Mage::getModel("vendorsrma/source_status")->getTitleById($this->getStatus());
    }
    
    public function getPackageOpenedLabel(){
    	return Mage::getModel("vendorsrma/option_pack")->getTitleByKey($this->getData("package_opened"));
    }
    
    public function getNotifyPrintlabelAllowed(){
    	return Mage::helper("vendorsrma/config")->allowPrint();
    }
    
    public function getNotifyRmaAddress(){
    	$vendor = Mage::getModel("vendors/vendor")->load($this->getVendorId());
    	return Mage::helper("vendorsrma/config")->getContactAddressVendor($vendor);
    }
    
    public function getConfirmShippingIsRequired(){
    	return Mage::helper("vendorsrma/config")->confirmShipping();
    }
    
    /**
     * Send email notify request
     *
     * @param string $type
     * @param string $backUrl
     * @param string $storeId
     *
     */
    public function sendEmailNotify($type = 'vendor', $template , $storeId = '0')
    {
	
		
    	if(!Mage::helper("vendorsrma/config")->emailNotify($type)) return $this;
		
			
        $types = array(
            'mark-customer' => self::XML_PATH_MARK_EMAIL_TEMPLATE,
            'mark-vendor' => self::XML_PATH_MARK_EMAIL_TEMPLATE,
            'vendor'   => self::XML_PATH_VENDOR_EMAIL_TEMPLATE,
            'customer'    => self::XML_PATH_CUSTOMER_EMAIL_TEMPLATE,
            'admin' => self::XML_PATH_ADMIN_EMAIL_TEMPLATE,
        );

        $vendor = Mage::getModel("vendors/vendor")->load($this->getVendorId());
		
		

        $mailtos = array(
            'mark-vendor'   => array(Mage::helper("vendorsrma/config")->getContactEmailVendor($vendor),Mage::helper("vendorsrma/config")->getContactNameVendor($vendor)),
            'mark-customer'    => array($this->getCustomerEmail(),$this->getCustomerName()),
            'vendor'   => array(Mage::helper("vendorsrma/config")->getContactEmailVendor($vendor),Mage::helper("vendorsrma/config")->getContactNameVendor($vendor)),
            'customer'    => array($this->getCustomerEmail(),$this->getCustomerName()),
            'admin' => array(Mage::helper("vendorsrma/config")->getContactEmail(),Mage::helper("vendorsrma/config")->getContactName())
        );


        if (!isset($types[$type])) {
            Mage::throwException(Mage::helper('customer')->__('Wrong transactional account email type'));
        }

        if (!$storeId) {
            $storeId = $this->_getWebsiteStoreId($this->getSendemailStoreId());
        }

        $content = $this->_processedResultHtml($template);
        

        $this->_sendEmailTemplate($types[$type], self::XML_PATH_REGISTER_EMAIL_IDENTITY,$mailtos[$type] ,
            array('name' => $mailtos[$type][1] , 'url' => $this->getRmaGuestLink() , 'content'=>$content ,'request'=>$this), $storeId);

        return $this;
    }
    /**
     * Send corresponding email template
     *
     * @param string $emailTemplate configuration path of email template
     * @param string $emailSender configuration path of email identity
     * @param array $templateParams
     * @param int|null $storeId
     */
    protected function _sendEmailTemplate($template, $sender,$mailtos, $templateParams = array(), $storeId = null)
    {
        /** @var $mailer Mage_Core_Model_Email_Template_Mailer */
        $mailer = Mage::getModel('core/email_template_mailer');
        $emailInfo = Mage::getModel('core/email_info');
        $emailInfo->addTo($mailtos[0], $mailtos[1]);
        $mailer->addEmailInfo($emailInfo);

        // Set all required params and send emails
        $mailer->setSender(Mage::getStoreConfig($sender, $storeId));
        $mailer->setStoreId($storeId);

        $mailer->setTemplateId(Mage::getStoreConfig($template, $storeId));
        $mailer->setTemplateParams($templateParams);
        $mailer->send();
        return $this;
    }
	
}