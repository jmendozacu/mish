<?php

class VES_VendorsCms_Block_Vendor_App_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('vendorsAppGrid');
      $this->setDefaultSort('sort_order');
      $this->setDefaultDir('ASC');
  }

  protected function _prepareCollection()
  {
  	  $vendorId = Mage::getSingleton('vendors/session')->getVendorId();
      $collection = Mage::getModel('vendorscms/app')->getCollection()
      				->addFieldToFilter('vendor_id',$vendorId)
      				;
      $this->setCollection($collection);
      return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
  }

	protected function _prepareColumns()
    {
        $baseUrl = $this->getUrl();

        $this->addColumn('app_id', array(
            'header'    => Mage::helper('vendorscms')->__('Frontend App ID'),
            'align'     => 'left',
            'index'     => 'app_id',
        	'width'		=> 100,
        ));

        $this->addColumn('title', array(
            'header'    => Mage::helper('vendorscms')->__('Frontend App Instance Title'),
            'align'     => 'left',
            'index'     => 'title'
        ));

        $this->addColumn('type', array(
            'header'    => Mage::helper('vendorscms')->__('Type'),
            'index'     => 'type',
            'type'      => 'options',
            'options'   => Mage::getSingleton('vendorscms/source_app_type')->getOptions(),
        ));

		$this->addColumn('sort_order', array(
            'header'    => Mage::helper('vendorscms')->__('Sort Order'),
            'index'     => 'sort_order',
			'width'		=> 100,
        ));
        return parent::_prepareColumns();
    }
    
	/**
     * Row click url
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('app_id' => $row->getId()));
    }
}