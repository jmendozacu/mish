<?php
class IWD_OrderManager_Block_Adminhtml_Sales_Order_View_Tab_Shipments extends Mage_Adminhtml_Block_Sales_Order_View_Tab_Shipments
{
    protected function _getCollectionClass()
    {
        $order = $this->getOrder();
        $is_archived = Mage::getModel('iwd_ordermanager/order')->isArchived($order);
        if($is_archived){
            return 'iwd_ordermanager/archive_shipment_collection';
        }
        return 'sales/order_shipment_grid_collection';
    }
}
