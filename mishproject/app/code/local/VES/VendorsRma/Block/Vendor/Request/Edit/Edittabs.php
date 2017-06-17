<?php

class VES_VendorsRma_Block_Vendor_Request_Edit_Edittabs extends Mage_Adminhtml_Block_Template
{

    public function __construct()
    {
        parent::__construct();
        if ($this->getRequestData()->getId()) {
            $this->setTemplate('ves_vendorsrma/request/edit/tabs.phtml');
        }
    }

    public function getTitleVendorTab(){
        return Mage::helper('vendorsrma')->__('Vendor Infomation');
    }

    public function getTitleRequestTab()
    {
        return Mage::helper('vendorsrma')->__('Request Information');
    }

    public function getTitleNoteTab()
    {
        return Mage::helper('vendorsrma')->__('Notes');
    }

    public function getTitleCustomerTab()
    {
        return Mage::helper('vendorsrma')->__('Customer Infomation');
    }

    /** get Data Request */
    public function getRequestData(){
        return Mage::registry("current_request");
    }


    public function getCustomerHtml(){
        return $$this->getRequestData()->getData('customer_email');
    }

    public function getCustomerLink(){

        $customer=Mage::getModel('customer/customer')->load($this->getRequestData()->getData('customer_id'));
        if($customer->getId() && Mage::app()->getStore()->isAdmin()){
            $html='<a href="'.$this->getUrl('adminhtml/customer/edit',array('id'=>$customer->getId())).'" target="_blank">'.$this->getRequestData()->getData('customer_name').'</a>';
        }
        else{
            $html=$this->getRequestData()->getData('customer_name');
        }

        return $html;
    }

    public function getVendor(){
        $vendor = Mage::getModel("vendors/vendor")->load($this->getRequestData()->getVendorId());
        return $vendor;
    }

    public function getVendorEmail(){
        $vendor = $this->getVendor();
        return $vendor->getEmail() ? $vendor->getEmail() : "N/A";
    }

    public function getVendorLink(){
        $vendor = $this->getVendor();
        if(!$vendor->getId()) return "N/A";
        if (Mage::app()->getStore()->isAdmin()) {
            $html='<a href="'.$this->getUrl('adminhtml/vendors/edit',array('id'=>$vendor->getId())).'" target="_blank">'.$vendor->getTitle().'</a>';

        }
        else{
            $html =  $vendor->getTitle() ? $vendor->getTitle() : "N/A";
        }

        return $html;
    }

    public function getOrderLink(){
        $html='';
        $order=Mage::getModel('sales/order')->loadByIncrementId($this->getRequestData()->getData('order_incremental_id'));
        if($order->getId()){
            $html='<a href="'.$this->getUrl('*/sales_order/view',array('order_id'=>$order->getId())).'" target="_blank">#'.$this->getRequestData()->getData('order_incremental_id').'</a>';
        }
        else{
            if($this->getRequestData()->getData('order_incremental_id')){
                $html='#'.$this->getRequestData()->getData('order_incremental_id');
            }
        }
        return $html;
    }

    public function getOrderId(){
        $order=Mage::getModel('sales/order')->loadByIncrementId($this->getRequestData()->getData('order_incremental_id'));
        return $order->getId();
    }

    public function checkOrderByCustomer(){
        $customer=$this->getRequestData()->getData('customer_id');
        $order=Mage::getModel('sales/order')->loadByIncrementId($this->getRequestData()->getData('order_incremental_id'));
        if($customer == $order->getData('customer_id') || !$order->getId()) return true;
        return false;
    }

    public function getOrderHtml(){
        return "#".$this->getRequestData()->getData('order_incremental_id');
    }

    public function getStatus(){
        if (Mage::app()->getStore()->isAdmin())  return Mage::getModel('vendorsrma/status')->getToOptions(false);
        return Mage::getModel('vendorsrma/status')->getToOptions(true);
    }

    
    public function getState(){
        return Mage::getModel('vendorsrma/option_state')->getOptionArray();
    }
    
    
    public function getType(){
        return Mage::getModel('vendorsrma/type')->getToOptions();
    }

    public function getUrlUpdate(){
        return $this->getUrl("*/rma_request/ajaxUpdate");
    }
    public function getReason(){
        return Mage::getModel('vendorsrma/reason')->getToOptions();
    }

    public function getPack(){
        return Mage::getModel('vendorsrma/option_pack')->getOptionArray();
    }


    public function getStore(){
        return Mage::getModel('vendorsrma/attribute_source_store')->getAllOptions();
    }

    public function isEnableEditStatus(){
        if (!Mage::app()->getStore()->isAdmin()) {
            return false;
           // $status = Mage::getModel("vendorsrma/status")->load($this->getRequestData()->getData('status'));
            //if($status->getType() == VES_VendorsRma_Model_Option_Status_Type::TYPE_ADMIN) return false;
        }
        return true;
    }

    
    public function isEnableEditState(){
        if (!Mage::app()->getStore()->isAdmin()) {
            return false;
        }
        return true;
    }
    
    
    public function isEnableEditType(){
        if (!Mage::app()->getStore()->isAdmin()) {
            return false;
        }
        return true;
    }

    public function isEnableEditStore(){
        if (!Mage::app()->getStore()->isAdmin()) {
            return false;
        }
        return true;
    }

    public function isEnableEditReason(){
        if (!Mage::app()->getStore()->isAdmin()) {
            return false;
        }
        return true;
    }

    public function displayNoteTab(){
        if (!Mage::app()->getStore()->isAdmin()) {
            return false;
        }
        return true;
    }
    public function displayVendorTab(){
        if (!Mage::app()->getStore()->isAdmin()) {
            return false;
        }
        return true;
    }
    public function isEnableEditTracking(){
        if (!Mage::app()->getStore()->isAdmin()) {
            return false;
        }
        return true;
    }

    public function isEnableEditNote(){
        if (!Mage::app()->getStore()->isAdmin()) {
            return false;
        }
        return true;
    }

    public function isEnableEditPack(){
        if (!Mage::app()->getStore()->isAdmin()) {
            return false;
        }
        return true;
    }

}