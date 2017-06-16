<?php

class Mercadolibre_Items_Model_Mercadolibreorder extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('items/mercadolibreorder');
    }
	public function getAllOrdersFromAPIhit(){
		try{
					$commonModel = Mage::getModel('items/common');
					$storeId = Mage::helper('items')->_getStore()->getId();
					if(Mage::helper('items')->getMlAccessToken()){
						$access_token = Mage::helper('items')->getMlAccessToken();
					} else {
						$this->errorMessage = "Error :: Access Token Not Found OR Invalid";
						$commonModel->saveLogger($this->moduleName, "Exception", $this->fileName, $this->errorMessage);
						Mage::getSingleton('adminhtml/session')->addError(Mage::helper('items')->__('Access Token Not Found OR Invalid'));
						$this->_redirect('*/*/');
					}
					
					/* Get Base URL Id */
					if(Mage::getStoreConfig("mlitems/mltokenaccess/mlapiurl",Mage::app()->getStore($storeId))){
						 $api_url = Mage::getStoreConfig("mlitems/mltokenaccess/mlapiurl",Mage::app()->getStore($storeId));
					} else {
						$this->errorMessage = "Error :: Api Url Not Found OR Invalid";
						$commonModel->saveLogger($this->moduleName, "Exception", $this->fileName, $this->errorMessage);
						Mage::getSingleton('adminhtml/session')->addError(Mage::helper('items')->__('Api Url Not Found OR Invalid'));
						$this->_redirect('*/*/');
					}
					
					/* Get Meli Seller Id*/
					if(Mage::helper('items')->getMlSellerId()){
						$meli_sellerId = Mage::helper('items')->getMlSellerId();
					} else {
						$this->errorMessage = "Error :: Meli Seller Id Not Found OR Invalid";
						$commonModel->saveLogger($this->moduleName, "Exception", $this->fileName, $this->errorMessage);
						Mage::getSingleton('adminhtml/session')->addError(Mage::helper('items')->__('Meli Seller Id Not Found OR Invalid.'));
						$this->_redirect('*/*/');
					}
					
					/* Service Url For get All orders data */
					$service_url = $api_url.'/orders/search?seller='.$meli_sellerId.'&access_token='.$access_token;
					/* Get Call */      
					$responseAllOrdersData = $commonModel ->meliConnect($service_url,'GET');
						if(isset($responseAllOrdersData['statusCode']) && $responseAllOrdersData['statusCode'] == '200'){
						if(count($responseAllOrdersData['json']['results']) > 0){
							foreach($responseAllOrdersData['json']['results'] as $rowOrder){
								//create a new order in magento if config allows
								if(Mage::getStoreConfig("mlitems/meliordersyncsetting/enableinventoryded",Mage::app()->getStore($storeId)) == 1){
									require_once 'app/Mage.php';
									Mage::app();
									$transaction = Mage::getModel('core/resource_transaction');
									$meliorderID = '';
									$meliorderID  = $rowOrder['id'];
									$reservedOrderId = Mage::getSingleton('eav/config')->getEntityType('order')->fetchNewIncrementId($storeId);
									$mageEntityId	= Mage::getSingleton('sales/order')
													->getCollection()
													->addAttributeToSelect('entity_id')
													->addAttributeToFilter("meli_order_id", $meliorderID)
													->load();
									$mageEntityIdArr = $mageEntityId->getData();
									$mageEntityId = $mageEntityIdArr['0']['entity_id'];
									$order = Mage::getModel('sales/order')
											 ->setIncrementId($reservedOrderId)
											 ->setStoreId($storeId)
											 ->setMeliOrderId($meliorderID)
											 ->setQuoteId(0)
											 ->setGlobal_currency_code('USD')
											 ->setBase_currency_code('USD')
											 ->setStore_currency_code('USD')
											 ->setOrder_currency_code('USD');
									if($mageEntityId){
									 		$order ->setEntityId($mageEntityId);
									}
								}
								//ends create a new order in magento if config allows
							
								$response = array();
								$response['json'] = $rowOrder;
								/* Save/Update Buyer Info */
								$meliBuyerCollection  = Mage::getModel('items/melibuyer')-> getCollection()->addFieldToFilter('buyer_id',trim($response['json']['buyer']['id']));
								$meliBuyerArr = $meliBuyerCollection->getData();
								$meliBuyerModel = Mage::getModel('items/melibuyer');
								//create a new order in magento if config allows
								if(Mage::getStoreConfig("mlitems/meliordersyncsetting/enableinventoryded",Mage::app()->getStore($storeId)) ==1){
								// set Customer data
									$order->setCustomer_email($response['json']['buyer']['email'])
											->setCustomerFirstname($response['json']['buyer']['first_name'])
											->setCustomerLastname($response['json']['buyer']['last_name'])
											->setCustomerGroupId(1)
											->setCustomer_is_guest(0);
											
								}
								//ends create a new order in magento if config allows
								
								$data = array(
												'buyer_id' =>$response['json']['buyer']['id'],
												'nickname' =>$response['json']['buyer']['nickname'],
												'email' =>$response['json']['buyer']['email'],
												'first_name' =>$response['json']['buyer']['first_name'],
												'last_name' =>$response['json']['buyer']['last_name']
											);
								if(isset($meliBuyerArr['0']['id'])){
									$data['id'] = $meliBuyerArr['0']['id'];
								}
								//print_r($data); die;
								$meliBuyerModel->setData($data);
								$meliBuyerModel->save();
								//track the tags shipping status and payment status
								$allTags = array();
								$allTags = $response['json']['tags'];
								$payment_status = 0; $shipping_status=0;
								if(count($allTags)>0 && in_array('paid', $allTags)){
									$payment_status = 1; 
								}
								if(count($allTags)>0 && in_array('shipped', $allTags)){
									$shipping_status = 1;
								}
		
							/* Save/Update Order Data */
								$meliorderCollection  = Mage::getModel('items/mercadolibreorder')-> getCollection()->addFieldToFilter('order_id',trim($response['json']['id']));
								$meliOrderArr = $meliorderCollection->getData();
								$meliOrderModel = Mage::getModel('items/mercadolibreorder');
								$data = array(
												'order_id' =>$response['json']['id'],
												'date_created' =>$response['json']['date_created'],
												'date_closed' =>$response['json']['date_closed'],
												'status' =>$response['json']['status'],
												'status_detail' =>$response['json']['status_detail'],
												'buyer_id' =>$response['json']['buyer']['id'],
												'payment_status' =>$payment_status,
												'shipping_status' => $shipping_status,
												'total_amount' =>$response['json']['total_amount'],
												'currency_id' =>$response['json']['currency_id'],
												'tags' => serialize($response['json']['tags']),
												'store_id' =>$storeId 
											);
								if(count($response['json']['payments'])>0){
									if(isset($response['json']['payments']['id'])) $data['payment_id'] = trim($response['json']['payments']['id']);
									if(isset($response['json']['payments']['transaction_amount']))  $data['transaction_amount'] = $response['json']['payments']['transaction_amount'];									if(isset($response['json']['payments']['currency_id'])) $data['payment_currency_id'] = $response['json']['payments']['currency_id'];
									if(isset($response['json']['payments']['status'])) $data['payment_status'] = $response['json']['payments']['status'];
								}
								
								if(count($response['json']['shipping'])>0){
									if(isset($response['json']['shipping']['id'])) $data['shipping_id'] = $response['json']['shipping']['id'];
									//if(isset($response['json']['shipping']['status'])) $data['shipping_status'] = $response['json']['shipping']['status'];
									if(isset($response['json']['shipping']['shipment_type'])) $data['shipment_type'] = $response['json']['shipping']['shipment_type'];
									if(isset($response['json']['shipping']['date_created'])) $data['shipment_date_created'] = $response['json']['shipping']['date_created'];
									if(isset($response['json']['shipping']['cost'])) $data['shipping_cost'] = $response['json']['shipping']['cost']; 
									if(isset($response['json']['shipping']['currency_id'])) $data['shipping_currency_id'] = $response['json']['shipping']['currency_id'];
									if(isset($response['json']['shipping']['receiver_address'])) $data['receiver_address'] = serialize($response['json']['shipping']['receiver_address']); 
								
								}
								
								if(isset($meliOrderArr['0']['id'])){
									$data['id'] = $meliOrderArr['0']['id'];
								}
								//create a new order in magento if config allows
								if(Mage::getStoreConfig("mlitems/meliordersyncsetting/enableinventoryded",Mage::app()->getStore($storeId)) ==1){
									//set billing data
									$billingAddress = Mage::getModel('sales/order_address')
													->setStoreId($storeId)
													->setAddressType(Mage_Sales_Model_Quote_Address::TYPE_BILLING)
													->setCustomerId()
													->setCustomerAddressId(1)
													->setCustomer_address_id(1)
													->setPrefix()
													->setFirstname($response['json']['buyer']['first_name'])
													->setMiddlename()
													->setLastname($response['json']['buyer']['last_name']."(From Mercadolibre)")
													->setSuffix()
													->setCompany()
													->setStreet('')
													->setCity()
													->setCountry_id()
													->setRegion()
													->setRegion_id()
													->setPostcode()
													->setTelephone()
													->setFax();
									if($mageEntityId){
									 		$billingAddress ->setEntityId($mageEntityId);
									}
									$order->setBillingAddress($billingAddress);
									
									//set shipping data
									$shippingAddress = Mage::getModel('sales/order_address')
													->setStoreId($storeId)
													->setAddressType(Mage_Sales_Model_Quote_Address::TYPE_SHIPPING)
													->setCustomerId()
													->setCustomerAddressId(1)
													->setCustomer_address_id(1)
													->setPrefix()
													->setFirstname($response['json']['buyer']['first_name'])
													->setMiddlename()
													->setLastname($response['json']['buyer']['last_name']."(From Mercadolibre)")
													->setSuffix()
													->setCompany()
													->setStreet('')
													->setCity()
													->setCountry_id()
													->setRegion()
													->setRegion_id()
													->setPostcode()
													->setTelephone()
													->setFax();
									if($mageEntityId){
									 		$shippingAddress ->setEntityId($mageEntityId);
									}
									$order ->setShippingAddress($shippingAddress)
										   //->setShipping_method('flatrate_flatrate')
										   //->setShippingDescription('Flat Rate - Fixed');
										   ->setShipping_method('ML Test')
										   ->setShippingDescription('Managed By Mercadolibre');
									
									$orderPayment = Mage::getModel('sales/order_payment')
													->setStoreId($storeId)
													->setCustomerPaymentId(0)
													->setMethod('checkmo')
													->setPo_number(' - ');
									if($mageEntityId){
									 		$orderPayment ->setEntityId($mageEntityId);
									}
									$order->setPayment($orderPayment);
								}
								//ends create a new order in magento if config allows
								$meliOrderModel->setData($data);
								$meliOrderModel->save();
								
								/* Save/Update Order Item Data */
								if(count($response['json']['order_items'])>0){
									$subTotal = 0;
									foreach($response['json']['order_items'] as $key => $itemArray){
										$meliItemCollection  = Mage::getModel('items/meliorderitems')
															-> getCollection()
															->addFieldToFilter('order_id',trim($response['json']['id']))
															->addFieldToFilter('item_id',trim($itemArray['item']['id']));
										$meliItemArr = $meliItemCollection->getData();
										$meliItemModel = Mage::getModel('items/meliorderitems');
										$data = array(
														'order_id' =>$response['json']['id'],
														'item_id' =>$itemArray['item']['id'],
														'title' =>$itemArray['item']['title'],
														'variation_id' =>$itemArray['item']['variation_id'],
														'quantity' =>$itemArray['quantity'],
														'unit_price' =>$itemArray['unit_price'],
														'currency_id' =>$itemArray['currency_id']
													);
										if(isset($meliItemArr['0']['id'])){
											$data['id'] = $meliItemArr['0']['id'];
										}
										$meliItemModel->setData($data);
										$meliItemModel->save();
										
									//create a new order in magento if config allows
									if(Mage::getStoreConfig("mlitems/meliordersyncsetting/enableinventoryded",Mage::app()->getStore($storeId)) ==1){
										// set items data
										$rowTotal = $itemArray['unit_price'] * $itemArray['quantity'];
										$orderItem = Mage::getModel('sales/order_item')
													->setOrderId($mageEntityId)
													->setStoreId($storeId)
													->setQuoteItemId(0)
													->setQuoteParentItemId(NULL)
													->setProductId($itemArray['item']['id'])
													->setProductType()
													->setQtyBackordered(NULL)
													->setTotalQtyOrdered($itemArray['quantity'])
													->setQtyOrdered($itemArray['quantity'])
													->setName($itemArray['item']['title'])
													->setSku()
													->setPrice($itemArray['unit_price'])
													->setBasePrice($itemArray['unit_price'])
													->setOriginalPrice($itemArray['unit_price'])
													->setRowTotal($rowTotal)
													->setBaseRowTotal($rowTotal);
										if($mageEntityId){
									 		$orderItem ->setItemId($mageEntityId);
										}
										$subTotal += $rowTotal;
										$order->addItem($orderItem);
									}
									//ends create a new order in magento if config allows
									
									/* Save/Update variation attributes  */
										if(count($itemArray['item']['variation_attributes']) > 0){
											foreach($itemArray['item']['variation_attributes'] as $keyVari => $variationArr){
												$meliItemVariCollection = Mage::getModel('items/meliordervariationattributes')
																	-> getCollection()
																	->addFieldToFilter('order_id',trim($response['json']['id']))
																	->addFieldToFilter('item_id',trim($itemArray['item']['id']))
																	->addFieldToFilter('variation_id',trim($itemArray['item']['variation_id']))
																	->addFieldToFilter('attribute_id',trim($variationArr['id']));
												$meliItemVariArr =  $meliItemVariCollection->getData();
												$meliItemModel = Mage::getModel('items/meliordervariationattributes');
												$data = array(
																'item_id'		=> $itemArray['item']['id'],
																'order_id'		=> $response['json']['id'],
																'attribute_id'	=>$variationArr['id'],
																'variation_id'	=>$itemArray['item']['variation_id'],
																'name'			=>$variationArr['name'],
																'value_id'		=>$variationArr['value_id'],
																'value_name'	=>$variationArr['value_name']
															);
												if(isset($meliItemVariArr['0']['id'])){
													$data['id'] = $meliItemVariArr['0']['id'];
												}
												$meliItemModel->setData($data);
												$meliItemModel->save();
											}
										}
									}
								}
								//create a new order in magento if config allows
								if(Mage::getStoreConfig("mlitems/meliordersyncsetting/enableinventoryded",Mage::app()->getStore($storeId)) ==1){
									$order->setSubtotal($subTotal)
											->setStoreId($storeId)
											->setBaseSubtotal($subTotal)
											->setGrandTotal($subTotal)
											->setBaseGrandTotal($subTotal)
											->setCustomerNote('This order has been cerated at Mercadolibre');
									/*if($mageEntityId){
									 $order = Mage::getModel('sales/order')->setEntityId($mageEntityId);
									}*/
									$transaction->addObject($order);
									$transaction->addCommitCallback(array($order, 'place'));
									$transaction->addCommitCallback(array($order, 'save'));
									$transaction->save();
								
								
									//save the order id to mercadolibre table
									$mlLatestOrderArr = array();
									$mlLatestOrderColl	= Mage::getSingleton('sales/order')
														->getCollection()
														->addAttributeToSelect('entity_id')
														->addAttributeToSelect('increment_id')
														->addAttributeToFilter("meli_order_id", $meliorderID)
														->load();
									$mlLatestOrderArr = $mlLatestOrderColl->getData();
									$mageOrderId = $mlLatestOrderArr['0']['entity_id'];
									$mageOrderNumber = $mlLatestOrderArr['0']['increment_id'];
									
									if(isset($meliOrderArr['0']['id'])){
										$meliorderModelLoad = Mage::getModel('items/mercadolibreorder');
										$dataOrder = array(
														'id' => $meliOrderArr['0']['id'],
														'mage_order_Id'	=> $mageOrderId,
														'mage_order_number'	=> $mageOrderNumber
													);
										$meliorderModelLoad->setData($dataOrder);
										$meliorderModelLoad->save();
										
									}
							}
								
								//ends create a new order in magento if config allows
							
							}
					}
				}else {
					Mage::getSingleton('adminhtml/session')->addError(Mage::helper('items')->__('Order(s) does not exist.'));
					$this->_redirect('*/*/');
				}
			}catch(Exception $e){
				$commonModel->saveLogger($this->moduleName, "Exception", $this->fileName, $e->getMessage());
			}			
}
}