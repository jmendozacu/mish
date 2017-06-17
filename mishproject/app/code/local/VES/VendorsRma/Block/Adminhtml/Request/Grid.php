<?php

class VES_VendorsRma_Block_Adminhtml_Request_Grid extends VES_VendorsRma_Block_Vendor_Request_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('reasonGrid');
      $this->setDefaultSort('created_at');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('vendorsrma/request')->getCollection();
      $collection->joinTable(array('vendor_table'=>'vendors/vendor'),'entity_id = vendor_id',array('vendor'=>'vendor_id'),null,'left');

      Mage::dispatchEvent("admin_rma_grid_prepare_colletion_before", array("collection" => $collection));



      $this->setCollection($collection);
      return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      parent::_prepareColumns();

      $this->addColumnAfter('vendor',
      array(
          'header'=> Mage::helper('vendors')->__('Vendor Id'),
          'index' => 'vendor',
          'renderer'	=> new VES_VendorsRma_Block_Adminhtml_Widget_Grid_Renderer_Text(),
      ),
      'increment_id');


      return Mage_Adminhtml_Block_Widget_Grid::_prepareColumns();
  }


}