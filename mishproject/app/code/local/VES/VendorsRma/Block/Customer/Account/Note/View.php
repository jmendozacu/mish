<?php
class VES_VendorsRma_Block_Customer_Account_Note_View extends VES_VendorsRma_Block_Customer_Abstract
{

    public function _prepareLayout()
    {
        if(!$this->getRequest()->getParam('sc'))
            $this->getLayout()->getBlock('customer_account_navigation')->setActive('vesrma/rma_customer/list');
        return parent::_prepareLayout();
    }

    public function getSaveNoteAttachment(){
        if(!$this->getRequest()->getParam('sc'))
        return $this->getUrl('vesrma/rma_customer/saveNoteAttacment/',array('id'=>$this->getRequestRma()->getId(),'note_id'=>$this->getNoteRma()->getId()));
        return $this->getUrl('vesrma/rma_guest/saveNoteAttacment/',array('id'=>$this->getRequestRma()->getId(),'note_id'=>$this->getNoteRma()->getId()));
    }


    public function getNameAttachment($file){
        $files = explode("/",$file);
        return $files[count($files)-1];
    }
    

}