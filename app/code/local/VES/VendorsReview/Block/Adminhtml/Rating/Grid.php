<?php

class VES_VendorsReview_Block_Adminhtml_Rating_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('vendorsRatingGrid');
		$this->setDefaultSort('title');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
	}

	protected function _prepareCollection()
	{
		$collection = Mage::getModel('vendorsreview/rating')->getCollection();
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}

	protected function _prepareColumns()
	{
		$this->addColumn('rating_id', array(
			'header'    => Mage::helper('vendorsreview')->__('ID'),
			'align'     =>'right',
			'width'     => '50px',
			'index'     => 'rating_id',
		));

		$this->addColumn('title', array(
			'header'    => Mage::helper('vendorsreview')->__('Rating Name'),
			'align'     =>'left',
			'index'     => 'title',
		));

		$this->addColumn('position', array(
				'header'    => Mage::helper('vendorsreview')->__('Sort Order'),
				'align'     =>'left',
				'width'		=>'50px',
				'index'     => 'position',
		));
		
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