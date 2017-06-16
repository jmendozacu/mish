<?php

class OTTO_AdvancedFaq_Block_Adminhtml_Category_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('categoryGrid');
      $this->setDefaultSort('sort_order');
      $this->setDefaultDir('DESC');
      //$this->setUseAjax(true);
      $this->setSaveParametersInSession(true);
  }

  
  protected function _prepareCollection()
  {
      $collection = Mage::getModel('advancedfaq/category')->getCollection();
      if(Mage::app()->getStore()->isAdmin()){
      	$collection->getSelect()->joinLeft(array('seller_tbl'=>$collection->getTable('sellers/seller')),'main_table.seller_id=entity_id',array('seller'=>'seller_id'));
      }
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('category_id', array(
          'header'    => Mage::helper('advancedfaq')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'category_id',
      ));
	  if(Mage::app()->getStore()->isAdmin()){
	  	$this->addColumn('seller', array(
          'header'    => Mage::helper('advancedfaq')->__('Seller'),
          'align'     =>'left',
	  	  'width'     => '90',
          'index'     => 'seller',
      	));
	  }
      $this->addColumn('title', array(
          'header'    => Mage::helper('advancedfaq')->__('Title'),
          'align'     =>'left',
          'index'     => 'title',
      ));
      
      $this->addColumn('url_key', array(
      		'header'    => Mage::helper('advancedfaq')->__('Url key'),
      		'align'     =>'left',
      		'index'     => 'url_key',
      ));

    
      if (!Mage::app()->isSingleStoreMode() && Mage::app()->getStore()->isAdmin()) {
      	$this->addColumn('store_id', array(
      			'header'        => Mage::helper('cms')->__('Store View'),
      			'index'         => 'store_id',
      			'type'          => 'store',
      			'store_all'     => true,
      			'store_view'    => false,
      			'sortable'      => false,
      			'filter' => false,
      			'renderer'=>new OTTO_AdvancedFaq_Block_Adminhtml_Category_Widget_Grid_Column_Renderer_Store(),
      	));
      }
      	/*
      $this->addColumn('store_id', array(
      		'header'    => Mage::helper('kbase')->__('Store'),
      		'align'     =>'left',
      		'index'     => 'store_id',
      		'renderer'=>new OTTO_Kbase_Block_Adminhtml_Category_Widget_Grid_Column_Renderer_Store(),
      ));
       */
      $this->addColumn('sort_order', array(
      		'header'    => Mage::helper('advancedfaq')->__('Sort'),
      		'align'     => 'left',
      		'width'     => '80px',
      		'index'     => 'sort_order',
      ));
      
      $this->addColumn('status', array(
          'header'    => Mage::helper('advancedfaq')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => OTTO_AdvancedFaq_Model_Status::getOptionShowArray(),
      ));
	  
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('advancedfaq')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('advancedfaq')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('advancedfaq')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('advancedfaq')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('category_id');
        $this->getMassactionBlock()->setFormFieldName('category');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('advancedfaq')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('advancedfaq')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('advancedfaq/status')->getOptionShowArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('advancedfaq')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('advancedfaq')->__('Status'),
                         'values' => $statuses
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