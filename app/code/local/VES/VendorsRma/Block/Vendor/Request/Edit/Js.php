<?php


class VES_VendorsRma_Block_Vendor_Request_Edit_Js extends Mage_Adminhtml_Block_Template
{
    /**
     * Get currently edited Request
     *
     * @return VES_VendorsRma_Model_Request
     */
    public function getRequest()
    {
        return Mage::registry('current_request');
    }

    public function getUrlUpdateMessage(){
        return Mage::getUrl('vendors/rma_request/ajaxEditMessage');
    }
}
