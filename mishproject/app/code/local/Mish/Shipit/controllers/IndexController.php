<?php
class Mish_Shipit_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/shipit?id=15 
    	 *  or
    	 * http://site.com/shipit/id/15 	
    	 */
    	/* 
		$shipit_id = $this->getRequest()->getParam('id');

  		if($shipit_id != null && $shipit_id != '')	{
			$shipit = Mage::getModel('shipit/shipit')->load($shipit_id)->getData();
		} else {
			$shipit = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($shipit == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$shipitTable = $resource->getTableName('shipit');
			
			$select = $read->select()
			   ->from($shipitTable,array('shipit_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$shipit = $read->fetchRow($select);
		}
		Mage::register('shipit', $shipit);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }
}