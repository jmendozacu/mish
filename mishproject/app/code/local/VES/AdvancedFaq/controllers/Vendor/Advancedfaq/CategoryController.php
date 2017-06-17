<?php

class OTTO_AdvancedFaq_Seller_Advancedfaq_CategoryController extends OTTO_Sellers_Controller_Action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('advancedfaq/category')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Category Manager'));
		//Mage::helper('otto_core');
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('advancedfaq/category')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('sellers/session')->getFormData(true);
			//var_dump($data);exit;
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('category_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('advancedfaq/category');

			$this->_addBreadcrumb(Mage::helper('sellers')->__('Category Manager'), Mage::helper('adminhtml')->__('Category Manager'));
			$this->_addBreadcrumb(Mage::helper('sellers')->__('Category News'), Mage::helper('adminhtml')->__('Category News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('advancedfaq/seller_category_edit'))
				->_addLeft($this->getLayout()->createBlock('advancedfaq/seller_category_edit_tabs'));

			$this->renderLayout();
		} else {
			$this->_getSession()->addError(Mage::helper('advancedfaq')->__('Category does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward("edit");
	}
 
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
			$model = Mage::getModel('advancedfaq/category');
			$data['store_id'] = array("0");
			$check_url_key = Mage::getModel('advancedfaq/category')->getCollection()->addFieldToFilter('url_key',array("eq"=>$data['url_key']))->getFirstItem();
			if($check_url_key->getId() && $check_url_key->getId()!=$this->getRequest()->getParam('id')){
				$this->_getSession()->addError("A Url key with the same properties already exists.");
				$this->_getSession()->setFormData($data);
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				return;
			}
			/*
			if(!is_array($data['store_id'])) $data['store_id'] = array($data['store_id']);
	
			foreach ($data['store_id'] as $store_id){
				if($store_id == 0){
					$check_url_key = Mage::getModel('advancedfaq/category')->getCollection()->addFieldToFilter('url_key',array("eq"=>$data['url_key']))->getFirstItem();
				}
				else{
					$check_url_key = Mage::getModel('advancedfaq/category')->getCollection()->addFieldToFilter('url_key',array("eq"=>$data['url_key']))->addFieldToFilter('store_id',array('finset'=>$store_id))->getFirstItem();
				}
				//$check_url_key = Mage::getModel('kbase/category')->load($data['url_key'],"url_key");
				if($check_url_key->getId() && $check_url_key->getId()!=$this->getRequest()->getParam('id')){
					$this->_getSession()->addError("A Url key with the same properties already exists in the selected store.");
					$this->_getSession()->setFormData($data);
					$this->_redirect('edit', array('id' => $this->getRequest()->getParam('id')));
					return;
				}
			}
			*/
			$data['store_id']=implode(',', $data['store_id']);
			$data['seller_id'] = $this->_getSession()->getSellerId();
		
			$model->setData($data)
				->setId($this->getRequest()->getParam('id'));
			
			try {
				if ($model->getCreatedTime() == NULL || $model->getUpdatedTime() == NULL) {
					$model->setCreatedTime(now())
						->setUpdatedTime(now());
				} else {
					$model->setUpdatedTime(now());
				}	
				
				$model->save();
				$this->_getSession()->addSuccess(Mage::helper('advancedfaq')->__('Category was successfully saved'));
				$this->_getSession()->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $this->_getSession()->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
       $this->_getSession()->addError(Mage::helper('advancedfaq')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('advancedfaq/category');
				 
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
				$this->_getSession()->addSuccess(Mage::helper('adminhtml')->__('Category was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				$this->_getSession()->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $kbaseIds = explode(",",$this->getRequest()->getParam('category'));
        if(!is_array($kbaseIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($kbaseIds as $kbaseId) {
                    $kbase = Mage::getModel('advancedfaq/category')->load($kbaseId);
                    $kbase->delete();
                }
                    $this->_getSession()->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($kbaseIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	
    public function massStatusAction()
    {
        $kbaseIds = explode(",",$this->getRequest()->getParam('category'));
        if(!is_array($kbaseIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
        	//var_dump($kbaseIds);exit;
            try {
                foreach ($kbaseIds as $kbaseId) {
                    $kbase = Mage::getSingleton('advancedfaq/category')
                        ->load($kbaseId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($kbaseIds))
                );
            } catch (Exception $e) {
            	
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'advancedfaq_category.csv';
        $content    = $this->getLayout()->createBlock('advancedfaq/adminhtml_category_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'advancedfaq_category.xml';
        $content    = $this->getLayout()->createBlock('advancedfaq/adminhtml_category_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
}
