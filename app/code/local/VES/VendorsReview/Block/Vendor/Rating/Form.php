<?php

class VES_VendorsReview_Block_Vendor_Rating_Form extends Mage_Core_Block_Template
{
    public function __construct()
    {
        $customerSession = Mage::getSingleton('customer/session');

        parent::__construct();

        $data =  Mage::getSingleton('vendorsreview/session')->getFormData(true);
        $data = new Varien_Object($data);

        // add logged in customer name as nickname
        if (!$data->getNickName()) {
            $customer = $customerSession->getCustomer();
            if ($customer && $customer->getId()) {
                $data->setNickName($customer->getFirstname());
                $this->setCustomerId($customer->getId());
            }
        }

        $this->setAllowWriteReviewFlag($customerSession->isLoggedIn() && $this->canReview());
        if (!$this->getAllowWriteReviewFlag) {
            $this->setLoginLink(
                Mage::getUrl('customer/account/login/', array(
                    Mage_Customer_Helper_Data::REFERER_QUERY_PARAM_NAME => Mage::helper('core')->urlEncode(
                        Mage::getUrl('*/*/*', array('_current' => true)) .
                        '#review-form')
                    )
                )
            );
        }

        $this->setTemplate('ves_vendorsreview/review/rating/form.phtml')
            ->assign('data', $data)
            ->assign('messages', Mage::getSingleton('vendorsreview/session')->getMessages(true));
    }
    
    public function canReview() {
    	if(Mage::registry('no_rating')) return false;
    	return true;
    }


    public function getAction()
    {
        return Mage::getUrl('vendorsreview/review/post', array('vendor' => Mage::registry('vendor_id')));
    }
    
    public function getVendorTitle() {
    	return Mage::getModel('vendors/vendor')->loadByVendorId(Mage::registry('vendor_id'))->getTitle();
    }
    
    public function getVendorId() {
    	return Mage::getModel('vendors/vendor')->loadByVendorId(Mage::registry('vendor_id'))->getId();
    }

    public function getRatings()
    {
        $ratingCollection = Mage::getModel('vendorsreview/rating')->getCollection()
     	->setOrder('position','asc');
        return $ratingCollection;
    }
    
     public function getOrderId() {
     	return $this->getRequest()->getParams('order_id');
     }
     
     public function getOrder(){
     	return Mage::getModel('sales/order')->load($this->getOrderId());
     }
}
