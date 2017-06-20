<?php

class FME_Geoipultimatelock_Block_Adminhtml_Geoipultimatelock_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('geoipultimatelockGrid');
        $this->setDefaultSort('geoipultimatelock_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _getStore() {

        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }

    protected function _prepareCollection() {
        $collection = Mage::getModel('geoipultimatelock/geoipultimatelock')->getCollection();
        $store = $this->_getStore();
        if ($store->getId()) {
            $collection->addStoreFilter($store->getId());
        }
        // setting stores to array from string
        foreach ($collection as $i) {
            $i->setStores(explode(',', $i->getStores()));
        }
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn('geoipultimatelock_id', array(
            'header' => Mage::helper('geoipultimatelock')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'geoipultimatelock_id',
        ));

        $this->addColumn('title', array(
            'header' => Mage::helper('geoipultimatelock')->__('Title'),
            'align' => 'left',
            'index' => 'title',
        ));
        /*
        $this->addColumn('notes', array(
            'header' => Mage::helper('geoipultimatelock')->__('Message'),
            'align' => 'left',
            'index' => 'notes',
            'type' => 'text',
            'format' => 'text'
        ));
        */
        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header' => Mage::helper('geoipultimatelock')->__('Store View'),
                'index' => 'stores',
                'type' => 'store',
                'store_all' => true,
                'store_view' => true,
                'sortable' => false,
                'filter' => false,
                'default_value' => 'Global'
            ));
        }

        $this->addColumn('priority', array(
            'header' => Mage::helper('geoipultimatelock')->__('Priority'),
            'align' => 'left',
            'index' => 'priority',
            'width' => '50px'
        ));

        $this->addColumn('status', array(
            'header' => Mage::helper('geoipultimatelock')->__('Status'),
            'align' => 'left',
            'width' => '80px',
            'index' => 'status',
            'type' => 'options',
            'options' => array(
                1 => 'Enabled',
                2 => 'Disabled',
            ),
        ));

        $this->addColumn('action', array(
            'header' => Mage::helper('geoipultimatelock')->__('Action'),
            'width' => '100',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('geoipultimatelock')->__('Edit'),
                    'url' => array('base' => '*/*/edit'),
                    'field' => 'id'
                )
            ),
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
            'is_system' => true,
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('geoipultimatelock')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('geoipultimatelock')->__('XML'));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction() {
        $this->setMassactionIdField('geoipultimatelock_id');
        $this->getMassactionBlock()->setFormFieldName('geoipultimatelock');

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('geoipultimatelock')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('geoipultimatelock')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('geoipultimatelock/status')->getOptionArray();

        array_unshift($statuses, array('label' => '', 'value' => ''));
        $this->getMassactionBlock()->addItem('status', array(
            'label' => Mage::helper('geoipultimatelock')->__('Change status'),
            'url' => $this->getUrl('*/*/massStatus', array('_current' => true)),
            'additional' => array(
                'visibility' => array(
                    'name' => 'status',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => Mage::helper('geoipultimatelock')->__('Status'),
                    'values' => $statuses
                )
            )
        ));
        return $this;
    }

    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}
