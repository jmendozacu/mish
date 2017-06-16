<?php

class VES_VendorsReview_Block_Adminhtml_Dashboard_Review_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('vendorsReviewGrid');
		$this->setDefaultSort('created_time');
		$this->setDefaultDir('DESC');
		
		$this->setPagerVisibility(false);
		$this->setFilterVisibility(false);
	}

	protected function _prepareCollection()
	{
		$collection = Mage::getModel('vendorsreview/review')->getCollection()
		->addFieldToFilter('status',VES_VendorsReview_Model_Type::APPROVED)
		->addFieldToFilter('vendor_id',Mage::getSingleton('vendors/session')->getVendorId());
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}

	protected function _prepareColumns()
	{
		$outputFormat = Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);
      	$this->addColumn('created_time', array(
      		'header'    => Mage::helper('vendorsreview')->__('Created time'),
      		'width'     => '120px',
      		'index'     => 'created_time',
      		'type'     =>'datetime',
      		'format' => $outputFormat,
      		'time' => true,
      		'sortable'  => false,
      		'filter'	=> false,
      		'input_format' => Varien_Date::DATETIME_INTERNAL_FORMAT,
      	));
      
		$this->addColumn('title', array(
			'header'    => Mage::helper('vendorsreview')->__('Title'),
			'align'     =>'left',
			'width'		=>'130px',
			'index'     => 'title',
			'sortable'  => false,
			'filter'	=> false,
		));
		
		$this->addColumn('detail', array(
			'header'    => Mage::helper('vendorsreview')->__('Review'),
			'align'     =>'left',
			'width'		=>'230px',
			'index'     => 'detail',
			'sortable'  => false,
			'filter'	=> false,
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
			'header'        => Mage::helper('vendorsreview')->__('Customer Name'),
			'align'         => 'left',
			'width'         => '100px',
			'index'         => 'nick_name',
			'type'          => 'text',
			'truncate'      => 50,
			'escape'        => true,
			'sortable'  => false,
			'filter'	=> false,
		));

		return parent::_prepareColumns();
	}

	protected function _prepareMassaction()
	{
		return $this;
	}

	public function getRowUrl($row)
	{
		return "#";
	}

}