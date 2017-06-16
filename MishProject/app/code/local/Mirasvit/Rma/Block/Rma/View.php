<?php

class Mirasvit_Rma_Block_Rma_View extends Mage_Core_Block_Template
{
    public function getConfig()
    {
        return Mage::getSingleton('rma/config');
    }

	protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $rma = $this->getRma();
        if ($headBlock = $this->getLayout()->getBlock('head')) {
            $headBlock->setTitle(Mage::helper('rma')->__('RMA #%s', $rma->getIncrementId()));
        }
    }

    public function getId()
    {
        return $this->getRma()->getId();
    }

    public function getRma()
    {
        return Mage::registry('current_rma');
    }

    public function getOrderUrl($orderId)
    {
        return Mage::getUrl('sales/order/view', array('order_id' => $orderId));
    }

    public function getCommentPostUrl()
    {
        return Mage::getUrl('rma/rma/savecomment');
    }

    protected $commentCollection = false;
    public function getCommentCollection()
    {
        if (!$this->commentCollection) {
            $this->commentCollection = $this->getRma()->getCommentCollection()
                ->addFieldToFilter('is_visible_in_frontend', true)
                ;
        }
        return $this->commentCollection;
    }

    public function getConfirmationUrl()
    {
        return Mage::getUrl('rma/rma/view', array('id' => $this->getRma()->getId(), 'shipping_confirmation' => true));
    }

    public function getPrintUrl()
    {
        return $this->getRma()->getGuestPrintUrl();
    }

    public function getPrintLabelUrl()
    {
        return $this->getRma()->getGuestPrintLabelUrl();
    }

    public function getCustomFields($isEdit = false)
    {
        $collection = Mage::helper('rma/field')->getVisibleCustomerCollection($this->getRma()->getStatusId(), $isEdit);
        return $collection;
    }

    public function getShippingConfirmation()
    {
        $str = $this->getConfig()->getGeneralShippingConfirmationText();
        $str = str_replace('"', '\'', $str);
        return $str;
    }

    public function getIsRequireShippingConfirmation()
    {
        if ($this->getRma()->getStatus()->getCode() == Mirasvit_Rma_Model_Status::PACKAGE_SENT) {
            return false;
        }
        return $this->getConfig()->getGeneralIsRequireShippingConfirmation();
    }


}