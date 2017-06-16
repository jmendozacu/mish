<?php
class Bluethink_Btslider_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/btslider?id=15 
    	 *  or
    	 * http://site.com/btslider/id/15 	
    	 */
    	/* 
		$btslider_id = $this->getRequest()->getParam('id');

  		if($btslider_id != null && $btslider_id != '')	{
			$btslider = Mage::getModel('btslider/btslider')->load($btslider_id)->getData();
		} else {
			$btslider = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($btslider == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$btsliderTable = $resource->getTableName('btslider');
			
			$select = $read->select()
			   ->from($btsliderTable,array('btslider_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$btslider = $read->fetchRow($select);
		}
		Mage::register('btslider', $btslider);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }
}