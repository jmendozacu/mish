<?php
class Mercadolibre_Items_Block_Adminhtml_Dashboard_Totals extends Mage_Adminhtml_Block_Dashboard_Bar
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('items/dashboard/totalbar.phtml');
    }

    protected function _prepareLayout()
    {
        $db = Mage::getSingleton('core/resource')->getConnection('core_write');
		$storeId = Mage::helper('items')->_getStore()->getId();
		$totalPublished = $db->fetchOne("SELECT count(item_id) as totalPublished from mercadolibre_item where sent_to_publish = 'Published' and store_id = '".$storeId."'");
		$totalUnPublished = $db->fetchOne("SELECT count(item_id) as totalPublished from mercadolibre_item where sent_to_publish = 'Unpublished' and store_id = '".$storeId."'");
		$totallisted = $db->fetchOne("SELECT count(item_id) as totalPublished from mercadolibre_item where store_id = '".$storeId."'");
		
		 $this->setData('totalPublished',$totalPublished);
		 $this->setData('totalUnPublished',$totalUnPublished);
		 $this->setData('totallisted',$totallisted);
       
    }
}
