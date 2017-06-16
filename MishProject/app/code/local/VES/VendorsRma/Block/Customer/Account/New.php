<?php
class VES_VendorsRma_Block_Customer_Account_New extends Mage_Core_Block_Template
{
    public function _prepareLayout()
    {
        if(!$this->getRequest()->getParam('sc'))
        $this->getLayout()->getBlock('customer_account_navigation')->setActive('vesrma/rma_customer/list');
        return parent::_prepareLayout();
    }
    public function getSubmitUrl(){
        return $this->getUrl('vesrma/rma_customer/save');
    }

}