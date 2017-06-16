<?php

class Mercadolibre_Items_Adminhtml_MastertemplatesController extends Mage_Adminhtml_Controller_action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('items/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Manage Template'), Mage::helper('adminhtml')->__('Manage Template'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

	public function editAction() {

		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('items/melimastertemplates')->load($id);
		if ($model->getMasterTempId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			$data = $model->getData();
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('mastertemplates', $model);

			$this->loadLayout();
			$this->_setActiveMenu('items/items');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Manage Templates'), Mage::helper('adminhtml')->__('Manage Templates'));
			//$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));
			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('items/adminhtml_mastertemplates_edit'))
				->_addLeft($this->getLayout()->createBlock('items/adminhtml_mastertemplates_edit_tabs'));
			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('items')->__('Master Template does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
 
	public function saveAction() {
			try{
				if ($data = $this->getRequest()->getPost()) {
					//echo '<pre>';
					//print_r($data['payment_temp_id']); 
						$store_id = Mage::helper('items')-> _getStore()-> getId();
						$paymentTempIds = implode(',', $data['payment_id']);
						$data['payment_id'] = array();
						$data['payment_id'] = $paymentTempIds;
						//collection Chect Unique Template with same combination already exist
						$collectionMasterTemp = Mage::getModel('items/melimastertemplates')	
										-> getCollection()
										-> addFieldToFilter('template_id', $data['template_id'])
										-> addFieldToFilter('shipping_id', $data['shipping_id'])
										-> addFieldToFilter('profile_id', $data['profile_id'])
										-> addFieldToFilter('payment_id', $paymentTempIds)
										-> addFieldToFilter('store_id', $store_id);

						$dataTitleArr = $collectionMasterTemp->getData();
						//collection to check for same name 
						$collectionTempName = Mage::getModel('items/melimastertemplates')	
										-> getCollection()
										-> addFieldToFilter('master_temp_title', $data['master_temp_title']);
			
						if($this->getRequest()->getParam('id')){  //edit mode
							if($this->getRequest()->getParam('id') && count($collectionMasterTemp->getData()) > 0 && $dataTitleArr['0']['master_temp_id'] !=  $this->getRequest()->getParam('id')){
								// Chect Record Exist On Edit Record
								Mage::getSingleton('adminhtml/session')->addError(Mage::helper('items')->__('Master Template already exists.'));
								$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
								return;
							} 
						}
						if(!$this->getRequest()->getParam('id')){ //add mode  
							if(count($collectionMasterTemp->getData()) > 0){
								//Check for unique combinations
									Mage::getSingleton('adminhtml/session')->addError(Mage::helper('items')->__('Master Template with same options already exists.'));
									Mage::getSingleton('adminhtml/session')->setFormData($data);
									$this->_redirect('*/*/new');
									return;
							}
							if (count($collectionTempName->getData()) > 0){
								//Check Record Exist wirh same name
								Mage::getSingleton('adminhtml/session')->addError(Mage::helper('items')->__('Master Template with same name already exists.'));
								Mage::getSingleton('adminhtml/session')->setFormData($data);
								$this->_redirect('*/*/new');
								return;
							}	
						}
							
						try {
								$model  = Mage::getModel('items/melimastertemplates');		
								$model	-> setData($data)->setMasterTempId($this->getRequest()->getParam('id'))->setStoreId($store_id);
								if (!$this->getRequest()->getParam('id')) {
									$model->setDateCreated(now())
										 ->setLastUpdated(now());
								} else {
									$model->setLastUpdated(now());
								}	
								
								$model->save();
								Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('items')->__('Master Template is saved successfully '));
								Mage::getSingleton('adminhtml/session')->setFormData(false);
				
								if ($this->getRequest()->getParam('back')) {
									$this->_redirect('*/*/edit', array('id' => $model->getId()));
									return;
								}
								$this->_redirect('*/*/');
								return;
							} catch (Exception $e) {
								Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
								Mage::getSingleton('adminhtml/session')->setFormData($data);
								$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
								return;
						}
					}
				 Mage::getSingleton('adminhtml/session')->addError(Mage::helper('items')->__('Unable to find template to save'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				Mage::getSingleton('adminhtml/session')->setFormData($data);
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				return;
			}
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('items/melimastertemplates');
				 
				$model->setMasterTempId($this->getRequest()->getParam('id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Master Template is successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $itemsIds = $this->getRequest()->getParam('itemtemplates');
        if(!is_array($itemsIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select Profile(s)'));
        } else {
            try {
                foreach ($itemsIds as $itemsId) {
                    $items = Mage::getModel('items/melimastertemplates')->load($itemsId);
                    $items->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($itemsIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }  
}