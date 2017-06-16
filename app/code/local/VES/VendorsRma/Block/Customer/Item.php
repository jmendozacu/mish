<?php

class VES_VendorsRma_Block_Customer_Item extends Mage_Sales_Block_Order_Items
{
    protected $_qty;

    public function getOrder(){
        return Mage::registry("order");
    }

    public function allowPerItem(){
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
            
            if( $this->getOrder()->getStatus() == Mage_Sales_Model_Order::STATE_COMPLETE){
                if($qty_rma_old == 0){
                    $qty = $item->getData("qty_shipped") - $item->getData("qty_refunded") > 0 ? $item->getData("qty_shipped") - $item->getData("qty_refunded") : 0;
                    $item->setData("qty_rma",(int)$qty);
                    $item->setData("allow_per_item_order",$this->allowPerItem());
                }
                else{
                    $qty = $item->getData("qty_shipped") - $item->getData("qty_refunded") - $qty_rma_old > 0 ? $item->getData("qty_shipped") - $item->getData("qty_refunded") - $qty_rma_old : 0;
                    $item->setData("qty_rma",(int)$qty);
                    $item->setData("request_rma",$rmas);
                    $item->setData("allow_per_item_order",$this->allowPerItem());
                }
            }
            else{
                if($qty_rma_old == 0){
                    $qty = $item->getData("qty_invoiced") > 0 ? $item->getData("qty_invoiced") : 0;
                    $item->setData("qty_rma",(int)$qty);
                    $item->setData("allow_per_item_order",$this->allowPerItem());
                }
                else{
                    $qty = $item->getData("qty_invoiced") - $qty_rma_old > 0 ? $item->getData("qty_invoiced") - $qty_rma_old : 0;
                    $item->setData("qty_rma",(int)$qty);
                    $item->setData("request_rma",$rmas);
                    $item->setData("allow_per_item_order",$this->allowPerItem());
                }
            }
           

            $items[] = $item;
        }


        return $items;
    }
}
