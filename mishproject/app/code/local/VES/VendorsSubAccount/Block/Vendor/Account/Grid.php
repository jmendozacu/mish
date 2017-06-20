<?php

class VES_VendorsSubAccount_Block_Vendor_Account_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('vendorssubaccountGrid');
		$this->setDefaultSort('account_id');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
	}

	protected function _prepareCollection()
	{
		$collection = Mage::getModel('vendorssubaccount/account')->getCollection()->addFieldToFilter('vendor_id',Mage::getSingleton('vendors/session')->getVendor()->getId());
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}
	
	protected function _prepareColumns()
	{
	
		$this->addColumn('username', array(
			'header'    => Mage::helper('vendorssubaccount')->__('User Name'),
			'align'     =>'left',
			'index'     => 'username',
		));
		
		$this->addColumn('firstname', array(
			'header'    => Mage::helper('vendorssubaccount')->__('First Name'),
			'align'     =>'left',
			'index'     => 'firstname',
		));
		$this->addColumn('lastname', array(
			'header'    => Mage::helper('vendorssubaccount')->__('Last Name'),
			'align'     =>'left',
			'index'     => 'lastname',
		));
		$this->addColumn('email', array(
			'header'    => Mage::helper('vendorssubaccount')->__('Email'),
			'align'     =>'left',
			'index'     => 'email',
		));
		$this->addColumn('telephone', array(
			'header'    => Mage::helper('vendorssubaccount')->__('Telephone'),
			'align'     =>'left',
			'index'     => 'telephone',
		));
		$this->addColumn('status', array(
			'header'    => Mage::helper('vendorssubaccount')->__('Status'),
			'align'     => 'left',
			'width'     => '80px',
			'index'     => 'status',
			'type'      => 'options',
			'options'   => Mage::getModel('vendorssubaccount/status')->getOptionArray(),
		));
	
		$this->addColumn('action',
			array(
				'header'    =>  Mage::helper('vendorssubaccount')->__('Action'),
				'width'     => '100',
				'type'      => 'action',
				'getter'    => 'getId',
				'actions'   => array(
						array(
						'caption'   => Mage::helper('vendorssubaccount')->__('Edit'),
						'url'       => array('base'=> '*/*/edit'),
						'field'     => 'id'
					)
				),
			'filter'    => false,
			'sortable'  => false,
			'index'     => 'stores',
			'is_system' => true,
		));
	
	
		return parent::_prepareColumns();
	}

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('account_id');
        $this->getMassactionBlock()->setFormFieldName('vendorssubaccount');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('vendorssubaccount')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('vendorssubaccount')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('vendorssubaccount/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('vendorssubaccount')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('vendorssubaccount')->__('Status'),
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