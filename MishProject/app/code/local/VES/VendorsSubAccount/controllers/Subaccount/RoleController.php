<?php
class VES_VendorsSubAccount_Subaccount_RoleController extends VES_Vendors_Controller_Action
{   
	protected function _isAllowed()
    {
        return Mage::helper('vendorssubaccount')->moduleEnable();
    }
    
	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('subaccount')->_title($this->__('Manage Roles'))
			->_addBreadcrumb($this->__('Manage Roles'), $this->__('Manage Roles'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('vendorssubaccount/role')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('vendors/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}
			if($model->getId()){
				/*Remove the prefix of username*/
				$vendorId = $this->_getSession()->getVendor()->getVendorId();
				$userName = str_replace($vendorId.VES_VendorsSubAccount_Model_Account::SPECIAL_CHAR, '', $model->getUsername());
				$model->setUsername($userName);
			}
			Mage::register('role_data', $model);
                                                                                                                                     
			$this->loadLayout();
			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
			$this->getLayout()->getBlock('js')->append(
	            $this->getLayout()->createBlock('adminhtml/template')->setTemplate('permissions/role_users_grid_js.phtml')
	        );
			$this->_setActiveMenu('subaccount')->_title($this->__('Manage Roles'));

			$this->_addBreadcrumb($this->__('Manage Roles'), $this->__('Manage Roles'),Mage::getUrl('vendors/subaccount_role'));
			if($model->getId()){
				$this->_addBreadcrumb($this->__('Edit Role'), $this->__('Edit Role'))->_title($this->__('Edit Role'));;
			}else{
				$this->_addBreadcrumb($this->__('Add Role'), $this->__('Add Role'))->_title($this->__('Add Role'));;
			}

			$this->renderLayout();
		} else {
			Mage::getSingleton('vendors/session')->addError(Mage::helper('vendorssubaccount')->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
 
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
			$model 		= Mage::getModel('vendorssubaccount/role');
			$vendor 	= $this->_getSession()->getVendor();
			$model->setData($data)
				->setId($this->getRequest()->getParam('id'));
			
			try {

				$model->setVendorId($vendor->getId())->save();
				Mage::getSingleton('vendors/session')->addSuccess(Mage::helper('vendorssubaccount')->__('Role was successfully saved'));
				Mage::getSingleton('vendors/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('vendors/session')->addError($e->getMessage());
                Mage::getSingleton('vendors/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('vendors/session')->addError(Mage::helper('vendorssubaccount')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('vendorssubaccount/role');
				 
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
				Mage::getSingleton('vendors/session')->addSuccess(Mage::helper('vendorssubaccount')->__('Item was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('vendors/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}
}