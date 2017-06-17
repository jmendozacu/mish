<?php

class OTTO_AdvancedFaq_Block_Adminhtml_Faq_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('faqGrid');
      $this->setDefaultSort('sort_order');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
  	  $tbl_faq_item = Mage::getSingleton('core/resource')->getTableName('advancedfaq/category');
  	  $collection = Mage::getModel('advancedfaq/faq')->getCollection();
  	  if(Mage::app()->getStore()->isAdmin()){
      	$collection->getSelect()->joinLeft(array('seller_tbl'=>$collection->getTable('sellers/seller')),'main_table.seller_id=entity_id',array('seller'=>'seller_id'));
      }
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('faq_id', array(
          'header'    => Mage::helper('advancedfaq')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'faq_id',
      ));
      if(Mage::app()->getStore()->isAdmin()){
	  	$this->addColumn('seller', array(
          'header'    => Mage::helper('advancedfaq')->__('Seller'),
          'align'     =>'left',
	  	  'width'     => '90',
          'index'     => 'seller',
      	));
	  }
      $this->addColumn('created_time', array(
      		'header'    => Mage::helper('advancedfaq')->__('Created Time'),
      		'width'     => '150px',
      		'type' =>  "datetime",
      		'index'     => 'created_time',
      ));
      $this->addColumn('question', array(
          'header'    => Mage::helper('advancedfaq')->__('Title'),
          'align'     =>'left',
          'index'     => 'question',
      ));
	
     
      $this->addColumn('category_id', array(
      		'header'    => Mage::helper('advancedfaq')->__('Topic'),
      		'align'     =>'left',
      		'index'     => 'category_id',
      		 'type'      => 'options',
          	'options'   => Mage::getModel('advancedfaq/category')->getCategoryOpTion(),
      		'filter_condition_callback' => array($this, '_filterSupplierCallback'),
      		'renderer'=>new OTTO_AdvancedFaq_Block_Adminhtml_Faq_Renderer_Category()    
      ));
      
 
   
      $this->addColumn('sort_order', array(
      		'header'    => Mage::helper('advancedfaq')->__('Sort'),
      		'align'     => 'left',
      		'width'     => '80px',
      		'index'     => 'sort_order',
      ));
      $this->addColumn('show_on', array(
      		'header'    => Mage::helper('advancedfaq')->__('Show on main page'),
      		'align'     => 'left',
      		'width'     => '80px',
      		'index'     => 'show_on',
      		'type'      => 'options',
      		'options'   => OTTO_AdvancedFaq_Model_Status::getOptionShowArray()
      ));
       
      $this->addColumn('status', array(
          'header'    => Mage::helper('advancedfaq')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => OTTO_AdvancedFaq_Model_Status::getOptionArray()
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
        $this->setMassactionIdField('kbase_id');
        $this->getMassactionBlock()->setFormFieldName('faq');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('advancedfaq')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('advancedfaq')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('advancedfaq/status')->getOptionArray();

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
        
        
        $statuses = Mage::getSingleton('advancedfaq/status')->getOptionShowArray();
        
        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('show_on', array(
        		'label'=> Mage::helper('advancedfaq')->__('Show on main page'),
        		'url'  => $this->getUrl('*/*/massShow', array('_current'=>true)),
        		'additional' => array(
        				'visibility' => array(
        						'name' => 'show_on',
        						'type' => 'select',
        						'class' => 'required-entry',
        						'label' => Mage::helper('advancedfaq')->__('Show on main page'),
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
  
  
  protected function _filterStoreCondition($collection, $column)
  {
  	 
  	if (!$store = $column->getFilter()->getValue()) {
  		return;
  	}
  	if ($store instanceof Mage_Core_Model_Store) {
  		$store = array($store->getId());
  	}
  	
  	if (!is_array($store)) {
  		$store = array($store);
  	}
  	
  	 $this->getCollection()->addFieldToFilter("store_id",array('finset'=>$store));
  	
  
  	//$this->setCollection($collection);
  // var_dump($this->getCollection()->getData());
  	return $this;
  }
  
  public function _filterSupplierCallback($collection, $column) {
  	if (!$value = $column->getFilter()->getValue()) {
  		return $this;
  	}
  	if (!is_array($value)) {
  		$value = array($value);
  	}

  	$this->getCollection()->addFieldToFilter('category_id', array('finset' => $value));
  	return $this;
  }
}