<?php

class Mercadolibre_Items_Adminhtml_PaymentprofileController extends Mage_Adminhtml_Controller_action
{

	private $moduleName = "Items";
	private $fileName = "PaymentprofileController.php";
	
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
		try{           
            $commonModel = Mage::getModel('items/common');
			/* Get Base URL Id */
            if(Mage::getStoreConfig("mlitems/mltokenaccess/mlapiurl",Mage::app()->getStore())){
                    $api_url = Mage::getStoreConfig("mlitems/mltokenaccess/mlapiurl",Mage::app()->getStore());
            } else {
                    $this->errorMessage = "Error :: Api Url Not Found OR Invalid";
                    $this->saveLogger($this->moduleName, "Exception", $this->fileName, $this->errorMessage);
                    $commonModel->sendNotificationMail($this->to, 'ML Payment Methods All Data Cron Error', $this->errorMessage);
            }
            
            $this->to = Mage::getStoreConfig("mlitems/meligeneralsetting/notificationemailid", Mage::app()->getStore());
            $item =  Mage::getModel('items/melipaymentmethods')
					-> getCollection();

			if(count($item->getData()) <= 0){
					if($siteid = Mage::helper('items')->getMlSiteId()){
						$siteid = $siteid;
					}else {
						$this->errorMessage = "Error :: Site Id Not Found.".
						$commonModel->saveLogger($this->moduleName, "Exception", $this->fileName, $this->errorMessage);	
						Mage::getSingleton('adminhtml/session')->addError(Mage::helper('items')->__("Error :: Site Id Not Found."));
						$this->_redirect('*/*/index');
					}
					/*  Get & save all payment methods json data into DB table mercadolibre_payment_methods */
					$service_url = $api_url.'/sites/'.$siteid.'/payment_methods';                
					$jsonDataResp = $commonModel ->meliConnect($service_url);
					$arryResp = $jsonDataResp['json'];
					if(isset($jsonDataResp['statusCode']) && $jsonDataResp['statusCode'] == '200'){
						if(count($arryResp) > 0){
							foreach($arryResp as $data)
							{
							   $melipaymentmethod = Mage::getModel('items/melipaymentmethods');
							   $dataArry = array(                            
												'payment_id'=>trim($data['id']),    
												'payment_name'=>$data['name'],
												'payment_type_id'=>$data['payment_type_id'],
												'site_id'=>$siteid,
												'thumbnail'=>$data['thumbnail'],    
												'secure_thumbnail'=>$data['secure_thumbnail'],
												'date_created'=>now(),
												'last_updated'=>now()
												); 
							   $melipaymentmethod->setData($dataArry);                        
							   $melipaymentmethod->save();                                                                                            
							} 
						}
					}else {
					Mage::getSingleton('adminhtml/session')->addError(Mage::helper('items')->__('Payment methods(s) does not exist.'));
					$this->_redirect('*/*/');
				}
			}
        }catch(Exception $e){
            $commonModel->saveLogger($this->moduleName, "Exception", $this->fileName, $e->getMessage());
		}
		$this->_initAction()
			->renderLayout();
   }

	public function editAction() {

		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('items/meliproducttemplates')->load($id);
		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('paymentprofile', $model);

			$this->loadLayout();
			$this->_setActiveMenu('items/items');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Manage Profiles'), Mage::helper('adminhtml')->__('Manage Profiles'));
			//$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));
			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('items/adminhtml_paymentprofile_edit'))
				->_addLeft($this->getLayout()->createBlock('items/adminhtml_paymentprofile_edit_tabs'));
			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('items')->__('Profile does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
 
	public function saveAction() {
			try{
				if ($data = $this->getRequest()->getPost()) {
						// Chect Unique Template Name On Edit Record
						$collectionTitle = Mage::getModel('items/meliproducttemplates')	-> getCollection()
																						-> addFieldToFilter('title',$data['title'])-> addFieldToSelect('template_id');
						$dataTitleArr = $collectionTitle->getData();																
						// Check for buying_mode_id,listing_type_id and condition_id combination exist
						$collection = Mage::getModel('items/meliproducttemplates') -> getCollection()
																				   -> addFieldToFilter('buying_mode_id',$data['buying_mode_id'])
																				   -> addFieldToFilter('listing_type_id',$data['listing_type_id'])
																				   -> addFieldToFilter('condition_id',$data['condition_id'])
																				   -> addFieldToSelect('template_id');
						$dataArr = $collection->getData();
						if($this->getRequest()->getParam('id')){  // Edit Template
							if($this->getRequest()->getParam('id') && count($collection->getData()) > 0 && $dataArr['0']['template_id'] !=  $this->getRequest()->getParam('id')){
								// Chect Record Exist On Edit Record
								Mage::getSingleton('adminhtml/session')->addError(Mage::helper('items')->__('Profile already exists.'));
								$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
								return;
							} 
						} else if (count($collection->getData()) > 0){
							// Chect Record Exist On Edit Record
								Mage::getSingleton('adminhtml/session')->addError(Mage::helper('items')->__('Profile already exists.'));
								Mage::getSingleton('adminhtml/session')->setFormData($data);
								$this->_redirect('*/*/new');
								return;
						} 
						if(count($collectionTitle->getData()) > 0 && $dataTitleArr['0']['template_id'] !=  $this->getRequest()->getParam('id') ){ // New Record
							Mage::getSingleton('adminhtml/session')->addError(Mage::helper('items')->__('Profile name already exists.'));
							if($this->getRequest()->getParam('id')){
								$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
							} else {
								Mage::getSingleton('adminhtml/session')->setFormData($data);
								$this->_redirect('*/*/new');
							}
							return;
						} else if(count($collectionTitle->getData()) > 0 && !$this->getRequest()->getParam('id')){ // New Record
							Mage::getSingleton('adminhtml/session')->addError(Mage::helper('items')->__('Profile name already exists.'));
							if($this->getRequest()->getParam('id')){
								$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
							} else {
								Mage::getSingleton('adminhtml/session')->setFormData($data);
								$this->_redirect('*/*/new');
							}
							return;
						}
						try {
								$model  = Mage::getModel('items/meliproducttemplates');		
								$model	-> setData($data) ->setId($this->getRequest()->getParam('id'));
								if (!$this->getRequest()->getParam('id')) {
									$model->setDateCreated(now())
										 ->setLastUpdated(now());
								} else {
									$model->setLastUpdated(now());
								}	
								
								$model->save();
								Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('items')->__('Profile was saved successfully '));
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
				
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('items')->__('Unable to find Profile to save'));
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
				$model = Mage::getModel('items/meliproducttemplates');
				 
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Profile was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $itemsIds = $this->getRequest()->getParam('paymentprofile');
        if(!is_array($itemsIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select Profile(s)'));
        } else {
            try {
                foreach ($itemsIds as $itemsId) {
                    $items = Mage::getModel('items/meliproducttemplates')->load($itemsId);
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
	
    public function massStatusAction()
    {
        $itemsIds = $this->getRequest()->getParam('items');
        if(!is_array($itemsIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select Profile(s)'));
        } else {
            try {
                foreach ($itemsIds as $itemsId) {
                    $items = Mage::getSingleton('items/items')
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
  
    public function exportCsvAction()
    {
        $fileName   = 'items.csv';
        $content    = $this->getLayout()->createBlock('items/adminhtml_paymentprofile_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'items.xml';
        $content    = $this->getLayout()->createBlock('items/adminhtml_paymentprofile_grid')
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