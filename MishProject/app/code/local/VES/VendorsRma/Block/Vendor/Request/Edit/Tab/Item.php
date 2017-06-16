<?php

class VES_VendorsRma_Block_Vendor_Request_Edit_Tab_Item extends Mage_Adminhtml_Block_Template
{
    public function _toHtml(){
        parent::_toHtml();
        $html =  $this->getLayout()->getBlock("order_items")->toHtml();
        return $html;
    }
}