<?php
class Bluethink_Quesanswer_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/quesanswer?id=15 
    	 *  or
    	 * http://site.com/quesanswer/id/15 	
    	 */
    	/* 
		$quesanswer_id = $this->getRequest()->getParam('id');

  		if($quesanswer_id != null && $quesanswer_id != '')	{
			$quesanswer = Mage::getModel('quesanswer/quesanswer')->load($quesanswer_id)->getData();
		} else {
			$quesanswer = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($quesanswer == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$quesanswerTable = $resource->getTableName('quesanswer');
			
			$select = $read->select()
			   ->from($quesanswerTable,array('quesanswer_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$quesanswer = $read->fetchRow($select);
		}
		Mage::register('quesanswer', $quesanswer);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }
}