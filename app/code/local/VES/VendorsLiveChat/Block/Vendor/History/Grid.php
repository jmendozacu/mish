<?php

class VES_VendorsLiveChat_Block_Vendor_History_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('historyGrid');
        $this->setDefaultSort('created_time');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $vendor_id = Mage::getSingleton('vendors/session')->getVendor()->getId();
        $collection = Mage::getModel('vendorslivechat/session')->getCollection()->addFieldToFilter("vendor_id",$vendor_id);
        
        Mage::dispatchEvent("vendor_history_livechat_grid_prepare_colletion_before", array("collection" => $collection));
        $this->setCollection($collection);
       
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('session_id', array(
            'header'    => Mage::helper('vendorslivechat')->__('ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'session_id',
        ));

        $this->addColumn('name', array(
            'header'    => Mage::helper('vendorslivechat')->__('Name'),
            'align'     =>'left',
            'width'     => '150px',
            'index'     => 'name',
        ));


        $this->addColumn('email', array(
            'header'    => Mage::helper('vendorslivechat')->__('Email'),
            'index'     => 'email',
        ));

        $this->addColumn('created_time', array(
            'header'    => Mage::helper('vendorslivechat')->__('Created time'),
            'index'     => 'created_time',
            "type"=>"datetime"
        ));



        $this->addExportType('*/*/exportCsv', Mage::helper('vendorslivechat')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('vendorslivechat')->__('XML'));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/view', array('id' => $row->getId()));
    }

}