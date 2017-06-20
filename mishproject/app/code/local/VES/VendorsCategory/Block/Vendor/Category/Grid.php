<?php

class VES_VendorsCategory_Block_Vendor_Category_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('vendorsCategoryGrid');
		$this->_pagerVisibility = false;
		//$this->setDefaultSort('category_id');
		//$this->setDefaultDir('DESC');
		$this->setSaveParametersInSession(true);
	}

	protected function _prepareCollection()
	{
		$vendorId = Mage::getSingleton('vendors/session')->getVendorId();
		$collection = Mage::getModel('vendorscategory/category')->getCollection()->addFieldToFilter('vendor_id',$vendorId);
		
		$newCollection = new Varien_Data_Collection();
		$items = Mage::getModel('vendorscategory/source_category')->getCatGridCollection($vendorId);
		foreach($items as $_item) {
			$newCollection->addItem($_item);
		}
		$this->setCollection($newCollection);
		return parent::_prepareCollection();
	}

	protected function _prepareColumns()
	{
		$this->addColumn('category_id', array(
			'header'    => Mage::helper('vendorscategory')->__('Category ID'),
			'align'     =>'right',
			'width'     => '50px',
			'index'     => 'category_id',
			'sortable'	=> false,
			'filter'	=> false,
		));

		$this->addColumn('name', array(
			'header'    => Mage::helper('vendorscategory')->__('Name'),
			'align'     =>'left',
			'index'     => 'name',
			'sortable'	=> false,
			'filter'	=> false,
			'renderer'	=> new VES_VendorsCategory_Block_Vendor_Widget_Grid_Column_Renderer_Text(),
			'vendor_id'	=> Mage::getSingleton('vendors/session')->getVendorId(),
		));

		$this->addColumn('is_active', array(
			'header'    => Mage::helper('vendorscategory')->__('Is Active'),
			'align'     =>'left',
			'width'     => '50px',
			'type'		=> 'options',
			'options'	=> Mage::getModel('adminhtml/system_config_source_yesno')->toArray(),
			'index'     => 'is_active',
			'sortable'	=> false,
			'filter'	=> false,
		));
		
		$this->addColumn('sort_order', array(
				'header'    => Mage::helper('vendorscategory')->__('Sort order'),
				'align'     =>'left',
				'index'     => 'sort_order',
				'sortable'	=> false,
				'filter'	=> false,
				'width'     => '50px',
		));

		return parent::_prepareColumns();
	}

	protected function _prepareMassaction()
	{
		$this->setMassactionIdField('category_id');
		$this->getMassactionBlock()->setFormFieldName('vendorscategory');

		$this->getMassactionBlock()->addItem('delete', array(
			'label'    => Mage::helper('vendorscategory')->__('Delete'),
			'url'      => $this->getUrl('*/*/massDelete'),
			'confirm'  => Mage::helper('vendorscategory')->__('Are you sure?')
		));
		return $this;
	}

	public function getRowUrl($row)
	{
		return $this->getUrl('*/*/edit', array('id' => $row->getId()));
	}

}