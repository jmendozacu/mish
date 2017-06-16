<?php
class Mirasvit_Rma_Block_Adminhtml_Rma_Create_Order_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function _construct()
    {
        parent::_construct();
        $this->setId('rma_rma_create_order_grid');
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::helper('rma/mage')->getOrderCollection();
        if (Mage::getVersion() >= '1.4.1.1') {
            $collection->addFieldToFilter('main_table.status', array('in' => array('complete')));
        } else {
            $collection->addFieldToFilter('status', array('in' => array('complete')));
        }
        $this->setCollection($collection);
        // echo $collection->getSelect();die;
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('real_order_id', array(
            'header'=> Mage::helper('sales')->__('Order #'),
            'width' => '80px',
            'type'  => 'text',
            'index' => 'increment_id',
            'filter_index' => 'main_table.increment_id'
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'    => Mage::helper('sales')->__('Purchased From (Store)'),
                'index'     => 'store_id',
                'type'      => 'store',
                'store_view'=> true,
                'display_deleted' => true,
            ));
        }

        $this->addColumn('created_at', array(
            'header' => Mage::helper('sales')->__('Purchased On'),
            'index' => 'created_at',
            'type' => 'datetime',
            'width' => '100px',
        ));

        $this->addColumn('billing_name', array(
            'header' => Mage::helper('sales')->__('Bill to Name'),
            'index' => 'billing_name',
        ));

        $this->addColumn('shipping_name', array(
            'header' => Mage::helper('sales')->__('Ship to Name'),
            'index' => 'shipping_name',
        ));

        $this->addColumn('base_grand_total', array(
            'header' => Mage::helper('sales')->__('G.T. (Base)'),
            'index' => 'base_grand_total',
            'type'  => 'currency',
            'currency' => 'base_currency_code',
        ));

        $this->addColumn('grand_total', array(
            'header' => Mage::helper('sales')->__('G.T. (Purchased)'),
            'index' => 'grand_total',
            'type'  => 'currency',
            'currency' => 'order_currency_code',
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/add', array('order_id'=>$row->getId(), 'ticket_id'=> Mage::app()->getRequest()->getParam('ticket_id')));
    }

}
