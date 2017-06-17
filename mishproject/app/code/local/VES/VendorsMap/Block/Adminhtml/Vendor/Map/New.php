<?php

class VES_VendorsMap_Block_Adminhtml_Vendor_Map_New extends VES_VendorsMap_Block_Vendor_Account_New{

    public function getUrlNew(){
        return $this->getUrl("adminhtml/vendors_map/save",array("vendor_id"=>$this->getVendors()->getId()));
    }

    public function getVendors(){
        $id     = $this->getRequest()->getParam('id');
        $model  = Mage::getModel('vendors/vendor')->load($id);
        return $model;
    }

}