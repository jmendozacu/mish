<?php
/**
 * @copyright   Copyright (c) 2009-2011 Amasty (http://www.amasty.com)
 */ 
class Amasty_Perm_Model_Observer
{
    public function handleAdminUserSaveAfter($observer) 
    {
        $editor = Mage::getSingleton('admin/session')->getUser();
        if (!$editor) // API or smth else
            return $this;  
             
        $user = $observer->getDataObject(); 
        if ($editor->getId() == $user->getId()){ // My Account
            return $this;     
        }
                 
        $ids = $user->getSelectedCustomers();
        if (is_null($ids))
            return $this;        
        $ids = Mage::helper('adminhtml/js')->decodeGridSerializedInput($ids);
        
        Mage::getModel('amperm/perm')->assignCustomers($user->getId(), $ids);
        
        return $this;           
    }
    
    public function handleOrderCollectionLoadBefore($observer) 
    {
        if ('amperm' == Mage::app()->getRequest()->getModuleName())
            return $this;
            
        $uid = Mage::helper('amperm')->getCurrentSalesPersonId();
        if ($uid){
            $permissionManager = Mage::getModel('amperm/perm');
            $collection = $observer->getOrderGridCollection();
            if ($collection){
                $permissionManager->addOrdersRestriction($collection, $uid);
            }
            else {
                $keys = array_keys($observer->getData());
                $collection = $observer->getData($keys[1]);
                $permissionManager->addOrderDataRestriction($collection, $uid);
            }
        }       
        
        return $this;    
    }
    
    public function handleCustomerCollectionLoadBefore($observer) 
    {
        $collection = $observer->getCollection();
        if (strpos(get_class($collection),'Customer_Collection')){
            $uid = Mage::helper('amperm')->getCurrentSalesPersonId();
            if ($uid){
                $permissionManager = Mage::getModel('amperm/perm');
                $permissionManager->addCustomersRestriction($collection, $uid);
            }         
        }
        
        return $this;    
    } 
       
    public function handleCustomerSaveAfter($observer) 
    {
        //registration form
        $uid = Mage::app()->getRequest()->getParam('sales_person');
        if ($this->_isAdmin()){
            $uid = Mage::helper('amperm')->getCurrentSalesPersonId();
        }
        
        if ($uid){
            Mage::getModel('amperm/perm')->assignOneCustomer($uid, $observer->getCustomer()->getId());    
        }  
               
        return $this; 
    }
    
    public function handleOrderCreated($observer) 
    {
        $user = null;
        
        $isGuest = false;
        $orders = $observer->getOrders(); // multishipping
        if (!$orders){ // all other situalions like single checkout, goofle checkout, admin 
            $orders  = array($observer->getOrder());
            $isGuest = $orders[0]->getCustomerIsGuest();
        }
        
        if ($this->_isAdmin()){
            $uid = Mage::helper('amperm')->getCurrentSalesPersonId();
            if ($uid){
                Mage::getModel('amperm/perm')->assignOneOrder($uid, $orders[0]->getId());
                $user = Mage::getSingleton('admin/session')->getUser();
            }
        }
        elseif (!$isGuest) {
            foreach ($orders as $order){
                $uid = Mage::getModel('amperm/perm')->assignOrderByCustomer($order->getCustomerId(), $order->getId());
            }
            $user = Mage::getModel('admin/user')->load($uid);
        }
        
        // send email
        if (Mage::getStoreConfig('amperm/general/send_email') && $user){
            foreach ($orders as $order){
                try {
                    $this->_sendEmail($user->getEmail(), $order);
                } 
                catch (Exception $e) {
                    print_r($e);
                    Mage::logException($e);
                }   
            }              
        }

        return $this;
    }
    
    // for old versions
    public function handleCoreCollectionAbstractLoadBefore($observer)
    {
        if (!Mage::helper('ambase')->isVersionLessThan(1, 4, 2))
            return;
        
       $collection = $observer->getCollection();
       if ($collection instanceof Mage_Sales_Model_Mysql4_Order_Grid_Collection)
       {
            $mod  = Mage::app()->getRequest()->getModuleName();
            $uid = Mage::helper('amperm')->getCurrentSalesPersonId();
            if ($uid && 'amperm' != $mod){
                $permissionManager = Mage::getModel('amperm/perm');
                if ($collection){
                    $permissionManager->addOrdersRestriction($collection, $uid);
                }
            }
       }        
    }
       
    protected function _sendEmail($to, $order)
    {
        if (!Mage::getStoreConfig('amperm/general/send_email')){
            return;
        }
            
        if (!Mage::helper('sales')->canSendNewOrderEmail($order->getStoreId())) {
            return;
        }

        $translate = Mage::getSingleton('core/translate');
        /* @var $translate Mage_Core_Model_Translate */
        $translate->setTranslateInline(false);

        $paymentBlock = Mage::helper('payment')->getInfoBlock($order->getPayment())
            ->setIsSecureMode(true);
        $paymentBlock->getMethod()->setStore($order->getStoreId());

        $mailTemplate = Mage::getModel('core/email_template');
        /* @var $mailTemplate Mage_Core_Model_Email_Template */


        $mailTemplate->setDesignConfig(array('area'=>'frontend', 'store'=>$order->getStoreId()))
            ->sendTransactional(
                Mage::getStoreConfig('sales_email/order/template', $order->getStoreId()),
                Mage::getStoreConfig('sales_email/order/identity', $order->getStoreId()),
                $to,
                null,
                array(
                    'order'         => $order,
                    'billing'       => $order->getBillingAddress(),
                    'payment_html'  => $paymentBlock->toHtml(),
                )
            );
            
        $translate->setTranslateInline(true);
    }
    
    protected function _isAdmin()
    {
        if (Mage::app()->getStore()->isAdmin())
            return true;
        // for some reason isAdmin does not work here
        if (Mage::app()->getRequest()->getControllerName() == 'sales_order_create')
            return true;
            
        return false;
    }
   
}