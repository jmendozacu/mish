<?php

class VES_VendorsRma_Block_Adminhtml_Reason_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('reasonGrid');
      $this->setDefaultSort('sort_order');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('vendorsrma/reason')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('reason_id', array(
          'header'    => Mage::helper('vendorsrma')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'reason_id',
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

      $status = Mage::getModel("vendorsrma/option_status")->getOptionArray();
      $this->addColumn('active', array(
          'header'    => Mage::helper('vendorsrma')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'active',
          'type'      => 'options',
          'options'   => $status,
      ));

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
        $this->setMassactionIdField('reason_id');
        $this->getMassactionBlock()->setFormFieldName('reason');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('vendorsrma')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('vendorsrma')->__('Are you sure?')
        ));

        $status = Mage::getModel("vendorsrma/option_status")->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('vendorsrma')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('vendorsrma')->__('Status'),
                         'values' => $status
                     )
             )
        ));
        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}