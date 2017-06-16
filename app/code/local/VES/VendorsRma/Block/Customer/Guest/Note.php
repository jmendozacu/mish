<?php
class VES_VendorsRma_Block_Customer_Guest_Note extends VES_VendorsRma_Block_Customer_Abstract
{

    public function _prepareLayout()
    {

        return parent::_prepareLayout();
    }

    public function getSaveNote(){
        return $this->getUrl('vesrma/rma_guest/saveNote/',array('id'=>$this->getRequestRma()->getId()));
    }



}