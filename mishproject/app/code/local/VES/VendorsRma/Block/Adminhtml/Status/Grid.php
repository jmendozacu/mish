<?php

class VES_VendorsRma_Block_Adminhtml_Status_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('statusGrid');
      $this->setDefaultSort('sort_order');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('vendorsrma/status')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {

      $this->addColumn('status_id', array(
          'header'    => Mage::helper('vendorsrma')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'status_id',
      ));

      $this->addColumn('title', array(
          'header'    => Mage::helper('vendorsrma')->__('Title'),
          'align'     =>'left',
          'index'     => 'title',
      ));

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
      /*
      $status = Mage::getModel("vendorsrma/option_status")->getOptionArray();
      $this->addColumn('active', array(
          'header'    => Mage::helper('vendorsrma')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'active',
          'type'      => 'options',
          'options'   => $status,
      ));
       */
      $this->addColumn('sort_order', array(
          'header'    => Mage::helper('vendorsrma')->__('Sort Order'),
          'align'     =>'left',
          'index'     => 'sort_order',
          'width'     => '50px',
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

    protected function _prepareMassaction()
    {


        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}