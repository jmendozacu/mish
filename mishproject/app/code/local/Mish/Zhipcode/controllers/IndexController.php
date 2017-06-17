<?php
class Mish_Zhipcode_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/zhipcode?id=15 
    	 *  or
    	 * http://site.com/zhipcode/id/15 	
    	 */
    	/* 
		$zhipcode_id = $this->getRequest()->getParam('id');

  		if($zhipcode_id != null && $zhipcode_id != '')	{
			$zhipcode = Mage::getModel('zhipcode/zhipcode')->load($zhipcode_id)->getData();
		} else {
			$zhipcode = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($zhipcode == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$zhipcodeTable = $resource->getTableName('zhipcode');
			
			$select = $read->select()
			   ->from($zhipcodeTable,array('zhipcode_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$zhipcode = $read->fetchRow($select);
		}
		Mage::register('zhipcode', $zhipcode);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }
}