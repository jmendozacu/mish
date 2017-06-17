<?php

class Mish_Personallogistic_Adminhtml_PersonallogisticadminController extends Mage_Adminhtml_Controller_action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('personallogistic/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
      
		$this->_initAction()
			->renderLayout();
	}
	public function saveAction() {

  $post=$this->getrequest()->getpost('min');
     
		$personallogisticadmin = Mage::getModel('personallogistic/personallogisticadmin')->load(9)
		->setMin($post)
        ->save();
        Mage::getSingleton('core/session')->addSuccess('You are suucessfully submit price');
        $this->_redirect('personallogistic/adminhtml_personallogisticadmin/index/');
        
	}


	
}