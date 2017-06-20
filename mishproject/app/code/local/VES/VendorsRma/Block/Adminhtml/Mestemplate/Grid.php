<?php
class VES_VendorsRma_Block_Adminhtml_Mestemplate_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('templateGrid');
      $this->setDefaultSort('mestemplate_id');
      $this->setDefaultDir('DESC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('vendorsrma/mestemplate')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('mestemplate_id', array(
          'header'    => Mage::helper('vendorsrma')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'mestemplate_id',
      ));

      
      $status = Mage::getModel("vendorsrma/option_status")->getOptionArray();
      $this->addColumn('status', array(
      		'header'    => Mage::helper('vendorsrma')->__('Active'),
      		'align'     => 'left',
      		'width'     => '100px',
      		'index'     => 'status',
      		'type'      => 'options',
      		'options'   => $status,
      ));
      
      $type = Mage::getModel("vendorsrma/option_type")->getOptionArray();
      $this->addColumn('type', array(
          'header'    => Mage::helper('vendorsrma')->__('Type'),
          'align'     => 'left',
          'width'     => '100px',
          'index'     => 'type',
          'type'      => 'options',
          'options'   => $type,
      ));
      
      $this->addColumn('title', array(
          'header'    => Mage::helper('vendorsrma')->__('Title'),
          'align'     =>'left',
      	   'width'     => '350px',
          'index'     => 'title',
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
        $this->setMassactionIdField('mestemplate_id');
        $this->getMassactionBlock()->setFormFieldName('mestemplate');

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