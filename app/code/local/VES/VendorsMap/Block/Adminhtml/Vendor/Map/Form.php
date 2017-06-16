<?php
class VES_VendorsMap_Block_Adminhtml_Vendor_Map_Form extends VES_VendorsMap_Block_Map_Form
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

    public function getUrlSave(){
        return $this->getUrl("adminhtml/vendors_map/saveformedit",array('id'=>$this->getMarker()->getId(),"vendor_id"=>$this->getVendors()->getId()));
    }

}