<?php

class VES_VendorsSubAccount_Block_Vendor_Role_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('vendorsroletGrid');
		$this->setDefaultSort('account_id');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
	}

	protected function _prepareCollection()
	{
		$collection = Mage::getModel('vendorssubaccount/role')->getCollection()->addFieldToFilter('vendor_id',Mage::getSingleton('vendors/session')->getVendor()->getId());
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}
	
	protected function _prepareColumns()
	{
	
		$this->addColumn('role_name', array(
			'header'    => Mage::helper('vendorssubaccount')->__('Role Name'),
			'align'     =>'left',
			'index'     => 'role_name',
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