<?php
class Mercadolibre_Items_Block_Adminhtml_Notification_Toolbar extends Mage_Adminhtml_Block_Template
{
    protected function _construct()
    {}

    public function isShow()
    {
        if ($this->getMLNewOrdersCount() == 0 && $this->getMLUnAnsQuesCount() == 0 && $this->getMLNewFeedbacks() == 0) {
            return false;
        }
        return true;
    }

    public function getMLNewOrdersCount()
    {
        $collection = Mage::getModel('items/mercadolibreorder')->getCollection()
                    -> addFieldToFilter('status', array('eq' => 'confirmed'))
					-> addFieldToFilter('store_id',Mage::helper('items')->_getStore()->getId());
        return $collection->count();
    }

   
    public function getMLUnAnsQuesCount()
    {
		$storeId = Mage::helper('items')-> _getStore()->getId();
		$collection = Mage::getModel('items/meliquestions')->getCollection();
		$collection->getSelect()
					->join( array('mi' => 'mercadolibre_item'), 'mi.meli_item_id = main_table.itemid ', array("category_id","DATE_FORMAT(main_table.question_date,'%m-%d-%Y') as question_date","DATE_FORMAT(main_table.created_at,'%m-%d-%Y') as created_at"))
					->join( array('mcm' => 'mercadolibre_categories_mapping'), 'mcm.mage_category_id  = mi.category_id ', array("store_id"));
		$collection -> addFieldToFilter('mcm.store_id',$storeId)
				 	-> addFieldToFilter('main_table.status', array('eq' => 'UNANSWERED'))  
				 	-> setOrder('main_table.created_at', 'DESC'); 
		return $collection->count();
    }

   
    public function getMLNewFeedbacks()
    {
		$storeId = Mage::helper('items')-> _getStore()->getId();
		$collection = Mage::getModel('items/melifeedbacks')->getCollection()
					-> setOrder('id', 'ASC');
		$collection->getSelect()
					-> joinleft(array('mlorder'=>'mercadolibre_order'), "mlorder.order_id = main_table.order_id", array('mlorder.store_id'));
		$collection -> addFieldToFilter('mlorder.store_id',$storeId)
					-> addFieldToFilter('main_table.reply', array('eq' => ''))
					-> setOrder('main_table.id', 'ASC');
		
		return $collection->count();
    }
 
}
