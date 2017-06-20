<?php

class Mercadolibre_Items_Adminhtml_ItemordersController extends Mage_Adminhtml_Controller_action
{
	private $moduleName = "Items";
	private $fileName = "ItemordersController.php";
	
	//message variable
	private $infoMessage = "";
	private $errorMessage = "";
	private $successMessage = "";
	private $errorMessageLog = "";
	
	
	protected function _initAction() {
	
		$this->loadLayout()
			->_setActiveMenu('items/orders')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Orders Manager'), Mage::helper('adminhtml')->__('Order Manager'));
		
		return $this;
	}
	
	public function indexAction() {
		Mage::helper('items')->_getStore()->getId();
		$this->_initAction()->renderLayout(); 
	}
	
	public function itemOrderAction() {
			$this->getAllMLOrders();
			$this->_redirect('*/*/');
	}
 
	//public function cancelItemOrderAction(){
//					$orderId = $this->getRequest()->getParam('orderid');
//					$commonModel = Mage::getModel('items/common');
//					if(Mage::helper('items')->getMlAccessToken()){
//						$access_token = Mage::helper('items')->getMlAccessToken();
//					} else {
//						$this->errorMessage = "Error :: Access Token Not Found OR Invalid";
//						$commonModel->saveLogger($this->moduleName, "Exception", $this->fileName, $this->errorMessage);
//						Mage::getSingleton('adminhtml/session')->addError(Mage::helper('items')->__('Access Token Not Found OR Invalid'));
//						$this->_redirect('*/*/');
//					}
//					
//					/* Get Base URL Id */
//					if(Mage::getStoreConfig("mlitems/mltokenaccess/mlapiurl",Mage::app()->getStore())){
//						 $api_url = Mage::getStoreConfig("mlitems/mltokenaccess/mlapiurl",Mage::app()->getStore());
//					} else {
//						$this->errorMessage = "Error :: Api Url Not Found OR Invalid";
//						$commonModel->saveLogger($this->moduleName, "Exception", $this->fileName, $this->errorMessage);
//						Mage::getSingleton('adminhtml/session')->addError(Mage::helper('items')->__('Api Url Not Found OR Invalid'));
//						$this->_redirect('*/*/');
//					}
//					
//					/* Get Meli Seller Id*/
//					if(Mage::helper('items')->getMlSellerId()){
//						$meli_sellerId = Mage::helper('items')->getMlSellerId();
//					} else {
//						$this->errorMessage = "Error :: Meli Seller Id Not Found OR Invalid";
//						$commonModel->saveLogger($this->moduleName, "Exception", $this->fileName, $this->errorMessage);
//						Mage::getSingleton('adminhtml/session')->addError(Mage::helper('items')->__('Meli Seller Id Not Found OR Invalid.'));
//						$this->_redirect('*/*/');
//					}
//					
//					/* Service Url For get All orders data */
//					$service_url = $api_url.'/orders/search?seller='.$meli_sellerId.'&access_token='.$access_token;
//					/* Get Call */      
//					$responseUpdateStatus = $commonModel ->meliConnect($service_url,'PUT');
//					
//						if(isset($responseUpdateStatus['statusCode']) && $responseUpdateStatus['statusCode'] == '200'){
//						//update our table
//						
//						
//						
//						}
//				$this->_redirect('*/*/index');
//				return;
//	}
	
	//public function shippedItemOrderAction(){
//					$orderId = $this->getRequest()->getParam('orderid');
//					$commonModel = Mage::getModel('items/common');
//					if(Mage::helper('items')->getMlAccessToken()){
//						$access_token = Mage::helper('items')->getMlAccessToken();
//					} else {
//						$this->errorMessage = "Error :: Access Token Not Found OR Invalid";
//						$commonModel->saveLogger($this->moduleName, "Exception", $this->fileName, $this->errorMessage);
//						Mage::getSingleton('adminhtml/session')->addError(Mage::helper('items')->__('Access Token Not Found OR Invalid'));
//						$this->_redirect('*/*/');
//					}
//					
//					/* Get Base URL Id */
//					if(Mage::getStoreConfig("mlitems/mltokenaccess/mlapiurl",Mage::app()->getStore())){
//						 $api_url = Mage::getStoreConfig("mlitems/mltokenaccess/mlapiurl",Mage::app()->getStore());
//					} else {
//						$this->errorMessage = "Error :: Api Url Not Found OR Invalid";
//						$commonModel->saveLogger($this->moduleName, "Exception", $this->fileName, $this->errorMessage);
//						Mage::getSingleton('adminhtml/session')->addError(Mage::helper('items')->__('Api Url Not Found OR Invalid'));
//						$this->_redirect('*/*/');
//					}
//					
//					/* Get Meli Seller Id*/
//					if(Mage::helper('items')->getMlSellerId()){
//						$meli_sellerId = Mage::helper('items')->getMlSellerId();
//					} else {
//						$this->errorMessage = "Error :: Meli Seller Id Not Found OR Invalid";
//						$commonModel->saveLogger($this->moduleName, "Exception", $this->fileName, $this->errorMessage);
//						Mage::getSingleton('adminhtml/session')->addError(Mage::helper('items')->__('Meli Seller Id Not Found OR Invalid.'));
//						$this->_redirect('*/*/');
//					}
//					
//					/* Service Url For get All orders data */
//					$service_url = $api_url.'/orders/search?seller='.$meli_sellerId.'&access_token='.$access_token;
//					/* Get Call */      
//					$responseShippedStatus = $commonModel ->meliConnect($service_url,'PUT');
//						if(isset($responseShippedStatus['statusCode']) && $responseShippedStatus['statusCode'] == '200'){
//						//update our table
//						
//						
//						}
//				$this->_redirect('*/*/index');
//				return;
//	}
	
