<?php

class Mirasvit_Rma_Block_Rma_Guest_New extends Mirasvit_Rma_Block_Rma_New
{
    public function getStep1PostUrl()
    {
        return Mage::getUrl('rma/guest/new');
    }

    public function getStep2PostUrl()
    {
        return Mage::getUrl('rma/guest/save');
    }

    public function getOrder()
    {
        return Mage::registry('current_order');
    }

    public function getOrderIncrementId()
    {
        return Mage::app()->getRequest()->getParam('order_increment_id');
    }

    public function getEmail()
    {
        return Mage::app()->getRequest()->getParam('email');
    }

    public function getAllowGift()
    {
        return $this->getConfig()->getGeneralIsGiftActive();
    }
}