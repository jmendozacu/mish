<?php
class Mercadolibre_Items_Block_Adminhtml_Dashboard_Questionstatus extends Mage_Adminhtml_Block_Dashboard_Bar
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('items/dashboard/questionstatus.phtml');
    }

    protected function _prepareLayout()
    {
        $db = Mage::getSingleton('core/resource')->getConnection('core_write');
		$storeId = Mage::helper('items')->_getStore()->getId();
		//echo "SELECT count(mq.id) as totalAnswered from mercadolibre_questions as mq LEFT JOIN mercadolibre_item as mi on mq.itemid = mi.item_id where mq.status = 'ANSWERED'  and mi.store_id = '".$storeId."'"; die;
		$totalAnswered = $db->fetchOne("SELECT count(mq.id) as totalAnswered from mercadolibre_questions as mq LEFT JOIN mercadolibre_item as mi on mq.itemid = mi.item_id where mq.status = 'ANSWERED'  and mi.store_id = '".$storeId."'");
		$totalUnAnswered = $db->fetchOne("SELECT count(mq.id) as totalAnswered from mercadolibre_questions as mq LEFT JOIN mercadolibre_item as mi on mq.itemid = mi.item_id where mq.status = 'UNANSWERED'  and mi.store_id = '".$storeId."'");
		//$totallisted = $db->fetchOne("SELECT count(item_id) as totalPublished from mercadolibre_item where store_id = '".$storeId."'");
		
		 $this->setData('totalAnswered',$totalAnswered);
		 $this->setData('totalUnAnswered',$totalUnAnswered);
		// $this->setData('totallisted',$totallisted);
       
    }
}
