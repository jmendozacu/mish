<?php
/**
 * Adminhtml sales orders grid
 *
 * @category   	VES
 * @package    	VES_Vendors
 * @author    	Vnecoms Team <support@vnecoms.com>
 */
class VES_VendorsSales_Block_Vendor_Sales_Order_Grid extends Mage_Adminhtml_Block_Sales_Order_Grid
{

    public function __construct()
    {
        parent::__construct();
    }

    protected function _prepareCollection()
    {
        $vendorId = Mage::getSingleton('vendors/session')->getVendor()->getId();
        if(Mage::helper('vendors')->isAdvancedMode()){
    		$collection = Mage::getResourceModel($this->_getCollectionClass());
			$collection->getSelect()->where('main_table.vendor_id=?',$vendorId);
        }else{
        	$collection = Mage::getResourceModel('sales/order_item_collection');
        	$collection->getSelect()->columns(array('base_grand_total'=>'sum(base_row_total)','grand_total'=>'sum(row_total)'))
        	->group('order_id')
        	->join(array('order_table'=>$collection->getTable('sales/order_grid')),'order_id=entity_id',array('increment_id','status','billing_name','shipping_name','order_currency_code','base_currency_code'))
        	->where('main_table.vendor_id=?',$vendorId);
        }
        $this->setCollection($collection);
        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        parent::_prepareColumns();
        
    	if (!Mage::app()->isSingleStoreMode()) {
    		$this->removeColumn('store_id');
        }
        
        unset($this->_rssLists);
        return Mage_Adminhtml_Block_Widget_Grid::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
    	if(Mage::helper('vendors')->isAdvancedMode()) {
    		parent::_prepareMassaction();
			
			if (Mage::getStoreConfig('vendors/sales/view_cancel')) {
                $this->getMassactionBlock()->addItem('cancel_order', array(
                    'label'=> Mage::helper('sales')->__('Cancel'),
                    'url'  => $this->getUrl('*/sales_order/massCancel'),
                ));
            }

            if (Mage::getStoreConfig('vendors/sales/view_hold')) {
                $this->getMassactionBlock()->addItem('hold_order', array(
                    'label'=> Mage::helper('sales')->__('Hold'),
                    'url'  => $this->getUrl('*/sales_order/massHold'),
                ));
            }

            if (Mage::getStoreConfig('vendors/sales/view_hold')) {
                $this->getMassactionBlock()->addItem('unhold_order', array(
                    'label'=> Mage::helper('sales')->__('Unhold'),
                    'url'  => $this->getUrl('*/sales_order/massUnhold'),
                ));
            }

			
			
    		$this->getMassactionBlock()->removeItem('print_shipping_label');
    		return $this;
    	}
    	
    	return Mage_Adminhtml_Block_Widget_Grid::_prepareMassaction();
    }

    public function getRowUrl($row)
    {
    	$orderId = Mage::helper('vendors')->isAdvancedMode()?$row->getId():$row->getOrderId();
		return $this->getUrl('*/sales_order/view', array('order_id' => $orderId));
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

}
