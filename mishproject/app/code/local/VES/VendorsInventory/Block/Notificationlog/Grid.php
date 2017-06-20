<?php

class VES_VendorsInventory_Block_Notificationlog_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() { 
        parent::__construct();
        $this->setId('notificationlogGrid');
        $this->setDefaultSort('send_email_log_id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
    }

    /**
     * prepare collection for block to display
     *
     * @return Magestore_Inventorylowstock_Block_Adminhtml_Notificationlog_Grid
     */
    protected function _prepareCollection() {
        $collection = Mage::getModel('inventorylowstock/sendemaillog')->getCollection();
        $this->setCollection($collection);        
        return parent::_prepareCollection();
    }

    /**
     * prepare columns for this grid
     *
     * @return Magestore_Inventorylowstock_Block_Adminhtml_Notificationlog_Grid
     */
    protected function _prepareColumns() {
        $this->addColumn('send_email_log_id', array(
            'header' => Mage::helper('vendorsinventory')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'type' => 'number',
            'index' => 'send_email_log_id',
        ));

        $this->addColumn('sent_at', array(
            'header' => Mage::helper('vendorsinventory')->__('Sent at'),
            'align' => 'left',
            'width' => '250px',
            'index' => 'sent_at',
            'type' => 'datetime',
        ));

        $this->addColumn('type', array(
            'header' => Mage::helper('vendorsinventory')->__('Type'),
            'index' => 'type',
            'width' => '50px',
            'type' => 'options',
            'options' => Mage::helper('inventorylowstock')->getTypeList(),
        ));
        
        $this->addColumn('email_received', array(
            'header' => Mage::helper('vendorsinventory')->__('Email Received'),
            'index' => 'email_received',
            'width' => '50px',
            'type' => 'text',
        ));
        
        
        $this->addColumn('manager_name', array(
            'header' => Mage::helper('vendorsinventory')->__('Recipients'),
            'index' => 'manager_name',
            'width' => '50px',
            'type' => 'text',
        ));
        
        $this->addColumn('action',
            array(
                'header'    =>    Mage::helper('vendorsinventory')->__('Action'),
                'width'        => '100',
                'type'        => 'action',
                'getter'    => 'getId',
                'actions'    => array(
                    array(
                        'caption'    => Mage::helper('vendorsinventory')->__('View'),
                        'url'        => array('base'=> '*/*/view'),
                        'field'        => 'id'
                    )),
                'filter'    => false,
                'sortable'    => false,
                'index'        => 'stores',
                'is_system'    => true,
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('vendorsinventory')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('vendorsinventory')->__('XML'));

        return parent::_prepareColumns();
    }

    /**
     * get url for each row in grid
     *
     * @return string
     */
    public function getRowUrl($row) {
        return false;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid');
    }

}