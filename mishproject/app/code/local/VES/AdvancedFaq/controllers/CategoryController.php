<?php
class OTTO_AdvancedFaq_CategoryController extends Mage_Core_Controller_Front_Action
{
	/**
     * Action predispatch
     *
     * Check customer authentication for some actions
     */
    public function preDispatch()
    {
    	parent::preDispatch();
    	
       
		if(!Mage::helper("advancedfaq")->isEnabled()) {
			return;
		}
        if (!$this->getRequest()->isDispatched()) {
            return;
        }
        /*
        if (! Mage::getSingleton('customer/session')->authenticate($this)) {
        	$this->setFlag('', 'no-dispatch', true);
        }
        */
    }
    public function indexAction()
    {
		$this->loadLayout();     
		$this->renderLayout();
    }
}
