<?php

class VES_VendorsMap_Block_Adminhtml_Vendor_Map_Sidebar extends VES_VendorsMap_Block_Vendor_Account_Sidebar{

    public function getVendors(){
        $id     = $this->getRequest()->getParam('id');
        $model  = Mage::getModel('vendors/vendor')->load($id);
        return $model;
    }

}