<?php

class VES_VendorsRma_Block_Vendor_Request_Edit_Renderer_Items extends Mage_Adminhtml_Block_Sales_Order_View_Items
{
    protected $_qty;
    /**
     * Retrieve required options from parent
     */
    protected function _beforeToHtml()
    {
        Mage_Adminhtml_Block_Sales_Items_Abstract::_beforeToHtml();
    }

    public function getRequestRma(){
    	
    	if(!$this->getData('request')){
    		$this->setData('request',Mage::registry('current_request'));
    	}
    	return $this->getData('request');

    }

    public function getOrder(){
        $order = Mage::getModel("sales/order")->loadByIncrementId($this->getRequestRma()->getData("order_incremental_id"));
        if($order->getId()) return $order;
        return null;
    }

    public function allowPerItem(){
        if (!Mage::app()->getStore()->isAdmin()) {
            return false;
        }
        return Mage::helper("vendorsrma/config")->allowPerOrder();
    }


    /**
     * Retrieve order items collection
     *
     * @return unknown
     */


    public function getItemsCollection()
    {
        $items = array();
        if(!$this->getRequestRma()->getId()) {
            $colletions =  $this->getOrder()->getItemsCollection();
            foreach($colletions as $item){
                $models = Mage::getModel("vendorsrma/item")->getCollection()->addFieldToFilter("order_item_id",$item->getId());
                $qty_rma_old = 0;
                $rmas = null;
                $options = Mage::getModel("vendorsrma/status")->getOptions();
                foreach($models as $rma_item){
                    $request = Mage::getModel("vendorsrma/request")->load($rma_item->getRequestId());
                    if($request->getData("status") == $options[3]["value"]) continue;
                    /* check request not complete */
                    $qty_rma_old +=   $rma_item->getQty();

                    $rmas[] = array($request->getId() => $request->getData("increment_id"));
                }
                if($qty_rma_old == 0){
                    $qty = $item->getData("qty_shipped") - $item->getData("qty_refunded") > 0 ? $item->getData("qty_shipped") - $item->getData("qty_refunded") : 0;
                    $item->setData("qty_rma",$qty);
                }
                else{
                    $qty = $item->getData("qty_shipped") - $item->getData("qty_refunded") - $qty_rma_old > 0 ? $item->getData("qty_shipped") - $item->getData("qty_refunded") - $qty_rma_old : 0;
                    $item->setData("qty_rma",$qty);
                    $item->setData("request_rma",$rmas);
                    $item->setData("allow_per_item_order",$this->allowPerItem());
                }

                $items[] = $item;
            }
        }
        else{
            $colletions = Mage::getModel("vendorsrma/item")->getCollection()->addFieldToFilter("request_id",$this->getRequestRma()->getId());

            $qty = null;
            foreach($colletions as $item){
                $model = Mage::getModel('sales/order_item')->load($item->getData("order_item_id"));
                if(!$model->getId()) continue;

                $models = Mage::getModel("vendorsrma/item")->getCollection()->addFieldToFilter("order_item_id",$model->getId());
                $rmas = null;
                foreach($models as $rma_item){
                    $request = Mage::getModel("vendorsrma/request")->load($rma_item->getRequestId());
                    if($request->getId() != $this->getRequestRma()->getId())
                    $rmas[] = array($request->getId() => $request->getData("increment_id"));
                }

                if($rmas){
                    $model->setData("request_rma",$rmas);
                }
                $model->setData("qty_rma",$item->getQty());
                $model->setData("allow_per_item_order",$this->allowPerItem());
                $items[] = $model;
            }
        }

        return $items;
    }


    public function setQtyRma($qty){
        $this->_qty = $qty;
    }

    /**
     * get Qty RMA
     */

    public function getAllQtyRma(){
        if(!$this->getRequestRma()->getId()) return false;
        return $this->_qty;
    }
}
