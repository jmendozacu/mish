<?php

class VES_VendorsQuote_Block_Vendor_Quote_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('vendorsQuoteGrid');
		$this->setDefaultSort('increment_id');
		$this->setDefaultDir('DESC');
		$this->setSaveParametersInSession(true);
	}

	protected function _prepareCollection()
	{
		$collection = Mage::getModel('vendorsquote/quote')->getCollection()
	                  ->addFieldToFilter('vendor_id',Mage::getSingleton('vendors/session')->getVendor()->getId())
		              ->addFieldToFilter('status',array('nin'=>array(VES_VendorsQuote_Model_Quote::STATUS_CREATED)));
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}

	protected function _prepareColumns()
	{
		$this->addColumn('increment_id', array(
			'header'    => Mage::helper('vendors')->__('Quote #'),
			'align'     =>'right',
			'width'     => '50px',
			'index'     => 'increment_id',
		));

		$this->addColumn('created_at', array(
			'header'    => Mage::helper('vendors')->__('Created At'),
			'align'     =>'left',
			'index'     => 'created_at',
		    'width'     => '150px',
			'type'	  => 'datetime',
		));
		$this->addColumn('firstname', array(
			'header'    => Mage::helper('vendors')->__('First Name'),
			'width'     => '100px',
			'index'     => 'firstname',
		));
		$this->addColumn('lastname', array(
			'header'    => Mage::helper('vendors')->__('Last Name'),
			'width'     => '100px',
			'index'     => 'lastname',
		));
		$this->addColumn('email', array(
			'header'    => Mage::helper('vendors')->__('Email'),
			'index'     => 'email',
		));
		if(Mage::helper('vendorsquote')->getConfig('account_detail_company')){
    		$this->addColumn('company', array(
    			'header'    => Mage::helper('vendors')->__('Company'),
    			'width'     => '200px',
    			'index'     => 'company',
    		));
		}
		if(Mage::helper('vendorsquote')->getConfig('account_detail_telephone')){
    		$this->addColumn('telephone', array(
    			'header'    => Mage::helper('vendors')->__('Telephone'),
    			'width'     => '120px',
    			'index'     => 'telephone',
    		));
		}
		if(Mage::helper('vendorsquote')->getConfig('account_detail_taxvat')){
    		$this->addColumn('taxvat', array(
    		    'header'    => Mage::helper('vendors')->__('VAT/Tax Id'),
    		    'width'     => '120px',
    		    'index'     => 'taxvat',
    		));
		}
		$this->addColumn('status', array(
		    'header'    => Mage::helper('vendors')->__('Status'),
		    'align'     => 'left',
		    'width'     => '150px',
		    'index'     => 'status',
		    'type'      => 'options',
		    'options'   => Mage::getModel('vendorsquote/source_status')->getOptionArray(true),
		));
        
		$this->addColumn('action',
		    array(
		        'header'    =>  Mage::helper('vendors')->__('Action'),
		        'width'     => '50',
		        'type'      => 'action',
		        'getter'    => 'getId',
		        'actions'   => array(
		            array(
		                'caption'   => Mage::helper('vendors')->__('View'),
		                'url'       => array('base'=> '*/*/view'),
		                'field'     => 'quote_id'
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
		$this->setMassactionIdField('vendors_id');
		$this->getMassactionBlock()->setFormFieldName('quotes');
		$statuses = Mage::getSingleton('vendorsquote/source_status')->getOptionArray(false);
		
		//array_unshift($statuses, array('label'=>'', 'value'=>''));
		$this->getMassactionBlock()->addItem('status', array(
		    'label'=> Mage::helper('vendorsquote')->__('Change status'),
		    'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
		    'additional' => array(
		        'visibility' => array(
		            'name' => 'status',
		            'type' => 'select',
		            'class' => 'required-entry',
		            'label' => Mage::helper('vendorsquote')->__('Status'),
		            'values' => $statuses
		        )
		    )
		));
		
		return $this;
	}

	public function getRowUrl($row)
	{
		return $this->getUrl('*/*/view', array('quote_id' => $row->getId()));
	}

}