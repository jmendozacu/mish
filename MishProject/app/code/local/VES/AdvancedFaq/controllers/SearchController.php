<?php
class OTTO_AdvancedFaq_SearchController extends Mage_Core_Controller_Front_Action
{
	public function indexAction(){
		$this->loadLayout();
		$query = $this->getRequest()->getParam("q");
		Mage::register("key_query", $query);
		$this->renderLayout();
	}
}