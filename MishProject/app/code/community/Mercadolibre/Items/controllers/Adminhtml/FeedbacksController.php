<?php

class Mercadolibre_Items_Adminhtml_FeedbacksController extends Mage_Adminhtml_Controller_action {

    private $moduleName = "Items";
    private $fileName = "FeedbacksController.php";
    //message variable
    private $infoMessage = "";
    private $errorMessage = "";
    private $successMessage = "";
    private $errorMessageLog = "";

    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('items/items')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Manage Feedback'), Mage::helper('adminhtml')->__('Manage Feedback'));
        return $this;
    }

    public function indexAction() {
        $this->_initAction()->renderLayout();
    }

    public function feedbackAction() {
        //$this->_initAction()->renderLayout(); 
        $this->importFeedbacks();
        $this->_redirect('*/*/');
    }

    public function editAction() {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('items/melifeedbacks')->load($id);
        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }
            Mage::register('feedbacks', $model);
            $this->loadLayout();
            $this->_setActiveMenu('items/items');
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Feedback Manager'), Mage::helper('adminhtml')->__('Feedback Manager'));
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('items/adminhtml_feedbacks_edit'))
                 ->_addLeft($this->getLayout()->createBlock('items/adminhtml_feedbacks_edit_tabs'));
            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('items')->__('Feedback does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function newAction() {
        $this->_forward('edit');
    }

    public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
			$orderIdParam = "";
			$orderIdParam = Mage::getSingleton('core/session')->getOrderIdParam();
			try {
				$commonModel = Mage::getModel('items/common');
				$requestData = '{rating: '.$this->getRequest()->getPost('rating_seller').', fulfilled:"true", message:'.$this->getRequest()->getPost('reply').'}';
				//get api url
				if(Mage::getStoreConfig("mlitems/mltokenaccess/mlapiurl",Mage::app()->getStore())){ 
					$apiUrl = Mage::getStoreConfig("mlitems/mltokenaccess/mlapiurl",Mage::app()->getStore());
				} else {
					$this->errorMessage = "Error :: API Url Not Found OR Invalid";
					$commonModel->saveLogger($this->moduleName, "Exception", $this->fileName, $this->errorMessage);	
					Mage::getSingleton('adminhtml/session')->addError(Mage::helper('items')->__($this->errorMessage));
					$this->_redirect('items/adminhtml_itemorders/');
					return; 						
				}
				//get access token
				if(Mage::helper('items')->getMlAccessToken()){
					$access_token = Mage::helper('items')->getMlAccessToken();
				} else {
					$this->errorMessage = "Error :: Access Token Not Found OR Invalid";
					$commonModel->saveLogger($this->moduleName, "Exception", $this->fileName, $this->errorMessage);
					Mage::getSingleton('adminhtml/session')->addError(Mage::helper('items')->__($this->errorMessage));
					$this->_redirect('items/adminhtml_itemorders/');
					return;	
				}
				/* Send Post Request & Get responce   */
				if(trim($access_token)!='' & trim($access_token)!=''){
					$requestUrl = $apiUrl.'/orders/'.$orderIdParam.'/feedback?access_token='.$access_token;
					$response = $commonModel-> meliConnect($requestUrl,'POST',$requestData);
				}
				if(isset($response['statusCode']) && $response['statusCode'] == 201 && isset($response['json']) && is_array($response['json'])){
					$model = Mage::getModel('items/melifeedbacks');
					$model->setData($data); 
					/* Save Feedback */
					$model -> setDateCreated(now());
					if($orderIdParam!=''){
						$model -> setOrderId ($orderIdParam);
					}
					$model -> save();
					Mage::getSingleton('core/session')->unsOrderIdParam();
					if($orderIdParam!=''){
						Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('items')->__("Feedback has been post successfully."));
						$this->_redirect('items/adminhtml_itemorders/');
						return;
					}else{
						Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('items')->__("Reply on Feedback(" . $this->getRequest()->getPost('order_id') . ") has been sent successfully."));
						$this->_redirect('*/*/');
						return;
					}
					if ($this->getRequest()->getParam('back')) {
						$this->_redirect('*/*/edit', array('id' => $model->getId()));
						return;
					}
					return;
				}elseif(isset($response['json']['message'])){
        			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('items')->__($response['json']['message']));
        			if($orderIdParam!=''){
						$this->_redirect('items/adminhtml_itemorders/');
					}else{
						$this->_redirect('*/*/');
					}	
					return;
				}
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                if($orderIdParam!=''){
					$this->_redirect('items/adminhtml_itemorders/');
				}else{
					$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				}
                return;
            }
        }else{
        		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('items')->__('Cannot save the feedback now, please try again.'));
        		$this->_redirect('*/*/');
				return;
		}
    }

    public function deleteAction() {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('items/melifeedbacks');
                $model -> setId($this->getRequest()->getParam('id'))
                       -> delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Feedback has been deleted successfully.'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    public function massDeleteAction() {
        $itemsIds = $this->getRequest()->getParam('questions');
        if (!is_array($itemsIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select questions(s)'));
        } else {
            try {
                foreach ($itemsIds as $itemsId) {
                    $items = Mage::getModel('items/meliquestions')->load($itemsId);
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

    public function massStatusAction() {
        $itemsIds = $this->getRequest()->getParam('items');
        if (!is_array($itemsIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($itemsIds as $itemsId) {
                    $items = Mage::getSingleton('items/questions')
                            ->load($itemsId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) were successfully updated', count($itemsIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function exportCsvAction() {
        $fileName = 'items.csv';
        $content = $this->getLayout()->createBlock('items/adminhtml_questions_grid')
                ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction() {
        $fileName = 'items.xml';
        $content = $this->getLayout()->createBlock('items/adminhtml_questions_grid')
                ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType = 'application/octet-stream') {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK', '');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename=' . $fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }

    public function importFeedbacks() {
        $model = Mage::getModel('items/melifeedbacks');
        $model->getAllFeedbackFromAPIhit();
    }

}