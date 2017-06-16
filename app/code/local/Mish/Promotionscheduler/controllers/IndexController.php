<?php
class Mish_Promotionscheduler_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/promotionscheduler?id=15 
    	 *  or
    	 * http://site.com/promotionscheduler/id/15 	
    	 */
    	/* 
		$promotionscheduler_id = $this->getRequest()->getParam('id');

  		if($promotionscheduler_id != null && $promotionscheduler_id != '')	{
			$promotionscheduler = Mage::getModel('promotionscheduler/promotionscheduler')->load($promotionscheduler_id)->getData();
		} else {
			$promotionscheduler = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($promotionscheduler == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$promotionschedulerTable = $resource->getTableName('promotionscheduler');
			
			$select = $read->select()
			   ->from($promotionschedulerTable,array('promotionscheduler_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$promotionscheduler = $read->fetchRow($select);
		}
		Mage::register('promotionscheduler', $promotionscheduler);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }
}