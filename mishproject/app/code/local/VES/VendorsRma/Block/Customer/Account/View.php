<?php
class VES_VendorsRma_Block_Customer_Account_View extends VES_VendorsRma_Block_Customer_Abstract
{
    public function _prepareLayout()
    {
        if(!$this->getRequest()->getParam('sc'))
            $this->getLayout()->getBlock('customer_account_navigation')->setActive('vesrma/rma_customer/list');
        return parent::_prepareLayout();
    }

}