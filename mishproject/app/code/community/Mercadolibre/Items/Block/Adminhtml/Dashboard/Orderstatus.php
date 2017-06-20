<?php
class Mercadolibre_Items_Block_Adminhtml_Dashboard_Orderstatus extends Mage_Adminhtml_Block_Dashboard_Bar
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('items/dashboard/orderstatus.phtml');
    }

    protected function _prepareLayout()
    {
        $db = Mage::getSingleton('core/resource')->getConnection('core_write');
		$storeId = Mage::helper('items')->_getStore()->getId();
		$totalPaidOrder = $db->fetchOne("SELECT count(id) from mercadolibre_order where payment_status = '1' and store_id = '".$storeId."'");
		$totalUnPaidOrder = $db->fetchOne("SELECT count(id) from mercadolibre_order where payment_status = '0' and store_id = '".$storeId."'");
		$totalshippedOrder = $db->fetchOne("SELECT count(id) from mercadolibre_order where shipping_status = '1' and store_id = '".$storeId."'");
		$totalUnShippedOrder = $db->fetchOne("SELECT count(id) from mercadolibre_order where shipping_status = '0' and store_id = '".$storeId."'");
		
		 $this->setData('totalPaidOrder',$totalPaidOrder);
		 $this->setData('totalUnPaidOrder',$totalUnPaidOrder);
		 $this->setData('totalshippedOrder',$totalshippedOrder);
		 $this->setData('totalUnShippedOrder',$totalUnShippedOrder);
       
    }
}
