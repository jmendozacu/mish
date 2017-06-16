<?php
class VES_VendorsLiveChat_CssController extends Mage_Core_Controller_Front_Action
{
	  public function preDispatch(){
        $this->setFlag("index",Mage_Core_Controller_Varien_Action::FLAG_NO_START_SESSION,"no-start");
        parent::preDispatch();
    }
	
    public function indexAction()
    {
        $this->getResponse()->setHeader('Content-type', 'text/css');
        $this->loadLayout()->renderLayout();
    }
}