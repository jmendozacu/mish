<?php

class VES_VendorsRma_Vendor_Rma_AjaxController extends VES_Vendors_Controller_Action
{
    /** ajax load order  */

    public function ajaxOrderAction(){

        $date = Mage::helper("vendorsrma/config")->orderExpiryDay();


        $lastdate = Mage::getModel('core/date')->date('Y-m-d', strtotime("-".$date." days"));

        $order_id  = $this->getRequest()->getParam('order');
        $vendor = $this->_getSession()->getVendor();
        $order=Mage::getModel('sales/order')->getCollection()
            ->addAttributeToFilter("vendor_id",$vendor->getId())
            ->addAttributeToFilter('created_at', array('from'  => $lastdate))
            ->addAttributeToFilter("status",'complete')
            ->addAttributeToFilter('increment_id',array('like'=>$order_id.'%'));
        Mage::register('order', $order);
        $layout = $this->loadLayout();
        $block = $this->getLayout()->createBlock(
            'Mage_Core_Block_Template',
            'ajaxloadorder',
            array('template' => 'ves_vendorsrma/ajax/order.phtml')
        );
        echo $block->toHtml();
    }


}