<?php

class VES_VendorsLiveChat_Block_Vendor_Contact_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('contactGrid');
        $this->setDefaultSort('created_time');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $vendor_id = Mage::getSingleton('vendors/session')->getVendor()->getId();
        $collection = Mage::getModel('vendorslivechat/contact')->getCollection()->addFieldToFilter("vendor_id",$vendor_id);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('contact_id', array(
            'header'    => Mage::helper('vendorslivechat')->__('ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'contact_id',
        ));

        $this->addColumn('name', array(
            'header'    => Mage::helper('vendorslivechat')->__('Name'),
            'align'     =>'left',
            'width'     => '150px',
            'index'     => 'name',
        ));


        $this->addColumn('email', array(
            'header'    => Mage::helper('vendorslivechat')->__('Email'),
            'width'     => '150px',
            'index'     => 'email',
        ));

        $this->addColumn('question', array(
              'header'    => Mage::helper('vendorslivechat')->__('Question'),
              'index'     => 'question',
        ));
        $this->addColumn('created_time', array(
            'header'    => Mage::helper('vendorslivechat')->__('Created time'),
            'index'     => 'created_time',
            "type"=>"datetime"
        ));
        /*
        $this->addColumn('status', array(
            'header'    => Mage::helper('vendorslivechat')->__('Status'),
            'align'     => 'left',
            'width'     => '80px',
            'index'     => 'status',
            'type'      => 'options',
            'options'   => array(
                1 => 'Enabled',
                2 => 'Disabled',
            ),
        ));
        */
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('vendorslivechat')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('vendorslivechat')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
            ));

        $this->addExportType('*/*/exportCsv', Mage::helper('vendorslivechat')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('vendorslivechat')->__('XML'));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('contact_id');
        $this->getMassactionBlock()->setFormFieldName('contact');

        $this->getMassactionBlock()->addItem('delete', array(
            'label'    => Mage::helper('vendorslivechat')->__('Delete'),
            'url'      => $this->getUrl('*/*/massDelete'),
            'confirm'  => Mage::helper('vendorslivechat')->__('Are you sure?')
        ));
        /*
        $statuses = Mage::getSingleton('vendorslivechat/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
            'label'=> Mage::helper('vendorslivechat')->__('Change status'),
            'url'  => $this->getUrl('massStatus', array('_current'=>true)),
            'additional' => array(
                'visibility' => array(
                    'name' => 'status',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => Mage::helper('vendorslivechat')->__('Status'),
                    'values' => $statuses
                )
            )
        ));
        */
        return $this;
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}