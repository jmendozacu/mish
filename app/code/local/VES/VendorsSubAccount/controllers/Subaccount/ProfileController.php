<?php
class VES_VendorsSubAccount_Subaccount_ProfileController extends VES_Vendors_Controller_Action
{   
	protected function _isAllowed()
    {
        return Mage::helper('vendorssubaccount')->moduleEnable();
    }
    public function preDispatch()
    {
    	parent::preDispatch();
    	if(!$this->_getSession()->getIsSubAccount()){
    		$this->_redirect('vendors/account');
    		$this->setFlag('', 'no-dispatch', true);
    	}
    }
	protected function _initAction() {
		$this->loadLayout()
			->_title($this->__('My Account'))
			->_addBreadcrumb($this->__('My Account'), $this->__('My Account'));
		
		return $this;
	}   
 
	public function indexAction() {
		$subAccount = $this->_getSession()->getSubAccount();
		Mage::register('vendorssubaccount_data', $subAccount);
		$this->_initAction()
			->renderLayout();
	}
 
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
			$subAccount = $this->_getSession()->getSubAccount();
			
			
			try {
				foreach($data as $key=>$value){
					$subAccount->setData($key,$value);
				}
				$subAccount->setUpdatedAt(now());
				
				$subAccount->save();
				Mage::getSingleton('vendors/session')->addSuccess(Mage::helper('vendorssubaccount')->__('Account was successfully saved'));
				Mage::getSingleton('vendors/session')->setFormData(false);
				
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('vendors/session')->addError($e->getMessage());
                Mage::getSingleton('vendors/session')->setFormData($data);
                $this->_redirect('*/*/');
                return;
            }
        }
        $this->_redirect('*/*/');
	}
}