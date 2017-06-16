<?php

class Mish_Personallogistic_Block_Adminhtml_Personallogistic_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('personallogisticGrid');
      $this->setDefaultSort('personallogistic_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('personallogistic/personallogistic')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('personallogistic_id', array(
          'header'    => Mage::helper('personallogistic')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'personallogistic_id',
      ));

       $this->addColumn('profilepic', array(
            'header'    => Mage::helper('personallogistic')->__('Profile Picture'),
            'width'     => '150',
            'index'     => 'profilepic',
            'renderer' => 'Mish_Personallogistic_Block_Adminhtml_Personallogistic_Grid_Renderer_Image',
             ));

       $this->addColumn('created_time', array(
          'header'    => Mage::helper('personallogistic')->__('Created Date & Time'),
          'align'     =>'left',
          'index'     => 'created_time',
      ));


      $this->addColumn('firstname', array(
          'header'    => Mage::helper('personallogistic')->__('First Name'),
          'align'     =>'left',
          'index'     => 'firstname',
      ));

       $this->addColumn('lastname', array(
          'header'    => Mage::helper('personallogistic')->__('Last Name'),
          'align'     =>'left',
          'index'     => 'lastname',
      ));


 $this->addColumn('dob', array(
          'header'    => Mage::helper('personallogistic')->__('Date of Birth'),
          'align'     =>'left',
          'index'     => 'dob',
      ));

 $this->addColumn('rut', array(
          'header'    => Mage::helper('personallogistic')->__('Rut'),
          'align'     =>'left',
          'index'     => 'rut',
      ));


 $this->addColumn('mail', array(
          'header'    => Mage::helper('personallogistic')->__('E mail'),
          'align'     =>'left',
          'index'     => 'mail',
      ));


 $this->addColumn('transport', array(
          'header'    => Mage::helper('personallogistic')->__('Transport'),
          'align'     =>'left',
          'index'     => 'transport',
      ));


 $this->addColumn('region', array(
          'header'    => Mage::helper('personallogistic')->__('Region'),
          'align'     =>'left',
          'index'     => 'region',
      ));


 $this->addColumn('price', array(
          'header'    => Mage::helper('personallogistic')->__('Price'),
          'align'     =>'left',
          'index'     => 'price',
      ));


	  /*
      $this->addColumn('content', array(
			'header'    => Mage::helper('personallogistic')->__('Item Content'),
			'width'     => '150px',
			'index'     => 'content',
      ));
	  */

      $this->addColumn('status', array(
          'header'    => Mage::helper('personallogistic')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array(
              1 => 'Approved',
              2 => 'Unapproved',
          ),
      ));
	  
        // $this->addColumn('action',
        //     array(
        //         'header'    =>  Mage::helper('personallogistic')->__('Action'),
        //         'width'     => '100',
        //         'type'      => 'action',
        //         'getter'    => 'getId',
        //         'actions'   => array(
        //             array(
        //                 'caption'   => Mage::helper('personallogistic')->__('Edit'),
        //                 'url'       => array('base'=> '*/*/edit'),
        //                 'field'     => 'id'
        //             )
        //         ),
        //         'filter'    => false,
        //         'sortable'  => false,
        //         'index'     => 'stores',
        //         'is_system' => true,
        // ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('personallogistic')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('personallogistic')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('personallogistic_id');
        $this->getMassactionBlock()->setFormFieldName('personallogistic');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('personallogistic')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('personallogistic')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('personallogistic/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('personallogistic')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('personallogistic')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));
        return $this;
    }

  public function getRowUrl($row)
  {
      //return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}