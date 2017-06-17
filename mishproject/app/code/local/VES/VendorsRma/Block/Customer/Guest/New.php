<?php
class VES_VendorsRma_Block_Customer_Guest_New extends Mage_Core_Block_Template
{
    public function getSubmitUrl(){
        return $this->getUrl('vesrma/rma_guest/save');
    }
    public function getContinueUrl(){
        return $this->getUrl('vesrma/rma_guest/new');

    }
}