<?php

class VES_Commision_Block_Adminhtml_Managecommision_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('commisionGrid');
      $this->setDefaultSort('commision_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('vendors/vendor')->getCollection()
                ->addAttributeToSelect('telephone')
                ->addAttributeToSelect('postcode')
                ->addAttributeToSelect('country_id')
                ->addAttributeToSelect('group_id')
                ->addAttributeToSelect('firstname')
                ->addAttributeToSelect('lastname');
      $this->setCollection($collection);

   /*   echo "<pre>++";
      print_r($collection->getData());
      echo "</pre>";*/
     /* exit;*/
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('entity_id', array(
          'header'    => Mage::helper('commision')->__('ID'),
          'width'     => '250px',
          'index'     => 'entity_id',
         

      ));

   

      $this->addColumn('firstname', array(
          'header'    => Mage::helper('commision')->__('First Name'),
          'align'     =>'left',
          'width'     => '250px',
          'index'     => 'firstname',
         
      ));

    $this->addColumn('lastname', array(
          'header'    => Mage::helper('commision')->__('Last Name'),
          'align'     =>'left',
          'width'     => '250px',
          'index'     => 'lastname',
         
      ));

       $this->addColumn('vendor_id', array(
          'header'    => Mage::helper('commision')->__('Vendor ID'),
          'index'     => 'vendor_id',
          'width'     => '250px',
         
         
      ));

      /*$this->addColumn('created_at', array(
          'header'    => Mage::helper('commision')->__('Date of Registration'),
          'index'     => 'created_at',
          'filter' => false,
          
      ));*/

      // $this->addColumn('selected_categories', array(
      //     'header'    => Mage::helper('commision')->__('Selected Categories'),
      //     'index'     => 'selected_categories',
      // ));

      // $this->addColumn('percent_commission', array(
      //     'header'    => Mage::helper('commision')->__('% Commission'),
      //     'index'     => 'percent_commission',
      // ));
            
    /*  $this->addColumn('productcount', array(
          'header'    => Mage::helper('commision')->__('Number of products'),
          'index'     => 'productcount',
      ));*/

    /*
      $this->addColumn('content', array(
      'header'    => Mage::helper('commision')->__('Item Content'),
      'width'     => '150px',
      'index'     => 'content',
      ));
    */

    /*  $this->addColumn('status', array(
          'header'    => Mage::helper('commision')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array(
              1 => 'Enabled',
              2 => 'Disabled',
          ),
      ));*/
    
        // $this->addColumn('action',
        //     array(
        //         'header'    =>  Mage::helper('commision')->__('Action'),
        //         'width'     => '100',
        //         'type'      => 'action',
        //         'getter'    => 'getId',
        //         'actions'   => array(
        //             array(
        //                 'caption'   => Mage::helper('commision')->__('Edit'),
        //                 'url'       => array('base'=> '*/*/edit'),
        //                 'field'     => 'id'
        //             )
        //         ),
        //         'filter'    => false,
        //         'sortable'  => false,
        //         'index'     => 'stores',
        //         'is_system' => true,
        // ));
    
    $this->addExportType('*/*/exportCsv', Mage::helper('commision')->__('CSV'));
    $this->addExportType('*/*/exportXml', Mage::helper('commision')->__('XML'));
    
      return parent::_prepareColumns();
  }

    // protected function _prepareMassaction()
    // {
    //     $this->setMassactionIdField('commision_id');
    //     $this->getMassactionBlock()->setFormFieldName('commision');

    //     $this->getMassactionBlock()->addItem('delete', array(
    //          'label'    => Mage::helper('commision')->__('Delete'),
    //          'url'      => $this->getUrl('*/*/massDelete'),
    //          'confirm'  => Mage::helper('commision')->__('Are you sure?')
    //     ));

    //     $statuses = Mage::getSingleton('commision/status')->getOptionArray();

    //     array_unshift($statuses, array('label'=>'', 'value'=>''));
    //     $this->getMassactionBlock()->addItem('status', array(
    //          'label'=> Mage::helper('commision')->__('Change status'),
    //          'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
    //          'additional' => array(
    //                 'visibility' => array(
    //                      'name' => 'status',
    //                      'type' => 'select',
    //                      'class' => 'required-entry',
    //                      'label' => Mage::helper('commision')->__('Status'),
    //                      'values' => $statuses
    //                  )
    //          )
    //     ));
    //     return $this;
    // }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}