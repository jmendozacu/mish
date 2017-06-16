<?php

class VES_VendorsRma_Block_Vendor_Pending_Grid extends VES_VendorsRma_Block_Vendor_Request_Grid
{

  protected function _prepareCollection()
  {
      $options = Mage::getModel("vendorsrma/status")->getOptions();
      $vendorId = Mage::getSingleton('vendors/session')->getVendorId();
      $collection = Mage::getModel('vendorsrma/request')->getCollection()->addAttributeToSelect('*');
      $store = $this->_getStore();
      $collection->addAttributeToFilter("vendor_id",array("eq"=>$vendorId));
      $collection->addAttributeToFilter("status",array("eq"=>$options[0]["value"]));
      Mage::dispatchEvent("vendor_rma_grid_prepare_colletion_before", array("collection" => $collection));

      $this->setCollection($collection);
      return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
  }

}