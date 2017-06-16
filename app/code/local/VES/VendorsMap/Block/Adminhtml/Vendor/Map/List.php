<?php

class VES_VendorsMap_Block_Adminhtml_Vendor_Map_List extends VES_VendorsMap_Block_Map_List
{

    public function getVendors(){
        if($this->getCurrentVendorId()){
            $id = $this->getCurrentVendorId();
        }
        else{
             $id     = $this->getRequest()->getParam('id');
        }
        $model  = Mage::getModel('vendors/vendor')->load($id);
        return $model;
    }
}