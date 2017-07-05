<?php
class Bluethink_Recommendation_IndexController extends Mage_Core_Controller_Front_Action
{


	public function voteyesAction()
    {
    	$voteYesProid = $this->getRequest()->getPost('pro_id');
    	
    	$recommenedyes = Mage::getModel('recommendation/recommendation')->load($voteYesProid,'product_id');

    	if(!empty($recommenedyes->getProductId()))
    	{

    		$proidyes= $recommenedyes->getYes();
    		$count = $recommenedyes->getCount();
    		$countvalue = $count+1; 
    		$sum = $proidyes + 1;
	    	$recommenedyes->setProductId($voteYesProid);
	    	$recommenedyes->setYes($sum);
	    	$recommenedyes->setCount($countvalue);
	    	//$recommenedyes->setNo(0);
	    	$recommenedyes->save();
        }else{
    	
    	//$recommened = Mage::getModel('recommendation/recommendation')->load();
    	$recommenedyes->setProductId($voteYesProid);
    	$recommenedyes->setYes(1);
    	$recommenedyes->setNo(0);
    	$recommenedyes->setCount(1);
    	$recommenedyes->save();
    }
    	
    }

    public function votenoAction()
    {
    	$voteNoProid = $this->getRequest()->getPost('pro_id');
    	
    	$recommenedno = Mage::getModel('recommendation/recommendation')->load($voteNoProid,'product_id');

    	if(!empty($recommenedno->getProductId()))
    	{
    		$proidno= $recommenedno->getNo();
    		$count = $recommenedno->getCount();
    		$countvalue = $count+1; 
    		$sum = $proidno + 1;
	    	$recommenedno->setProductId($voteYesProid);
	    	//$recommenedno->setYes(0);
	    	$recommenedno->setNo($sum);
	    	$recommenedno->setCount($countvalue);
	    	$recommenedno->save();
        }else{
    	
    	//$recommened = Mage::getModel('recommendation/recommendation')->load();
    	$recommenedno->setProductId($voteNoProid);
    	$recommenedno->setYes(1);
    	$recommenedno->setNo(0);
    	$recommenedno->setCount(1);
    	$recommenedno->save();
    }
    	
    }

    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/recommendation?id=15 
    	 *  or
    	 * http://site.com/recommendation/id/15 	
    	 */
    	/* 
		$recommendation_id = $this->getRequest()->getParam('id');

  		if($recommendation_id != null && $recommendation_id != '')	{
			$recommendation = Mage::getModel('recommendation/recommendation')->load($recommendation_id)->getData();
		} else {
			$recommendation = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($recommendation == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$recommendationTable = $resource->getTableName('recommendation');
			
			$select = $read->select()
			   ->from($recommendationTable,array('recommendation_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$recommendation = $read->fetchRow($select);
		}
		Mage::register('recommendation', $recommendation);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }
}