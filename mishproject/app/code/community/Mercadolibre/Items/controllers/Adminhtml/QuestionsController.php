<?php

class Mercadolibre_Items_Adminhtml_QuestionsController extends Mage_Adminhtml_Controller_action
{
	
	private $moduleName = "Items";
	private $fileName = "QuestionsController.php";
	
	//message variable
	private $infoMessage = "";
	private $errorMessage = "";
	private $successMessage = "";
	private $errorMessageLog = "";
	
	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('items/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Manage Profiles'), Mage::helper('adminhtml')->__('Manage Profiles'));
		
		return $this;
	}   
 
	public function indexAction() {
			Mage::helper('items')->_getStore()->getId();
			$this->_initAction()->renderLayout(); 
	}
        
    public function questionAction() {
			$this->importQuestions();
			$this->_initAction()->renderLayout(); 
			$this->_redirect('*/*/');
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('items/meliquestions')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('questions', $model);

			$this->loadLayout();
			$this->_setActiveMenu('items/items');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			//$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('items/adminhtml_questions_edit'))
				->_addLeft($this->getLayout()->createBlock('items/adminhtml_questions_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('items')->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
 
	public function saveAction() {
	
		if ($data = $this->getRequest()->getPost()) {
			$commonModel = Mage::getModel('items/common');
			//get the current store ID
			$storeId =  Mage::helper('items')-> _getStore()->getId();
			/* Set Data if template selected for answer */
			if($this->getRequest()->getPost('answer_template_id') > 0){
				$mlTemplateData = Mage::getModel('items/melianswertemplate')->load($this->getRequest()->getPost('answer_template_id'));
				$data = array('answer'=>$mlTemplateData->getData('answer'), 'store_id'=>$storeId);
			}
			//echo $this->getRequest()->getPost('answer_template_id');
			//echo $storeId ; die;
			$model = Mage::getModel('items/meliquestions');	
			$model -> setData($data);
			if($this->getRequest()->getParam('id')){	
				  	$model-> setId($this->getRequest()->getParam('id'));
			}
			
			try {
				/* Save template */
				if($this->getRequest()->getPost('answer_template_id') == 'new_template'){
					if($this->getRequest()->getParam('saveas') == 'template' && trim($this->getRequest()->getParam('title')) !='' && trim($this->getRequest()->getParam('title')) !='Enter Answer Template Title'){
						$melianswertemplate = Mage::getModel('items/melianswertemplate');
						$melianswertemplate->setAnswer($data['answer']);
						$melianswertemplate->setTitle($data['title']);
						$melianswertemplate->setStoreId($storeId);
						$melianswertemplate->save();	
					} else if($this->getRequest()->getParam('saveas') == 'template'){
						  Mage::getSingleton('adminhtml/session')->addError(Mage::helper('items')->__('Answer Template Title is required.'));
						 $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
						 return;
					}
				}
				$model-> setCreatedAt(now());
				$model-> setStatus('ANSWERED');
				//$postData = array('question_id'=> (int)$this->getRequest()->getPost('question_id'),'text'=>$model->getData('answer'));
				$requestData = '{question_id: '.$this->getRequest()->getPost('question_id').', text:"'.$model->getData('answer').'"}';
				//$requestData = json_encode($postData);
				/* post data */
				if(Mage::getStoreConfig("mlitems/mltokenaccess/mlapiurl",Mage::app()->getStore())){ 
					$apiUrl = Mage::getStoreConfig("mlitems/mltokenaccess/mlapiurl",Mage::app()->getStore());
				} else {
					$this->errorMessage = "Error :: API Url Not Found OR Invalid";
					$commonModel->saveLogger($this->moduleName, "Exception", $this->fileName, $this->errorMessage);	
					Mage::getSingleton('adminhtml/session')->addError(Mage::helper('items')->__($this->errorMessage));
					$this->_redirect('*/*/index');	
					return; 						
				}
				

				if(Mage::helper('items')->getMlAccessToken()){
					$access_token = Mage::helper('items')->getMlAccessToken();
				} else {
					$this->errorMessage = "Error :: Access Token Not Found OR Invalid";
					$commonModel->saveLogger($this->moduleName, "Exception", $this->fileName, $this->errorMessage);
					Mage::getSingleton('adminhtml/session')->addError(Mage::helper('items')->__($this->errorMessage));
					$this->_redirect('items/adminhtml_itempublishing/');
					return;	
				}
				
				/* Send Post Request & Get responce   */
				if(trim($access_token)!='' & trim($access_token)!=''){
					$requestUrl = $apiUrl.'/answers?access_token='.$access_token;
					$response = $commonModel-> meliConnect($requestUrl,'POST',$requestData);		
				}
				if(isset($response['statusCode']) && $response['statusCode'] == 200 && isset($response['json']) && is_array($response['json'])){
					$model->save();
					Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('items')->__('Question was successfully saved'));
					Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('items')->__("Answer for Question(".$this->getRequest()->getPost('question_id').") has been sent for publishing successfully."));
					 $this->_redirect('*/*/');
					 return; 
				}elseif(isset($response['json']['message'])){
						$question_id = $this->getRequest()->getPost('question_id'); 
						$errorItemIdsArr[] = $this->getRequest()->getPost('question_id'); 
						$this->errorMessage .= "Error :: Question Id (". $question_id .")". $response['json']['message'] .' '.$response['json']['status'].' ' . $response['json']['error']." <br />";
						$this->errorMessageLog = "Error :: Question Id (". $question_id .")". $response['json']['message'] .' '.$response['json']['status'].' ' . $response['json']['error'];
						$commonModel->saveLogger($this->moduleName, "Exception", $this->fileName, $this->errorMessageLog);
					if(isset($response['json']['cause']) && count ($response['json']['cause']) > 0 ){
						$this->errorMessage .= "Error Cause :: ";
						foreach($response['json']['cause'] as $row){
							$this->errorMessage .= $row['cause'].':'.$row['message'];
						}
					}
							
				}
				
				if(is_array($errorItemIdsArr) && count($errorItemIdsArr) > 0 ){
					$errorItemIds = implode(' , ',$errorItemIdsArr);
					Mage::getSingleton('adminhtml/session')->addError(Mage::helper('items')->__("Following Item('s) ($errorItemIds) could't be sent for publishing.<br />".$this->errorMessage));
				}
				
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
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('items')->__('Unable to find question to save'));
        $this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('items/meliquestions');
				 
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Question was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

  	public function massAnswerAction(){
       //$data = $this->getRequest()->getPost();
	   $itemsIds = $this->getRequest()->getParam('question');
	   $storeId =  Mage::helper('items')-> _getStore()->getId();
	    if(!is_array($itemsIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select a question(s)'));
        } else {
            try {
				$commonModel = Mage::getModel('items/common');
				if(Mage::getStoreConfig("mlitems/mltokenaccess/mlapiurl",Mage::app()->getStore())){ 
					$apiUrl = Mage::getStoreConfig("mlitems/mltokenaccess/mlapiurl",Mage::app()->getStore());
				} else {
					$this->errorMessage = "Error :: API Url Not Found OR Invalid";
					$commonModel->saveLogger($this->moduleName, "Exception", $this->fileName, $this->errorMessage);	
					Mage::getSingleton('adminhtml/session')->addError(Mage::helper('items')->__($this->errorMessage));
					$this->_redirect('*/*/index');	
					return; 						
				}
				
				if(Mage::helper('items')->getMlAccessToken()){
					$access_token = Mage::helper('items')->getMlAccessToken();
				} else {
					$this->errorMessage = "Error :: Access Token Not Found OR Invalid";
					$commonModel->saveLogger($this->moduleName, "Exception", $this->fileName, $this->errorMessage);
					Mage::getSingleton('adminhtml/session')->addError(Mage::helper('items')->__($this->errorMessage));
					$this->_redirect('items/adminhtml_itempublishing/');
					return;	
				}
				
				/* Set Data if template selected for answer */
				if($this->getRequest()->getPost('ansTempId') > 0){
					$mlTemplateData = Mage::getModel('items/melianswertemplate')->load($this->getRequest()->getPost('ansTempId'));
					$data = array('answer'=>$mlTemplateData->getData('answer'), 'store_id'=>$storeId);
				}
				$model = Mage::getModel('items/meliquestions');	
				$model -> setData($data);
				$errorItemIdsArr = array();
				foreach ($itemsIds as $itemsId) {
					$mlQuesIds = Mage::getModel('items/meliquestions')->load($itemsId);
					$mlQuesId = $mlQuesIds->getData('question_id');
					$model-> setId($itemsId);
                    $model-> setCreatedAt(now());
					$model-> setStatus('ANSWERED');
					$requestData = '{question_id: '.$mlQuesId.', text:"'.$mlTemplateData->getData('answer').'"}';
					
					/* Send Post Request & Get responce   */
					if(trim($access_token)!='' & trim($access_token)!=''){
						$requestUrl = $apiUrl.'/answers?access_token='.$access_token;
						$response = $commonModel-> meliConnect($requestUrl,'POST',$requestData);		
					}
					
					if(isset($response['statusCode']) && $response['statusCode'] == 200 && isset($response['json']) && is_array($response['json'])){
						$model->save();
					}elseif(isset($response['json']['message'])){
							$errorItemIdsArr[] = $itemsId; 
							$commonModel->saveLogger($this->moduleName, "Exception", $this->fileName, $this->errorMessageLog);
						if(isset($response['json']['cause']) && count ($response['json']['cause']) > 0 ){
							$this->errorMessage .= "Error Cause :: ";
							foreach($response['json']['cause'] as $row){
								$this->errorMessage .= $row['cause'].':'.$row['message'];
							}
						}
								
					}
				}
				if(is_array($errorItemIdsArr) && count($errorItemIdsArr) > 0 ){
						$errorItemIds = implode(' , ',$errorItemIdsArr);
						Mage::getSingleton('adminhtml/session')->addError(Mage::helper('items')->__("Answers for question('s) with ids ($errorItemIds) could't be sent for publishing.<br />".$this->errorMessage));
				}else{
                	$this->_getSession()->addSuccess( $this->__('Answer for total %d question(s) is successfully posted', count($itemsIds)));
				}
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'items.csv';
        $content    = $this->getLayout()->createBlock('items/adminhtml_questions_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'items.xml';
        $content    = $this->getLayout()->createBlock('items/adminhtml_questions_grid')
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
    public function importQuestions()
    {
        $model = Mage::getModel('items/meliquestions');
        $model->getAllQuestionFromAPIhit();
    }
	
	public function getAnswerTemplateAjaxAction(){
			$id     = $this->getRequest()->getParam('id');
			$model  = Mage::getModel('items/melianswertemplate')->load($id);
			echo $model->getData('title').'##Answer1Template2Id##'.$model->getData('answer');
		
	}
}