<?php

class VES_VendorsRma_Block_Vendor_Request_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('requestGrid');
      $this->setDefaultSort('created_at');
      $this->setDefaultDir('DESC');
      $this->setSaveParametersInSession(true);
  }

  protected function _getStore(){
        return Mage::app()->getStore();
  }

  protected function _prepareCollection()
  {

      $vendorId = Mage::getSingleton('vendors/session')->getVendorId();
      $collection = Mage::getModel('vendorsrma/request')->getCollection()->addAttributeToSelect('*');
      $store = $this->_getStore();
      $collection->addAttributeToFilter("vendor_id",array("eq"=>$vendorId));

      Mage::dispatchEvent("vendor_rma_grid_prepare_colletion_before", array("collection" => $collection));

      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('increment_id', array(
          'header'    => Mage::helper('vendorsrma')->__('RMA ID #'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'increment_id',
      ));

      $this->addColumn('order_incremental_id', array(
          'header'    => Mage::helper('vendorsrma')->__('Order Id'),
          'align'     =>'left',
          'width'     => '150px',
          'index'     => 'order_incremental_id',
      ));


      $this->addColumn('created_at', array(
          'header'    => Mage::helper('vendorsrma')->__('Date'),
          'align'     =>'left',
          'index'     => 'created_at',
          'width'     => '200px',
          'type'=> 'datetime',
          'time'=> true
      ));

      /*
      if (!Mage::app()->isSingleStoreMode()) {
          $this->addColumn('store_id', array(
              'header'        => Mage::helper('cms')->__('Store View'),
              'index'         => 'store_id',
              'type'          => 'store',
              'store_all'     => true,
              'store_view'    => true,
              'sortable'      => false,
              'filter' => false,
              'renderer'=>new VES_VendorsRma_Block_Adminhtml_Widget_Grid_Renderer_Store(),
          ));
      }
        */

      $this->addColumn('customer_name', array(
          'header'    => Mage::helper('vendorsrma')->__('Customer Name'),
          'align'     =>'left',
          'index'     => 'customer_name',
      ));


      $this->addColumn('customer_email', array(
          'header'    => Mage::helper('vendorsrma')->__('Customer Email'),
          'align'     =>'left',
          'index'     => 'customer_email',
      ));

        /*
      $status = Mage::getModel("vendorsrma/status")->getToOptions();

      $this->addColumn('status', array(
          'header'    => Mage::helper('vendorsrma')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => $status,
          'width'     => '200px',
      ));
      */
      $states = Mage::getModel("vendorsrma/option_state")->getOptionArray();
      
      $this->addColumn('state', array(
          'header'    => Mage::helper('vendorsrma')->__('State'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'state',
          'type'      => 'options',
          'options'   => $states,
          'width'     => '200px',
          'renderer'=>new VES_VendorsRma_Block_Adminhtml_Widget_Grid_Renderer_State(),
      ));
      
      

      $reason = Mage::getModel("vendorsrma/reason")->getToOptions();
      $this->addColumn('reason', array(
          'header'    => Mage::helper('vendorsrma')->__('Reason'),
          'align'     =>'left',
          'index'     => 'reason',
          'width'     => '50px',
          'width'     => '200px',
          'type'      => 'options',
          'options'   => $reason,
      ));
      
      $reason = Mage::getModel("vendorsrma/type")->getToOptions();
      $this->addColumn('type', array(
          'header'    => Mage::helper('vendorsrma')->__('Type'),
          'align'     =>'left',
          'index'     => 'type',
          'width'     => '50px',
          'width'     => '200px',
          'type'      => 'options',
          'options'   => $reason,
      ));

      $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('vendorsrma')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('vendorsrma')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('vendorsrma')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('vendorsrma')->__('XML'));
	  
      return parent::_prepareColumns();
  }


  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}