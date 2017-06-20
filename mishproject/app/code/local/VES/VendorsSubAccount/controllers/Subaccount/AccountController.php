<?php
class VES_VendorsSubAccount_Subaccount_AccountController extends VES_Vendors_Controller_Action
{   
	protected function _isAllowed()
    {
        return Mage::helper('vendorssubaccount')->moduleEnable();
    }
    
	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('subaccount')->_title($this->__('Manage Sub Accounts'))
			->_addBreadcrumb($this->__('Manage Sub Accounts'), $this->__('Manage Sub Accounts'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('vendorssubaccount/account')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('vendors/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}
			if($model->getId()){
				if($model->getVendorId() != $this->_getSession()->getVendorId()){
					Mage::getSingleton('vendors/session')->addError(Mage::helper('vendorssubaccount')->__('Item does not exist'));
					$this->_redirect('*/*/');
					return;
				}
				/*Remove the prefix of username*/
				$vendorId = $this->_getSession()->getVendor()->getVendorId();
				$userName = str_replace($vendorId.VES_VendorsSubAccount_Model_Account::SPECIAL_CHAR, '', $model->getUsername());
				$model->setUsername($userName);
			}
			Mage::register('vendorssubaccount_data', $model);
                                                                                                                                     
			$this->loadLayout();
			$this->_setActiveMenu('subaccount')->_title($this->__('Manage Sub Accounts'));

			$this->_addBreadcrumb($this->__('Manage Sub Accounts'), $this->__('Manage Sub Accounts'),Mage::getUrl('vendors/subaccount_account'));
			if($model->getId()){
				$this->_addBreadcrumb($this->__('Edit Account'), $this->__('Edit Account'))->_title($this->__('Edit Account'));;
			}else{
				$this->_addBreadcrumb($this->__('Add Account'), $this->__('Add Account'))->_title($this->__('Add Account'));;
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
			$id = $this->getRequest()->getParam('id');
			$model 		= Mage::getModel('vendorssubaccount/account')->load($id);
			
			if($model->getId() && $model->getVendorId() != $this->_getSession()->getVendorId()){
				Mage::getSingleton('vendors/session')->addError(Mage::helper('vendorssubaccount')->__('Item does not exist'));
				$this->_redirect('*/*/');
				return;
			}
			
			$vendor 	= $this->_getSession()->getVendor();
			$newData 	= array_merge(array(), $data);
			if(isset($newData['username'])){
				/*Add vendor id as prefix of subaccount*/
				$newData['username'] = $vendor->getVendorId().VES_VendorsSubAccount_Model_Account::SPECIAL_CHAR.$newData['username'];
			}
			$model->setData($newData)
				->setId($id);
			
			try {
				if ($model->getCreatedAt() == NULL || $model->getUpdatedAt() == NULL) {
					$model->setCreatedAt(now())
						->setUpdatedAt(now());
				} else {
					$model->setUpdatedAt(now());
				}	
				
				$model->setVendorId($vendor->getId())->save();
				Mage::getSingleton('vendors/session')->addSuccess(Mage::helper('vendorssubaccount')->__('Account was successfully saved'));
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
				$id = $this->getRequest()->getParam('id');
				$model = Mage::getModel('vendorssubaccount/account')->load($id);
				
				if($model->getId() && $model->getVendorId() != $this->_getSession()->getVendorId()){
					Mage::getSingleton('vendors/session')->addError(Mage::helper('vendorssubaccount')->__('Item does not exist'));
					$this->_redirect('*/*/');
					return;
				}
				$model->delete();
					 
				Mage::getSingleton('vendors/session')->addSuccess(Mage::helper('vendorssubaccount')->__('Item was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('vendors/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $vendorssubaccountIds = explode(",",$this->getRequest()->getParam('vendorssubaccount'));
        if(!is_array($vendorssubaccountIds)) {
			Mage::getSingleton('vendors/session')->addError(Mage::helper('vendorssubaccount')->__('Please select item(s)'));
        } else {
            try {
            	$count = 0;
                foreach ($vendorssubaccountIds as $vendorssubaccountId) {
                    $vendorssubaccount = Mage::getModel('vendorssubaccount/account')->load($vendorssubaccountId);
	                if($vendorssubaccount->getId() && $vendorssubaccount->getVendorId() != $this->_getSession()->getVendorId()){
						Mage::getSingleton('vendors/session')->addError(Mage::helper('vendorssubaccount')->__('Item #%s does not exist',$vendorssubaccountId));
						continue;
					}
                    $vendorssubaccount->delete();
                }
                Mage::getSingleton('vendors/session')->addSuccess(
                    Mage::helper('vendorssubaccount')->__(
                        'Total of %d record(s) were successfully deleted', count($vendorssubaccountIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('vendors/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	
    public function massStatusAction()
    {
        $vendorssubaccountIds = explode(",",$this->getRequest()->getParam('vendorssubaccount'));
        if(!is_array($vendorssubaccountIds)) {
            Mage::getSingleton('vendors/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($vendorssubaccountIds as $vendorssubaccountId) {
                    $vendorssubaccount = Mage::getSingleton('vendorssubaccount/account')
                        ->load($vendorssubaccountId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($vendorssubaccountIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
}