<?php

class VES_VendorsReview_Block_Adminhtml_Review_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('vendorsReviewGrid');
		$this->setDefaultSort('created_time');
		$this->setDefaultDir('DESC');
		$this->setSaveParametersInSession(true);
	}

	protected function _prepareCollection()
	{
		$collection = Mage::getModel('vendorsreview/review')->getCollection();
		if(!Mage::registry('useAdminMode')) {
			$collection->addFieldToFilter('status',VES_VendorsReview_Model_Type::APPROVED)
			->addFieldToFilter('vendor_id',Mage::getSingleton('vendors/session')->getVendorId());
		}
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}

	protected function _prepareColumns()
	{
		if(Mage::registry('useAdminMode')) {
			$this->addColumn('review_id', array(
				'header'    => Mage::helper('vendorsreview')->__('ID'),
				'align'     =>'right',
				'width'     => '50px',
				'index'     => 'review_id',
			));
		}
		
		
		if(Mage::registry('useAdminMode')) {
			$this->addColumn('vendor_id', array(
				'header'    => Mage::helper('vendorsreview')->__('Vendor'),
				'align'     =>'left',
				'width'     => '50px',
				'index'     => 'vendor_id',
				'renderer'	=> new VES_VendorsReview_Block_Widget_Grid_Column_Renderer_Text(),
			));
		}
		
		$outputFormat = Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);
      $this->addColumn('created_time', array(
      		'header'    => Mage::helper('vendorsreview')->__('Created time'),
      		'width'     => '100px',
      		'index'     => 'created_time',
      		'type'     =>'datetime',
      		'format' => $outputFormat,
      		'time' => true,
      		'input_format' => Varien_Date::DATETIME_INTERNAL_FORMAT,
      ));
      
		$this->addColumn('title', array(
				'header'    => Mage::helper('vendorsreview')->__('Title'),
				'align'     =>'left',
				'width'		=>'130px',
				'index'     => 'title',
		));
		
		$this->addColumn('detail', array(
				'header'    => Mage::helper('vendorsreview')->__('Review'),
				'align'     =>'left',
				'width'		=>'230px',
				'index'     => 'detail',
		));
		
		$this->addColumn('rating', array(
				'header'    => Mage::helper('vendorsreview')->__('Rating'),
				'align'     =>'left',
				'width'     => '80px',
				'sortable'	=> false,
				'filter'	=> false,
				'index'     => 'rating',
				'renderer'	=> new VES_VendorsReview_Block_Widget_Grid_Column_Renderer_Rating(),
		));
		
		$this->addColumn('nick_name', array(
				'header'        => Mage::helper('vendorsreview')->__('Nickname'),
				'align'         => 'left',
				'width'         => '100px',
				'index'         => 'nick_name',
				'type'          => 'text',
				'truncate'      => 50,
				'escape'        => true,
		));
		
		if(Mage::registry('useAdminMode')) {
			$this->addColumn('status', array(
					'header'        => Mage::helper('vendorsreview')->__('Status'),
					'align'         => 'left',
					'type'          => 'options',
					'options'       => VES_VendorsReview_Model_Type::toOptionArray(),
					'width'         => '100px',
					'index'         => 'status',
			));
		}
		
		if(Mage::registry('useAdminMode')) {
		$this->addColumn('action',
				array(
						'header'    =>  Mage::helper('vendorsreview')->__('Action'),
						'width'     => '50',
						'type'      => 'action',
						'getter'    => 'getId',
						'actions'   => array(
								array(
										'caption'   => Mage::helper('vendorsreview')->__('Edit'),
										'url'       => array('base'=> '*/*/edit'),
										'field'     => 'id'
								)
						),
						'filter'    => false,
						'sortable'  => false,
						'index'     => 'stores',
						'is_system' => true,
				));
		}
		
		return parent::_prepareColumns();
	}

	protected function _prepareMassaction()
	{
		if(Mage::registry('useAdminMode')) {
			$this->setMassactionIdField('review_id');
			$this->getMassactionBlock()->setFormFieldName('vendorsreview');
			
	// 		$this->getMassactionBlock()->addItem('delete', array(
	// 				'label'    => Mage::helper('productquestion')->__('Delete'),
	// 				'url'      => $this->getUrl('*/*/massDelete'),
	// 				'confirm'  => Mage::helper('productquestion')->__('Are you sure?')
	// 		));
			
			$statuses = Mage::getSingleton('vendorsreview/type')->toOptionArray();
			
			array_unshift($statuses, array('label'=>'', 'value'=>''));
			$this->getMassactionBlock()->addItem('status', array(
					'label'=> Mage::helper('vendorsreview')->__('Change status'),
					'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
					'additional' => array(
							'visibility' => array(
									'name' => 'status',
									'type' => 'select',
									'class' => 'required-entry',
									'label' => Mage::helper('vendorsreview')->__('Status'),
									'values' => $statuses
							)
					)
			));
		}
		return $this;
	}

	public function getRowUrl($row)
	{
		return $this->getUrl('*/*/edit', array('id' => $row->getId()));
	}

}