<?php
class Mish_Correoschile_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/correoschile?id=15 
    	 *  or
    	 * http://site.com/correoschile/id/15 	
    	 */
    	/* 
		$correoschile_id = $this->getRequest()->getParam('id');

  		if($correoschile_id != null && $correoschile_id != '')	{
			$correoschile = Mage::getModel('correoschile/correoschile')->load($correoschile_id)->getData();
		} else {
			$correoschile = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($correoschile == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$correoschileTable = $resource->getTableName('correoschile');
			
			$select = $read->select()
			   ->from($correoschileTable,array('correoschile_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$correoschile = $read->fetchRow($select);
		}
		Mage::register('correoschile', $correoschile);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }
}