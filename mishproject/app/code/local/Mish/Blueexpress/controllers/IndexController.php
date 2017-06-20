<?php
class Mish_Blueexpress_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/blueexpress?id=15 
    	 *  or
    	 * http://site.com/blueexpress/id/15 	
    	 */
    	/* 
		$blueexpress_id = $this->getRequest()->getParam('id');

  		if($blueexpress_id != null && $blueexpress_id != '')	{
			$blueexpress = Mage::getModel('blueexpress/blueexpress')->load($blueexpress_id)->getData();
		} else {
			$blueexpress = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($blueexpress == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$blueexpressTable = $resource->getTableName('blueexpress');
			
			$select = $read->select()
			   ->from($blueexpressTable,array('blueexpress_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$blueexpress = $read->fetchRow($select);
		}
		Mage::register('blueexpress', $blueexpress);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }
}