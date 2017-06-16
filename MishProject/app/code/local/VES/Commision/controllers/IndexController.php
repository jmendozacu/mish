<?php
class VES_Commision_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/commision?id=15 
    	 *  or
    	 * http://site.com/commision/id/15 	
    	 */
    	/* 
		$commision_id = $this->getRequest()->getParam('id');

  		if($commision_id != null && $commision_id != '')	{
			$commision = Mage::getModel('commision/commision')->load($commision_id)->getData();
		} else {
			$commision = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($commision == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$commisionTable = $resource->getTableName('commision');
			
			$select = $read->select()
			   ->from($commisionTable,array('commision_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$commision = $read->fetchRow($select);
		}
		Mage::register('commision', $commision);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }
}