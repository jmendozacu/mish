<?php
class VES_VendorsRma_Block_Customer_Account_List extends Mage_Core_Block_Template
{
    public function _prepareLayout()
    {
        parent::_prepareLayout();
        $pager_open = $this->getLayout()->createBlock('page/html_pager', 'product.pager')
            ->setCollection($this->getRequestRma());
        $this->setChild('pager_request', $pager_open);
        $this->getLayout()->getBlock('customer_account_navigation')->setActive('vesrma/rma_customer/list');
        return $this;
    }
    /**
     * function name :getTickets()
     *
     * @param null
     * @return array
     */
    public function getRequestRma()
    {
        if (!$this->hasData('request_rma')) {
            $storeId = Mage::app()->getStore()->getId();
            $customer = Mage::getSingleton('customer/session') ->getCustomer();
            $requests = Mage::getModel('vendorsrma/request')->getCollection()->addAttributeToSelect('*')->addAttributeToFilter('customer_email',array('eq'=>$customer->getEmail()))->addAttributeToFilter('store_id',array('in'=>array($storeId,'0')));
            $requests->setOrder('created_at', 'desc');
            $this->setData('request_rma',$requests);
        }
        return $this->getData('request_rma');
    }

    public function getPagerRequestHtml()
    {
        return $this->getChildHtml('pager_request');
    }

    public function isCancelRma($state){
        $check = false;
        if($state == VES_VendorsRma_Model_Option_State::STATE_OPEN ) $check = true;
        return $check;
    }

    public function getNewUrl(){
        return $this->getUrl('vesrma/rma_customer/new');
    }
}
