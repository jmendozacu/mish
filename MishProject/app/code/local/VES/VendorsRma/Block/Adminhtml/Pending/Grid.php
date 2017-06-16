<?php

class VES_VendorsRma_Block_Adminhtml_Pending_Grid extends VES_VendorsRma_Block_Adminhtml_Request_Grid
{

  protected function _prepareCollection()
  {

      $collection = Mage::getModel('vendorsrma/request')->getCollection()->addAttributeToSelect('*');
      $store = $this->_getStore();
      $collection->addAttributeToFilter("state",array("eq"=>VES_VendorsRma_Model_Option_State::STATE_BEING));
      $collection->joinTable(array('vendor_table'=>'vendors/vendor'),'entity_id = vendor_id',array('vendor'=>'vendor_id'),null,'left');
      
      Mage::dispatchEvent("vendor_rma_grid_prepare_colletion_before", array("collection" => $collection));

      $this->setCollection($collection);
      return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
  }


}