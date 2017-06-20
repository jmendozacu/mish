<?php

class Mirasvit_Rma_Block_Rma_Guest_View extends Mirasvit_Rma_Block_Rma_View
{
    public function getCommentPostUrl()
    {
        return Mage::getUrl('rma/guest/savecomment');
    }

    public function getId()
    {
        return $this->getRma()->getGuestId();
    }

    public function getConfirmationUrl()
    {
        return Mage::getUrl('rma/guest/view', array('id' => $this->getRma()->getGuestId(), 'shipping_confirmation' => true));
    }

}