	public function putAction(){
		try{
			$orderUpdatddata = array();
			$commonModel = Mage::getModel('items/common');
			$orderStatusArr = $this->getRequest()->getPost('order_status');
			$shippingStatusArr = $this->getRequest()->getPost('shipping_status');
			$orderIdsArr = $this->getRequest()->getPost('order_id');
			$orderCount = 0;
			if(count($orderIdsArr) > 0){
				$countToPublish = 0;
				foreach($orderIdsArr as $key => $orderId){
						$checkboxOn = $this->getRequest()->getPost('checkbox_'.$orderId);
						if($checkboxOn == 'on'){
							$orderCount++;
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
							if($orderStatusArr[$key] && trim($access_token)!='' && trim($apiUrl)!=''){
								$meliorderCollection  = Mage::getModel('items/mercadolibreorder')-> getCollection()->addFieldToFilter('order_id',trim($orderId));
								$meliOrderArr = $meliorderCollection->getData();
								//$ModelOrderListing  = Mage::getModel('items/mercadolibreorder')->load(trim($orderId));
								//echo $orderId; die;
								//echo $ModelOrderListing->getData('status'); die;
								
								$dataValArr = array();
								if(isset($orderStatusArr) && $orderStatusArr[$key]!=''){
									$dataValArr[] = $orderStatusArr[$key];
								}
								if(isset($shippingStatusArr) && $shippingStatusArr[$key]!=''){
									$dataValArr[] = $shippingStatusArr[$key];
								}
								$data = array('tags'=>$dataValArr);
								$requestData = '';
								$requestData = json_encode($data);
								$requestUrl = $apiUrl.'/orders/'.$orderId.'?access_token='.$access_token;
								$response = $commonModel-> meliConnect($requestUrl,'PUT',$requestData);
								/* Update mercadolibre_order data on Successfull Responce - 200  */
								if(isset($response['statusCode']) && $response['statusCode'] == 200 && isset($response['json']) && is_array($response['json'])){
								$meliOrderModel = Mage::getModel('items/mercadolibreorder');
								$orderUpdatddata = array(
												'order_id' =>$response['json']['id'],
												'date_created' =>$response['json']['date_created'],
												'date_closed' =>$response['json']['date_closed'],
												'status' =>$response['json']['status'],
												'status_detail' =>$response['json']['status_detail'],
												'buyer_id' =>$response['json']['buyer']['id'],
												'payment_status' =>'pending',
												'shipping_status' => 'pending',
												'total_amount' =>$response['json']['total_amount'],
												'currency_id' =>$response['json']['currency_id'],
												'tags' => serialize($response['json']['tags'])
											);
								if(count($response['json']['payments'])>0){
									if(isset($response['json']['payments']['id'])) $orderUpdatddata['payment_id'] = trim($response['json']['payments']['id']);
									if(isset($response['json']['payments']['transaction_amount']))  $orderUpdatddata['transaction_amount'] = $response['json']['payments']['transaction_amount'];									if(isset($response['json']['payments']['currency_id'])) $orderUpdatddata['payment_currency_id'] = $response['json']['payments']['currency_id'];
									if(isset($response['json']['payments']['status'])) $orderUpdatddata['payment_status'] = $response['json']['payments']['status'];
								}
								
								if(count($response['json']['shipping'])>0){
									if(isset($response['json']['shipping']['id'])) $orderUpdatddata['shipping_id'] = $response['json']['shipping']['id'];
									if(isset($response['json']['shipping']['status'])) $orderUpdatddata['shipping_status'] = $response['json']['shipping']['status'];
									if(isset($response['json']['shipping']['shipment_type'])) $orderUpdatddata['shipment_type'] = $response['json']['shipping']['shipment_type'];
									if(isset($response['json']['shipping']['date_created'])) $orderUpdatddata['shipment_date_created'] = $response['json']['shipping']['date_created'];
									if(isset($response['json']['shipping']['cost'])) $orderUpdatddata['shipping_cost'] = $response['json']['shipping']['cost']; 
									if(isset($response['json']['shipping']['currency_id'])) $orderUpdatddata['shipping_currency_id'] = $response['json']['shipping']['currency_id'];
									if(isset($response['json']['shipping']['receiver_address'])) $orderUpdatddata['receiver_address'] = serialize($response['json']['shipping']['receiver_address']); 
								}
								if(isset($meliOrderArr['0']['id'])){
									$orderUpdatddata['id'] = $meliOrderArr['0']['id'];
								}
								$meliOrderModel->setData($orderUpdatddata);
								$meliOrderModel->save();
									
								} else if(isset($response['json']['message']))  {
									$this->errorMessage .= "Error :: Order Id (". $orderId .")". $response['json']['message'] .' '.$response['json']['status'].' ' . $response['json']['error']." <br />";
									$this->errorMessageLog = "Error :: Order Id (". $orderId .")". $response['json']['message'] .' '.$response['json']['status'].' ' . $response['json']['error'];
									if(isset($response['json']['cause']) && count ($response['json']['cause']) > 0 ){
									$this->errorMessage .= "Error Cause :: ";
									foreach($response['json']['cause'] as $row){
										$this->errorMessage .= $row['cause'].' : '.$row['message'];
									}
								}
							}
							}
						}
				}
			}
			if($orderCount==0){
			$this->errorMessage = "Error ::Please select atleast one order to change status";
			}
		}catch(Exception $e){
			$commonModel->saveLogger($this->moduleName, "Exception", $this->fileName, $e->getMessage());	
		}
		if($this->errorMessage){
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('items')->__($this->errorMessage));
		}else{
			Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('items')->__("Orders status has been updated successfully."));
		}
		$this->_redirect('*/*/index');
		return;
		
	}
	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('items/melicategories')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('items_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('items/items');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('items/adminhtml_items_edit'))
				->_addLeft($this->getLayout()->createBlock('items/adminhtml_items_edit_tabs'));

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
			
			if(isset($_FILES['filename']['name']) && $_FILES['filename']['name'] != '') {
				try {	
					/* Starting upload */	
					$uploader = new Varien_File_Uploader('filename');
					
					// Any extention would work
	           		$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
					$uploader->setAllowRenameFiles(false);
					
					// Set the file upload mode 
					// false -> get the file directly in the specified folder
					// true -> get the file in the product like folders 
					//	(file.jpg will go in something like /media/f/i/file.jpg)
					$uploader->setFilesDispersion(false);
							
					// We set media as the upload dir
					$path = Mage::getBaseDir('media') . DS ;
					$uploader->save($path, $_FILES['filename']['name'] );
					
				} catch (Exception $e) {
		      
		        }
	        
		        //this way the name is saved in DB
	  			$data['filename'] = $_FILES['filename']['name'];
			}
	  			
	  			
			$model = Mage::getModel('items/melicategories');		
			$model->setData($data)
				->setId($this->getRequest()->getParam('id'));
			
			try {
				if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
					$model->setCreatedTime(now())
						->setUpdateTime(now());
				} else {
					$model->setUpdateTime(now());
				}	
				
				$model->save();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('items')->__('Item was successfully saved'));
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
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('items')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('items/melicategories');
				 
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

	public function massMlOrderPaidAction()
    {
        $itemsIds = $this->getRequest()->getParam('orderid');
		
		$commonModel = Mage::getModel('items/common');
		if(Mage::helper('items')->getMlAccessToken()){
			$access_token = Mage::helper('items')->getMlAccessToken();
		} else {
			$this->errorMessage = "Error :: Access Token Not Found OR Invalid";
			$commonModel->saveLogger($this->moduleName, "Exception", $this->fileName, $this->errorMessage);
			$commonModel->sendNotificationMail($this->to, 'Notifications Error', $this->errorMessage);
		}
		
		/* Get Base URL Id */
		if(Mage::getStoreConfig("mlitems/mltokenaccess/mlapiurl",Mage::app()->getStore())){
			 $apiUrl = Mage::getStoreConfig("mlitems/mltokenaccess/mlapiurl",Mage::app()->getStore());
		} else {
			$this->errorMessage = "Error :: Api Url Not Found OR Invalid";
			$commonModel->saveLogger($this->moduleName, "Exception", $this->fileName, $this->errorMessage);
			$commonModel->sendNotificationMail($this->to, 'Notifications Error', $this->errorMessage);
		}	
		
		if(!is_array($itemsIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select order(s)'));
        } else {
            try {
				//get all the unpaid order from the selected list
				$meliorderCollection  = Mage::getModel('items/mercadolibreorder')-> getCollection()
							  ->addFieldToFilter('id',$itemsIds)
							  ->addFieldToFilter('payment_status',0)
							  ->addFieldToSelect('order_id')
							  ->addFieldToSelect('id');
				$meliOrderIdsArr = $meliorderCollection->getData();
				$countRecords = 0;
				foreach ($meliOrderIdsArr as $orderIds) {
					$orderId = $orderIds['order_id'];
					$Id = $orderIds['id'];
					$dataValArr  = array('paid');
					$data = array('tags'=>$dataValArr);
					$requestData = '';
					$requestData = json_encode($data);
					$requestUrl = $apiUrl.'/orders/'.$orderId.'?access_token='.$access_token;
					$response = $commonModel-> meliConnect($requestUrl,'PUT',$requestData);
					/* Update mercadolibre_order data on Successfull Responce - 200  */
					if(isset($response['statusCode']) && $response['statusCode'] == 200 && isset($response['json']) && is_array($response['json'])){
						//update our table
						$items  = Mage::getSingleton('items/mercadolibreorder')
								->load($orderId)
								->setPaymentStatus('1')
								->setId($Id)
								->save();
						$countRecords++;
					}
					}
				if($countRecords>0){
					$this->_getSession()->addSuccess(
						$this->__('Total of %d record(s) were successfully updated', count($meliOrderIdsArr))
					);
				}else{
					$this->_getSession()->addError( $this->__('Unable to change the payment status'));
				}
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	
	
	public function massMlOrderShippedAction()
    {
        $itemsIds = $this->getRequest()->getParam('orderid');
		$commonModel = Mage::getModel('items/common');
		if(Mage::helper('items')->getMlAccessToken()){
			$access_token = Mage::helper('items')->getMlAccessToken();
		} else {
			$this->errorMessage = "Error :: Access Token Not Found OR Invalid";
			$commonModel->saveLogger($this->moduleName, "Exception", $this->fileName, $this->errorMessage);
			$commonModel->sendNotificationMail($this->to, 'Notifications Error', $this->errorMessage);
		}
		
		/* Get Base URL Id */
		if(Mage::getStoreConfig("mlitems/mltokenaccess/mlapiurl",Mage::app()->getStore())){
			 $apiUrl = Mage::getStoreConfig("mlitems/mltokenaccess/mlapiurl",Mage::app()->getStore());
		} else {
			$this->errorMessage = "Error :: Api Url Not Found OR Invalid";
			$commonModel->saveLogger($this->moduleName, "Exception", $this->fileName, $this->errorMessage);
			$commonModel->sendNotificationMail($this->to, 'Notifications Error', $this->errorMessage);
		}	
		
		if(!is_array($itemsIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select order(s)'));
        } else {
            try {
				//get all the unpaid order from the selected list
				$meliorderCollection  = Mage::getModel('items/mercadolibreorder')-> getCollection()
							  ->addFieldToFilter('id',$itemsIds)
							  ->addFieldToFilter('shipping_status',0)
							  ->addFieldToSelect('order_id')
							  ->addFieldToSelect('id');
				$meliOrderIdsArr = $meliorderCollection->getData();
				$countRecords = 0;
				foreach ($meliOrderIdsArr as $orderIds) {
					$orderId = $orderIds['order_id'];
					$Id = $orderIds['id'];
					$dataValArr  = array('shipped');
					$data = array('tags'=>$dataValArr);
					$requestData = '';
					$requestData = json_encode($data);
					$requestUrl = $apiUrl.'/orders/'.$orderId.'?access_token='.$access_token;
					$response = $commonModel-> meliConnect($requestUrl,'PUT',$requestData);
					/* Update mercadolibre_order data on Successfull Responce - 200  */
					if(isset($response['statusCode']) && $response['statusCode'] == 200 && isset($response['json']) && is_array($response['json'])){
						//update our table
						$items  = Mage::getSingleton('items/mercadolibreorder')
								->load($orderId)
								->setShippingStatus('1')
								->setId($Id)
								->save();
						$countRecords++;
					}
					}
                if($countRecords>0){
					$this->_getSession()->addSuccess(
						$this->__('Total of %d record(s) were successfully updated', count($meliOrderIdsArr))
					);
				}else{
					$this->_getSession()->addError( $this->__('Unable to change the Shipping status'));
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
        $content    = $this->getLayout()->createBlock('items/adminhtml_items_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'items.xml';
        $content    = $this->getLayout()->createBlock('items/adminhtml_items_grid')
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
	
	public function getAllMLOrders()
    {
        $model = Mage::getModel('items/mercadolibreorder');
        $model->getAllOrdersFromAPIhit();
    }
}