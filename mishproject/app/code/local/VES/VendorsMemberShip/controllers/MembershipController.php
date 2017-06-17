<?php
class VES_VendorsMemberShip_MembershipController extends Mage_Core_Controller_Front_Action
{
	protected function _getSession(){
		return Mage::getSingleton('customer/session');
	}
	/**
     * Action predispatch
     *
     * Check customer authentication for some actions
     */
    public function preDispatch()
    {
        // a brute-force protection here would be nice

        parent::preDispatch();

        if (!$this->getRequest()->isDispatched()) {
            return;
        }

    	if (!$this->_getSession()->authenticate($this)) {
        	$this->setFlag('', 'no-dispatch', true);
        }
    }
	public function indexAction(){
		Mage::getModel('membership/observer')->dailyCheckExpiredVendor();
		exit;
		$this->loadLayout();
		$this->getLayout()->getBlock('head')->setTitle('Vendor Dashboard');
		$this->renderLayout();
	}
}