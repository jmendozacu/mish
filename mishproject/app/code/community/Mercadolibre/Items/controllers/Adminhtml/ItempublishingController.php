<?php
ob_start("ob_gzhandler");
ini_set('max_execution_time',0);
ini_set('memory_limit','1024M');
ini_set('max_input_time',0);
class Mercadolibre_Items_Adminhtml_ItempublishingController extends Mage_Adminhtml_Controller_action
{

	private $moduleName = "Items";
	private $fileName = "ItemPublishingController.php";
	
	//message variable
	private $infoMessage = "";
	private $errorMessage = "";
	private $successMessage = "";
	private $errorMessageLog = "";
	
	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('items/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Publish Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

	public function postAction() {
		try{
			$data = array();
			$successItemIdsArr = array();
			$errorItemIdsArr = array();
			$successItemIds = '';
			
			$commonModel = Mage::getModel('items/common');
			$product_idArr = $this->getRequest()->getPost('product_id');
			
			$storeId = Mage::helper('items')->_getStore()->getId();
			$commonModel = Mage::getModel('items/common');
			if(count($product_idArr) > 0){
				$countToPublish = 0;
				foreach($product_idArr as $key => $productId){
						
						$checkboxOn = $this->getRequest()->getPost('checkbox_'.$productId);
						if($checkboxOn == 'on'){
							$countToPublish++;
					
							$ModelItemListing2  =  Mage::getModel('items/mercadolibreitem')
												-> getCollection()
												-> addFieldToFilter('main_table.store_id',$storeId)
												-> addFieldToFilter('main_table.product_id',$productId);
							$ModelItemListing2 	-> getSelect()
											  	-> joinleft(array('mmt'=>'mercadolibre_master_templates'), "main_table.master_temp_id = mmt.master_temp_id", array('mmt.master_temp_title','mmt.template_id','mmt.shipping_id','mmt.profile_id', 'mmt.payment_id as payment_method_id'));
							$itemIdArr = $ModelItemListing2->getData();
							$template_id = '';
							$shipping_id = '';
							$profile_id = '';
							$payment_method_id = '';
							
							$template_id = $itemIdArr['0']['template_id'];
							$shipping_id = $itemIdArr['0']['shipping_id'];
							$profile_id = $itemIdArr['0']['profile_id'];
							$payment_method_id = $itemIdArr['0']['payment_method_id'];
							$itemId =  $itemIdArr['0']['item_id'];
							
							$ModelItemListing =  Mage::getModel('items/mercadolibreitem')->load($itemId);
							$ModelMeliCat 	  =  Mage::getModel('items/melicategoriesmapping')
											  -> getCollection()
											  -> addFieldToFilter('mage_category_id',$ModelItemListing->getData('category_id'))
											  -> addFieldToFilter('store_id',$storeId);
							
							$ModelMeliCatArr = $ModelMeliCat->getData();
							if(trim($ModelItemListing->getData('meli_item_id'))!=''){  // if item already published
									$ModelMeliCatArr['0']['meli_category_id'] = $ModelItemListing->getData('meli_category_id');
							}
														
							$ModelProduct	 = Mage::getModel('catalog/product')->load($ModelItemListing->getData('product_id'));
							$MediaGalleryArr = $ModelProduct->getMediaGallery('images');
							$ModelTemplate	 = Mage::getModel('items/meliproducttemplates')->load($template_id);
							
							$ModelShipping	 = Mage::getModel('items/melishipping')->load($shipping_id);
							$shippingCosts = array();
							if(trim($ModelShipping->getData('shipping_mode')) == 'custom'){
								$shippingCustomColl	 =  Mage::getModel('items/melishipping')-> getCollection()
													-> addFieldToFilter('msc.shipping_id',$shipping_id);
													//-> addFieldToFilter('msc.store_id',$storeId);	
								$shippingCustomColl	-> getSelect()
													-> join(array('msc'=>'mercadolibre_shipping_custom'), "main_table.shipping_id = msc.shipping_id", array('msc.*'));
						
								foreach($shippingCustomColl->getData() as $rowCustom){
									$shippingCosts[] = (object) array('description' => $rowCustom['shipping_service_name'],'cost' => $rowCustom['shipping_cost']);
								}
								
								if(count($shippingCosts) > 0){
									$shipping =  array('local_pick_up' => true,'free_shipping' => false,'costs' => $shippingCosts);
								}
							} else {
								$shipping = array('mode'=>$ModelShipping->getData('shipping_mode'));
							}
							
							$itemProfileDetailModel = Mage::getModel('items/meliitemprofiledetail')->load($profile_id);
							
							/*----------------------*/
							$meliattributevaluemapping  =  Mage::getModel('items/meliattributevaluemapping')
														-> getCollection()
														-> addFieldToFilter('main_table.store_id',$storeId)
														-> addFieldToFilter('main_table.mage_attribute','color'); 
							$meliattributevaluemapping 	-> getSelect()->limit(1);
							$mage_attribute_original = '';
							if(count($meliattributevaluemapping -> getData()) > 0){
								$meliattributevaluemappingArr = $meliattributevaluemapping -> getData();
								$mage_attribute_original = trim($meliattributevaluemappingArr['0']['mage_attribute_original']);
							}
									
							$_product = Mage::getModel('catalog/product')->load($productId);
							$seller_custom_field = '';
							$seller_custom_field = $_product->getData('sku');
							
							if($_product->getData('type_id') == 'configurable'){
	
								$associated_prods = $_product->getTypeInstance()->getUsedProducts();
								$html = '';
								$variations = array();
								foreach ($associated_prods as $assoc_product) {

									$productAssoId ='';		
									$productAssoId = $assoc_product->getId(); 
									$MediaGalleryArr = array();
									$ModelProductAsso = Mage::getModel('catalog/product')->load($productAssoId);
									$MediaGalleryArr = $ModelProductAsso->getMediaGallery('images');
									$seller_custom_field = '';
									$seller_custom_field = $ModelProductAsso->getData('sku');

									$meliImageArr = array();
									$pictureArr = array();
									$pictureArrVariation = array();
									  if(is_array($MediaGalleryArr) && count($MediaGalleryArr) > 0){
										   foreach ($MediaGalleryArr as $row){
											 $pictureArr[] = array(
												  'source'=> Mage::getBaseUrl('media').'catalog/product' . $row['file'],
												  );
											 $pictureArrVariation[] = Mage::getBaseUrl('media').'catalog/product' . $row['file'];
										   }
										}
										else if(file_exists($commonModel->getChangedSlash(Mage::getBaseDir('code').DS.'local\CJM\ColorSelectorPlus\Helper\Data.php')) && class_exists(get_class(Mage::helper('colorselectorplus')))){
											$html='';
											$imageFile = '';
											$ImageColorId = '';
											if(trim($mage_attribute_original) != ''){
												$ImageColorId = $ModelProductAsso->getData($mage_attribute_original);
											}
											
											$colorselectorplus_product_base = Mage::helper('colorselectorplus')->decodeImages($_product);
											if(count($colorselectorplus_product_base)>0 && count($colorselectorplus_product_base['full']) > 0){
												foreach ($colorselectorplus_product_base['full'] as $imgKey => $_colorselectorplus_image){
													if($colorselectorplus_product_base['morev'][$imgKey] == $ImageColorId){
														$pictureArr[] = array('source'=> $_colorselectorplus_image);
														$pictureArrVariation[] = $_colorselectorplus_image;
													}
												} 
											}
										}	
									
									$ModelItemAttributeCon  =  Mage::getModel('items/meliitemattributes')
														   -> getCollection()
														   -> addFieldToFilter('main_table.store_id',$storeId)
														   -> addFieldToFilter('main_table.item_id',$productAssoId); 
									$ModelItemAttributeCon -> getSelect()
														   -> joinleft(array('melicatatt'=>'mercadolibre_category_attributes'), " main_table.meli_attribute_id = melicatatt.meli_attribute_id and melicatatt.category_id = '".$ModelMeliCatArr['0']['meli_category_id']."'", array('melicatatt.meli_attribute_id','melicatatt.meli_attribute_name'))
														   -> joinleft(array('melicatattval'=>'mercadolibre_category_attribute_values'), "main_table.meli_value_id = melicatattval.meli_value_id and melicatattval.meli_category_id = '".$ModelMeliCatArr['0']['meli_category_id']."' ", array('melicatattval.meli_value_id','melicatattval.meli_value_name'));
								
								$attribute_combinations = array();
								if(count($ModelItemAttributeCon->getData()) > 0){
									foreach($ModelItemAttributeCon->getData() as $row){
										$attribute_combinations[] = (object) array('id'=>$row['meli_attribute_id'],'value_id' => $row['meli_value_id']);
									}
								}
								
									$PriceQtyAttributeCon  =  Mage::getModel('items/meliitempriceqty')
														   -> getCollection()
														    -> addFieldToFilter('main_table.store_id',$storeId) 
														   -> addFieldToFilter('main_table.item_id',$productAssoId) 
														   -> addFieldToFilter('main_table.meli_price',array('gt'=>'0.00'))
														   -> addFieldToFilter('main_table.meli_qty',array('gt'=>0)); 									

									$priceQtyArr = $PriceQtyAttributeCon->getData();
									if(count($PriceQtyAttributeCon->getData()) > 0){
										 if(count($attribute_combinations) >0 && count($pictureArrVariation) > 0){
											   $variations[] = (object) array('attribute_combinations'=>$attribute_combinations,'available_quantity' => (int)$priceQtyArr['0']['meli_qty'],'price' => (int)$priceQtyArr['0']['meli_price'],'picture_ids' =>$pictureArrVariation,'seller_custom_field' => $seller_custom_field);
											 
										  }else if(count($attribute_combinations) > 0){
											   $variations[] = (object) array('attribute_combinations'=>$attribute_combinations,'available_quantity' => (int)$priceQtyArr['0']['meli_qty'],'price' => (int)$priceQtyArr['0']['meli_price'],'seller_custom_field' => $seller_custom_field);
										  }	
									 }
								}
							} else {	
							
							/*----------Simple Product----------------*/	
							$ModelItemAttribute  = Mage::getModel('items/meliitemattributes')
												-> getCollection()
												-> addFieldToFilter('main_table.store_id',$storeId)
												-> addFieldToFilter('main_table.item_id',$productId);
							$ModelItemAttribute -> getSelect()
												-> joinleft(array('melicatatt'=>'mercadolibre_category_attributes'), " main_table.meli_attribute_id = melicatatt.meli_attribute_id and melicatatt.category_id = '".$ModelMeliCatArr['0']['meli_category_id']."' ", array('melicatatt.meli_attribute_id','melicatatt.meli_attribute_name'))
											    -> joinleft(array('melicatattval'=>'mercadolibre_category_attribute_values'), "main_table.meli_value_id = melicatattval.meli_value_id  and melicatattval.meli_category_id = '".$ModelMeliCatArr['0']['meli_category_id']."' ", array('melicatattval.meli_value_id','melicatattval.meli_value_name'));	
							
								
							$meliImageArr = array();
							  if(trim($ModelItemListing->getData('pictures'))!=''){
								   $meliImageArr =  unserialize(stripslashes($ModelItemListing->getData('pictures')));
								  }
								  $pictureArr = array();
								  $pictureArrVariation = array();
							  if(is_array($MediaGalleryArr) && count($MediaGalleryArr) > 0){
								   foreach ($MediaGalleryArr as $row){
									if(is_array($meliImageArr) && in_array($row['value_id'],$meliImageArr)){
									 $pictureArr[] = array(
										  'source'=> Mage::getBaseUrl('media').'catalog/product' . $row['file'],
										  );
									 $pictureArrVariation[] = Mage::getBaseUrl('media').'catalog/product' . $row['file'];
										  
									 
									}
								   }
								  }
													
								$variations = array();
								if(count($ModelItemAttribute->getData()) > 0){
									foreach($ModelItemAttribute->getData() as $row){
										$attribute_combinations[] = (object) array('id'=>$row['meli_attribute_id'],'value_id' => $row['meli_value_id']);
									}
								}
								$PriceQtyAttributeCon  = Mage::getModel('items/meliitempriceqty')
													  -> getCollection()
													  -> addFieldToFilter('main_table.store_id',$storeId)
													  -> addFieldToFilter('main_table.item_id',$productId)
													  -> addFieldToFilter('main_table.meli_price',array('gt'=>'0.00'))
													   -> addFieldToFilter('main_table.meli_qty',array('gt'=>0)); 					  
 					  
													  
								$priceQtyArr = $PriceQtyAttributeCon->getData();
								 if(count($attribute_combinations) > 0 && count($pictureArrVariation) > 0){
									   $variations[] = (object) array('attribute_combinations'=>$attribute_combinations,'available_quantity' => (int)$priceQtyArr['0']['meli_qty'],'price' => (int)$priceQtyArr['0']['meli_price'],'picture_ids' =>$pictureArrVariation,'seller_custom_field' => $seller_custom_field);
									 
								  }else if(count($attribute_combinations) > 0){
									   $variations[] = (object) array('attribute_combinations'=>$attribute_combinations,'available_quantity' => (int)$priceQtyArr['0']['meli_qty'],'price' => (int)$priceQtyArr['0']['meli_price'],'seller_custom_field' => $seller_custom_field);
								  }	
							  }
							$descriptionItem = '';
							$descriptionItem = $itemProfileDetailModel->getData('description_header');
							if(trim($ModelItemListing->getData('main_image'))!=''){
								$descriptionItem .= '<p><img src="'.$ModelItemListing->getData('main_image').'" /></p>';
							}
							$descriptionItem .= $itemProfileDetailModel->getData('description_body').$itemProfileDetailModel->getData('description_footer');
							$descriptionItem = str_replace('{{product_id}}',$productId,$descriptionItem);
							if(preg_match('/\<iframe\s+.*src\=\"([^"]*)\".*\<\/iframe\>/i', $descriptionItem, $matches )){
								$descriptionItem .= '<p>'.$this-> getIframHtml($matches['1']).'</p>';
							}
							  $data = array(
										'site_id'=> $ModelItemListing->getData('site_id'),
										'title'=> "Item de testeo – por favor no ofertar - ".$ModelItemListing->getData('title'),
										'description'=> $descriptionItem,
										'category_id'=> $ModelMeliCatArr['0']['meli_category_id'],
										'price'=>  number_format($ModelProduct->getData('price'), 2, '.', ''),
										'currency_id'=>'ARS',
										'buying_mode'=> $ModelTemplate->getData('buying_mode_id'),
										'listing_type_id'=>  $ModelTemplate->getData('listing_type_id'),
										'condition'=> $ModelTemplate->getData('condition_id'),
										'available_quantity'=>'1', //$ModelItemListing->getData('available_quantity')
										'variations'=>$variations,
										'shipping'=>$shipping
									);
							if(count($pictureArr) > 0){
								 $data['pictures'] = $pictureArr;
							 }

							$requestData = '';
							$requestData = json_encode($data);

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
								$requestUrl = $apiUrl.'/items?access_token='.$access_token;
								$response = $commonModel-> meliConnect($requestUrl,'POST',$requestData);
															 
							}
							if(isset($response['statusCode']) && $response['statusCode'] == 404){
								$this->errorMessage = "Error :: Please Check Client Id & Client Secret. Access Token Not Found OR Invalid.";
								$commonModel->saveLogger($this->moduleName, "Exception", $this->fileName, $this->errorMessage);
								Mage::getSingleton('adminhtml/session')->addError(Mage::helper('items')->__($this->errorMessage));
								$this->_redirect('items/adminhtml_itempublishing/');
								return;	
							}
						
							/* Update mercadolibre_item data on Successfull Responce - 201  */
							if(isset($response['statusCode']) && isset($response['json']) && is_array($response['json']) && isset($response['json']['id']) && trim($response['json']['id'])!=''){
								$start_time = Mage::helper('items')->getMLebayDateToDateTime($response['json']['start_time']);
								$stop_time = Mage::helper('items')->getMLebayDateToDateTime($response['json']['stop_time']);
								$end_time = Mage::helper('items')->getMLebayDateToDateTime($response['json']['end_time']);
								$date_created =  Mage::helper('items')->getMLebayDateToDateTime($response['json']['date_created']);
								$last_updated =  Mage::helper('items')->getMLebayDateToDateTime($response['json']['last_updated']);
								
								$ModelItemListing->setMeliItemId($response['json']['id']);
								$ModelItemListing->setStartTime($start_time);
								$ModelItemListing->setStopTime($stop_time);
								$ModelItemListing->setEndTime($end_time);
								$ModelItemListing->setAcceptsMercadopago($response['json']['accepts_mercadopago']);
								$ModelItemListing->setStatus($response['json']['status']);
								$ModelItemListing->setDateCreated($date_created);
								$ModelItemListing->setLastUpdated($last_updated); 
								$ModelItemListing->setSentToPublish('Published'); 
								$ModelItemListing->setPermalink($response['json']['permalink']); 
								$ModelItemListing->setSoldQuantity($response['json']['sold_quantity']);
								$ModelItemListing->setWarranty($response['json']['warranty']);
								//$ModelItemListing->setSubStatus($response['json']['sub_status']);
								$ModelItemListing->setVideoId($response['json']['video_id']); 
								$ModelItemListing->setParentItemId($response['json']['parent_item_id']);
								$ModelItemListing->setMeliCategoryId(trim($response['json']['category_id'])); 
									 
								$ModelItemListing->save();
								$successItemIdsArr[] = $productId;

								
							} else  if(isset($response['json']['message'])){
									$errorItemIdsArr[] = $productId; 
									
									$this->errorMessage .= "Error :: Item Id (". $productId .")". $response['json']['message'] .' '.$response['json']['status'].' ' . $response['json']['error']." <br />";
									$this->errorMessageLog = "Error :: Item Id (". $productId .")". $response['json']['message'] .' '.$response['json']['status'].' ' . $response['json']['error'];
									$commonModel->saveLogger($this->moduleName, "Exception", $this->fileName, $this->errorMessageLog);
								if(isset($response['json']['cause']) && count ($response['json']['cause']) > 0 ){
									$this->errorMessage .= "Error Cause :: ";
									foreach($response['json']['cause'] as $row){
										if(isset($row['message'])){
											$this->errorMessage .= $row['code'].':'.$row['message'];
										}
									}
									
	
								}
							}				  
						}
				}
				if(!$countToPublish){
					 Mage::getSingleton('adminhtml/session')->addError(Mage::helper('items')->__("There is no item selected to publish. Please select item. "));
				}
				//die;
				if(is_array($successItemIdsArr) && count($successItemIdsArr) > 0){
					$successItemIds = implode(' , ',$successItemIdsArr);
					Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('items')->__("Item('s) ($successItemIds ) has been sent for publishing successfully."));
				}
				if(is_array($errorItemIdsArr) && count($errorItemIdsArr) > 0 ){
					$errorItemIds = implode(' , ',$errorItemIdsArr);
					Mage::getSingleton('adminhtml/session')->addError(Mage::helper('items')->__("Following Item('s) ($errorItemIds) could't be sent for publishing.<br />".$this->errorMessage));
				}
			}else{
					Mage::getSingleton('adminhtml/session')->addError(Mage::helper('items')->__("Please select Item('s) to publish."));
			}
			
		}catch(Exception $e){
			$commonModel->saveLogger($this->moduleName, "Exception", $this->fileName, $e->getMessage());	
		}
		//$rootIdArr =  explode('root_id', $_SERVER['HTTP_REFERER']);
		//if(count($rootIdArr)== 2){
				//$this->_redirect('*/*/index/root_id'.$rootIdArr['1']);
				//return;
		//}else {
			$this->_redirect('*/*/index');
			return;
		//}
		
  }
    public function relistAction() {
		try{
			$data = array();
			$commonModel = Mage::getModel('items/common');
			$product_idArr[] = $this->getRequest()->getParam('id');
			$storeId = Mage::helper('items')->_getStore()->getId();
			
			if(count($product_idArr) > 0){
				$countToPublish = 0;
				foreach($product_idArr as $key => $productId){
						$ModelItemListing2  =  Mage::getModel('items/mercadolibreitem')
											-> getCollection()
											-> addFieldToFilter('main_table.store_id',$storeId)
											-> addFieldToFilter('main_table.product_id',$productId);
						$ModelItemListing2 	-> getSelect()
											-> joinleft(array('mmt'=>'mercadolibre_master_templates'), "main_table.master_temp_id = mmt.master_temp_id", array('mmt.master_temp_title','mmt.template_id','mmt.shipping_id','mmt.profile_id', 'mmt.payment_id as payment_method_id'));
						$itemIdArr = $ModelItemListing2->getData();
						$template_id = '';
						$template_id = $itemIdArr['0']['template_id'];
						$itemId		 = $itemIdArr['0']['item_id'];
						
						$ModelItemListing = Mage::getModel('items/mercadolibreitem')->load($itemId);
						$ModelProduct	  = Mage::getModel('catalog/product')->load($ModelItemListing->getData('product_id'));						
						/*----------------------*/
						$_product = Mage::getModel('catalog/product')->load($productId);
						$ModelTemplate	 = Mage::getModel('items/meliproducttemplates')->load($template_id);
						$data = array(
						  			'listing_type_id' =>  $ModelTemplate->getData('listing_type_id'),
									'variations' => $variations
								);
						$requestData = '';
						$requestData = json_encode($data);

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
							$meli_item_id = $ModelItemListing->getData('meli_item_id');
							$requestUrl = $apiUrl.'/items/'.$meli_item_id.'/relist?access_token='.$access_token;
							$response = $commonModel-> meliConnect($requestUrl,'POST',$requestData);
						}
						/* Update mercadolibre_item data on Successfull Responce - 201  */
						if(isset($response['statusCode']) && $response['statusCode'] == 201 && isset($response['json']) && is_array($response['json'])){
							$start_time = Mage::helper('items')->getMLebayDateToDateTime($response['json']['start_time']);
							$stop_time = Mage::helper('items')->getMLebayDateToDateTime($response['json']['stop_time']);
							$end_time = Mage::helper('items')->getMLebayDateToDateTime($response['json']['end_time']);
							$date_created =  Mage::helper('items')->getMLebayDateToDateTime($response['json']['date_created']);
							$last_updated =  Mage::helper('items')->getMLebayDateToDateTime($response['json']['last_updated']);
							
							$ModelItemListing->setMeliItemId($response['json']['id']);
							$ModelItemListing->setStartTime($start_time);
							$ModelItemListing->setStopTime($stop_time);
							$ModelItemListing->setEndTime($end_time);
							$ModelItemListing->setAcceptsMercadopago($response['json']['accepts_mercadopago']);
							$ModelItemListing->setStatus($response['json']['status']);
							$ModelItemListing->setDateCreated($date_created);
							$ModelItemListing->setLastUpdated($last_updated); 
							$ModelItemListing->setSentToPublish('Published');
							$ModelItemListing->setPermalink($response['json']['permalink']); 
							$ModelItemListing->setSoldQuantity($response['json']['sold_quantity']);
							$ModelItemListing->setWarranty($response['json']['warranty']);
							$ModelItemListing->setSubStatus($response['json']['sub_status']);
							$ModelItemListing->setVideoId($response['json']['video_id']); 
							$ModelItemListing->setParentItemId($response['json']['parent_item_id']);  
							$ModelItemListing->save();
							Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('items')->__("Meli Item Id (". $meli_item_id .") has been relist successfully."));
							
						} else  {
							$this->errorMessage .= "Error :: Meli Item Id (". $meli_item_id .")". $response['json']['message'] .' '.$response['json']['status'].' ' . $response['json']['error']." <br />";
							$this->errorMessageLog = "Error :: Meli Item Id (". $meli_item_id .")". $response['json']['message'] .' '.$response['json']['status'].' ' . $response['json']['error'];
							$commonModel->saveLogger($this->moduleName, "Exception", $this->fileName, $this->errorMessageLog);
							if(isset($response['json']['cause']) && count ($response['json']['cause']) > 0 ){
								$this->errorMessage .= "Error Cause :: ";
								foreach($response['json']['cause'] as $row){
									$this->errorMessage .= $row['cause'].':'.$row['message'];
								}
							}
							Mage::getSingleton('adminhtml/session')->addError(Mage::helper('items')->__("Meli Item Id (". $meli_item_id .") could't be sent for relist.<br />".$this->errorMessage));
					}				  
				}
			}
		}catch(Exception $e){
			$commonModel->saveLogger($this->moduleName, "Exception", $this->fileName, $e->getMessage());	
		}
			$this->_redirect('*/*/index');
			return;		
  }
  	public function reviseAction() {
		try{
			$data = array();
			$successItemIdsArr = array();
			$errorItemIdsArr = array();
			$successItemIds = '';
			
			$commonModel = Mage::getModel('items/common');
			$product_idArr = $this->getRequest()->getPost('product_id');
			$storeId = Mage::helper('items')->_getStore()->getId();
			
			if(count($product_idArr) > 0){
				$countToPublish = 0;
				foreach($product_idArr as $key => $productId){
						$checkboxOn = $this->getRequest()->getPost('checkbox_'.$productId);
						if($checkboxOn == 'on'){
							$countToPublish++;
							$ModelItemListing2  =  Mage::getModel('items/mercadolibreitem')
												-> getCollection()
												-> addFieldToFilter('main_table.store_id',$storeId)
												-> addFieldToFilter('main_table.product_id',$productId);
							$ModelItemListing2 	-> getSelect()
											  	-> joinleft(array('mmt'=>'mercadolibre_master_templates'), "main_table.master_temp_id = mmt.master_temp_id", array('mmt.master_temp_title','mmt.template_id','mmt.shipping_id','mmt.profile_id', 'mmt.payment_id as payment_method_id'));
							$itemIdArr = $ModelItemListing2->getData();
							$payment_method_id = '';							
							$itemId =  $itemIdArr['0']['item_id'];
							
							$ModelItemListing =  Mage::getModel('items/mercadolibreitem')->load($itemId);
							$ModelProduct	 = Mage::getModel('catalog/product')->load($ModelItemListing->getData('product_id'));

							/*----------------------*/
							$_product = Mage::getModel('catalog/product')->load($productId);
							if($_product->getData('type_id') == 'configurable'){
								/* Save data if revise listing */
								if($this->getRequest()->getPost('revise')){
									if(is_array($this->getRequest()->getPost('item_id')) && count($this->getRequest()->getPost('item_id')) > 0){
										foreach($this->getRequest()->getPost('item_id') as $r => $itemIdAsso){
											 $priceArr = array();
											 $priceArr = $this->getRequest()->getPost('configurable_attribute_price_'.$itemIdAsso);
											 $qtyArr = array();
											 $qtyArr = $this->getRequest()->getPost('configurable_attribute_qty_'.$itemIdAsso);
											  // Saving price / Qty 
											 $write = Mage::getSingleton('core/resource')->getConnection('core_write');
											 $write->query("DELETE FROM mercadolibre_item_price_qty WHERE item_id = '".$itemIdAsso."' AND store_id = '".$storeId."'");
											 $sql_insert_priceQty =" insert into mercadolibre_item_price_qty (item_id, meli_price, meli_qty, store_id) values (".$itemIdAsso.", '".$priceArr['0']."', '".$qtyArr['0']."', '".$storeId."')";
											 $write->query($sql_insert_priceQty);
										}	
									}
								}
								$associated_prods = $_product->getTypeInstance()->getUsedProducts();
								$html = '';
								$variations = array();
								foreach ($associated_prods as $assoc_product) {
									$productAssoId          = '';		
									$productAssoId          = $assoc_product->getId(); 
									$ModelProductAsso	    = Mage::getModel('catalog/product')->load($productAssoId);
									$ModelItemAttributeCon  = Mage::getModel('items/meliitemattributes')
														   -> getCollection()
														   -> addFieldToFilter('main_table.store_id',$storeId)
														   -> addFieldToFilter('main_table.item_id',$productAssoId); 
									$ModelItemAttributeCon -> getSelect()
														   -> joinleft(array('melicatatt'=>'mercadolibre_category_attributes'), " main_table.meli_attribute_id = melicatatt.meli_attribute_id and melicatatt.category_id = '".$ModelMeliCatArr['0']['meli_category_id']."'", array('melicatatt.meli_attribute_id','melicatatt.meli_attribute_name'))
														   -> joinleft(array('melicatattval'=>'mercadolibre_category_attribute_values'), "main_table.meli_value_id = melicatattval.meli_value_id and melicatattval.meli_category_id = '".$ModelMeliCatArr['0']['meli_category_id']."' ", array('melicatattval.meli_value_id','melicatattval.meli_value_name'));
									$attribute_combinations = array();
									if(count($ModelItemAttributeCon->getData()) > 0){
										foreach($ModelItemAttributeCon->getData() as $row){
											$attribute_combinations[] = (object) array('id'=>$row['meli_attribute_id'],'value_id' => $row['meli_value_id']);
										}
									}
									$PriceQtyAttributeCon  =  Mage::getModel('items/meliitempriceqty')
														   -> getCollection()
														   -> addFieldToFilter('main_table.store_id',$storeId) 
														   -> addFieldToFilter('main_table.item_id',$productAssoId) 
														   -> addFieldToFilter('main_table.meli_price',array('neq'=>'0.00'))
														   -> addFieldToFilter('main_table.meli_qty',array('neq'=>0)); 
									$priceQtyArr = $PriceQtyAttributeCon->getData();
									if(count($attribute_combinations) > 0){
										   $variations[] = (object) array('attribute_combinations'=>$attribute_combinations,'available_quantity' => (int)$priceQtyArr['0']['meli_qty'],'price' => (int)$priceQtyArr['0']['meli_price']);
									  }	
								}
							} else {	
							/*----------Simple Product----------------*/	
							/* Save data if revise listing */
							if($this->getRequest()->getPost('revise')){
								if(is_array($this->getRequest()->getPost('item_id')) && count($this->getRequest()->getPost('item_id')) > 0){
									foreach($this->getRequest()->getPost('item_id') as $r => $itemIdAsso){
										 $priceArr = array();
										 $priceArr = $this->getRequest()->getPost('attribute_price_'.$itemIdAsso);
										 $qtyArr   = array();
										 $qtyArr   = $this->getRequest()->getPost('attribute_qty_'.$itemIdAsso);
										  // Saving price / Qty 
										 $write = Mage::getSingleton('core/resource')->getConnection('core_write');
										 $write->query("DELETE FROM mercadolibre_item_price_qty WHERE item_id = '".$itemIdAsso."' AND store_id = '".$storeId."' ");
										 $sql_insert_priceQty =" insert into mercadolibre_item_price_qty (item_id, meli_price, meli_qty, store_id) values (".$itemIdAsso.", '".$priceArr['0']."', '".$qtyArr['0']."','".$storeId."')";
										 $write->query($sql_insert_priceQty);
									}	
								}
							}
							$ModelItemAttribute  = Mage::getModel('items/meliitemattributes')
												-> getCollection()
												-> addFieldToFilter('main_table.store_id',$storeId)
												-> addFieldToFilter('main_table.item_id',$productId);
							$ModelItemAttribute -> getSelect()
												-> joinleft(array('melicatatt'=>'mercadolibre_category_attributes'), " main_table.meli_attribute_id = melicatatt.meli_attribute_id and melicatatt.category_id = '".$ModelMeliCatArr['0']['meli_category_id']."' ", array('melicatatt.meli_attribute_id','melicatatt.meli_attribute_name'))
											    -> joinleft(array('melicatattval'=>'mercadolibre_category_attribute_values'), "main_table.meli_value_id = melicatattval.meli_value_id  and melicatattval.meli_category_id = '".$ModelMeliCatArr['0']['meli_category_id']."' ", array('melicatattval.meli_value_id','melicatattval.meli_value_name'));
													
								$variations = array();
								if(count($ModelItemAttribute->getData()) > 0){
									foreach($ModelItemAttribute->getData() as $row){
										$attribute_combinations[] = (object) array('id'=>$row['meli_attribute_id'],'value_id' => $row['meli_value_id']);
									}
								}
								$PriceQtyAttributeCon  = Mage::getModel('items/meliitempriceqty')
													  -> getCollection()
													  -> addFieldToFilter('main_table.store_id',$storeId)
													  -> addFieldToFilter('main_table.item_id',$productId); 
													   
								$priceQtyArr = $PriceQtyAttributeCon->getData();
								if(count($attribute_combinations) > 0){
									   $variations[] = (object) array('attribute_combinations'=>$attribute_combinations,'available_quantity' => (int)$priceQtyArr['0']['meli_qty'],'price' => (int)$priceQtyArr['0']['meli_price']);
								  }			
							  }

							$data = array( 'variations'=>$variations);
							$requestData = '';
							$requestData = json_encode($data);

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
							if($this->getRequest()->getPost('revise') && trim($access_token)!='' & trim($access_token)!=''){
								$meli_item_id = $ModelItemListing->getData('meli_item_id');
								$requestUrl = $apiUrl.'/items/'.$meli_item_id.'?access_token='.$access_token;
								$response = $commonModel-> meliConnect($requestUrl,'PUT',$requestData);
							}
						
							/* Update mercadolibre_item data on Successfull Responce - 201  */
							if(isset($response['statusCode']) && $response['statusCode'] == 200 && isset($response['json']) && is_array($response['json'])){
								$start_time = Mage::helper('items')->getMLebayDateToDateTime($response['json']['start_time']);
								$stop_time = Mage::helper('items')->getMLebayDateToDateTime($response['json']['stop_time']);
								$end_time = Mage::helper('items')->getMLebayDateToDateTime($response['json']['end_time']);
								$date_created =  Mage::helper('items')->getMLebayDateToDateTime($response['json']['date_created']);
								$last_updated =  Mage::helper('items')->getMLebayDateToDateTime($response['json']['last_updated']);
								
								$ModelItemListing->setMeliItemId($response['json']['id']);
								$ModelItemListing->setStartTime($start_time);
								$ModelItemListing->setStopTime($stop_time);
								$ModelItemListing->setEndTime($end_time);
								$ModelItemListing->setAcceptsMercadopago($response['json']['accepts_mercadopago']);
								$ModelItemListing->setStatus($response['json']['status']);
								$ModelItemListing->setDateCreated($date_created);
								$ModelItemListing->setLastUpdated($last_updated); 
								$ModelItemListing->setSentToPublish('Published'); 
								$ModelItemListing->setPermalink($response['json']['permalink']); 
								$ModelItemListing->setSoldQuantity($response['json']['sold_quantity']);
								$ModelItemListing->setWarranty($response['json']['warranty']);
								$ModelItemListing->setSubStatus($response['json']['sub_status']);
								$ModelItemListing->setVideoId($response['json']['video_id']); 
								$ModelItemListing->setParentItemId($response['json']['parent_item_id']); 
								 
								$ModelItemListing->save();
								$successItemIdsArr[] = $productId;
								
							} else if(isset($response['json']['message']))  {
									$errorItemIdsArr[] = $productId; 
									$this->errorMessage .= "Error :: Item Id (". $productId .")". $response['json']['message'] .' '.$response['json']['status'].' ' . $response['json']['error']." <br />";
									$this->errorMessageLog = "Error :: Item Id (". $productId .")". $response['json']['message'] .' '.$response['json']['status'].' ' . $response['json']['error'];
									$commonModel->saveLogger($this->moduleName, "Exception", $this->fileName, $this->errorMessageLog);
								if(isset($response['json']['cause']) && count ($response['json']['cause']) > 0 ){
									$this->errorMessage .= "Error Cause :: ";
									foreach($response['json']['cause'] as $row){
										$this->errorMessage .= $row['cause'].':'.$row['message'];
									}
								}
							}				  
						}
				}
				if(!$countToPublish){
					 Mage::getSingleton('adminhtml/session')->addError(Mage::helper('items')->__("There is no item selected to publish. Please select item. "));
				}
				if(is_array($successItemIdsArr) && count($successItemIdsArr) > 0){
					$successItemIds = implode(' , ',$successItemIdsArr);
					Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('items')->__("Item('s) ($successItemIds ) has been revised successfully."));
				}
				if(is_array($errorItemIdsArr) && count($errorItemIdsArr) > 0 ){
					$errorItemIds = implode(' , ',$errorItemIdsArr);
					Mage::getSingleton('adminhtml/session')->addError(Mage::helper('items')->__("Following Item('s) ($errorItemIds) could't be sent for publishing.<br />".$this->errorMessage));
				}
			}else{
					Mage::getSingleton('adminhtml/session')->addError(Mage::helper('items')->__("Please select Item('s) to publish."));
			}
			
		}catch(Exception $e){
			$commonModel->saveLogger($this->moduleName, "Exception", $this->fileName, $e->getMessage());	
		}
		//$rootIdArr =  explode('root_id', $_SERVER['HTTP_REFERER']);
		//if(count($rootIdArr)== 2){
				//$this->_redirect('*/*/index/root_id'.$rootIdArr['1']);
				//return;
		//}else {
			$this->_redirect('*/*/index');
			return;
		//}
		
  }
  	public function putStatusAction(){
	
				$commonModel = Mage::getModel('items/common');
				$productId = $this->getRequest()->getParam('id');
				$action = $this->getRequest()->getParam('action');
				$storeId = Mage::helper('items')->_getStore()->getId();
				
				$ModelItemListing2  =  Mage::getModel('items/mercadolibreitem')
									-> getCollection()
									-> addFieldToFilter('main_table.store_id',$storeId)
									-> addFieldToFilter('main_table.product_id',$productId);
				$ModelItemListing2 	-> getSelect()
									-> joinleft(array('mmt'=>'mercadolibre_master_templates'), "main_table.master_temp_id = mmt.master_temp_id", array('mmt.master_temp_title','mmt.template_id','mmt.shipping_id','mmt.profile_id', 'mmt.payment_id as payment_method_id'));
									
				$itemIdArr = $ModelItemListing2->getData();
				$itemId =  $itemIdArr['0']['item_id'];
				
				$ModelItemListing  = Mage::getModel('items/mercadolibreitem')->load($itemId);

				$data = array('status'=>$action);
				$requestData = '';
				$requestData = json_encode($data);
	
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
				if($this->getRequest()->getParam('action') && trim($access_token)!='' && trim($apiUrl)!=''){
					$meli_item_id = $ModelItemListing->getData('meli_item_id');
					$requestUrl = $apiUrl.'/items/'.$meli_item_id.'?access_token='.$access_token;
					$response = $commonModel-> meliConnect($requestUrl,'PUT',$requestData);
				}
				
				/* Update mercadolibre_item data on Successfull Responce - 201  */
				if(isset($response['statusCode']) && $response['statusCode'] == 200 && isset($response['json']) && is_array($response['json'])){
					$start_time = Mage::helper('items')->getMLebayDateToDateTime($response['json']['start_time']);
					$stop_time = Mage::helper('items')->getMLebayDateToDateTime($response['json']['stop_time']);
					$end_time = Mage::helper('items')->getMLebayDateToDateTime($response['json']['end_time']);
					$date_created =  Mage::helper('items')->getMLebayDateToDateTime($response['json']['date_created']);
					$last_updated =  Mage::helper('items')->getMLebayDateToDateTime($response['json']['last_updated']);
					
					$ModelItemListing->setMeliItemId($response['json']['id']);
					$ModelItemListing->setStartTime($start_time);
					$ModelItemListing->setStopTime($stop_time);
					$ModelItemListing->setEndTime($end_time);
					$ModelItemListing->setAcceptsMercadopago($response['json']['accepts_mercadopago']);
					$ModelItemListing->setStatus($response['json']['status']);
					$ModelItemListing->setDateCreated($date_created);
					$ModelItemListing->setLastUpdated($last_updated); 
					$ModelItemListing->setSentToPublish('Published'); 
					$ModelItemListing->setPermalink($response['json']['permalink']); 
					$ModelItemListing->setSoldQuantity($response['json']['sold_quantity']);
					$ModelItemListing->setWarranty($response['json']['warranty']);
					$ModelItemListing->setSubStatus($response['json']['sub_status']);
					$ModelItemListing->setVideoId($response['json']['video_id']); 
					$ModelItemListing->setParentItemId($response['json']['parent_item_id']); 
					$ModelItemListing->save();
					
					Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('items')->__("Meli Item Id ($meli_item_id) has been $action successfully."));
					
				} else if(isset($response['json']['message']))  {
						$this->errorMessage .= "Error :: Meli Item Id (". $meli_item_id .")". $response['json']['message'] .' '.$response['json']['status'].' ' . $response['json']['error']." <br />";
						$this->errorMessageLog = "Error :: Meli Item Id (". $meli_item_id .")". $response['json']['message'] .' '.$response['json']['status'].' ' . $response['json']['error'];
						$commonModel->saveLogger($this->moduleName, "Exception", $this->fileName, $this->errorMessageLog);
					if(isset($response['json']['cause']) && count ($response['json']['cause']) > 0 ){
						$this->errorMessage .= "Error Cause :: ";
						foreach($response['json']['cause'] as $row){
							$this->errorMessage .= $row['cause'].' : '.$row['message'];
						}
						

					}
				}
			if($this->errorMessage){
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('items')->__($this->errorMessage));
			}	
			$this->_redirect('*/*/index');
			return;
	}
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('items/itemlisting');
				 
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

    public function massDeleteAction() {
        $itemlistingIds = $this->getRequest()->getParam('itemlisting');
        if(!is_array($itemlistingIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($itemlistingIds as $itemlistingId) {
                    $itemlisting = Mage::getModel('items/itemlisting')->load($itemlistingId);
                    $itemlisting->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($itemlistingIds)
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
        $itemlistingIds = $this->getRequest()->getParam('itemlisting');
        if(!is_array($itemlistingIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($itemlistingIds as $itemlistingId) {
                    $itemlisting = Mage::getSingleton('items/itemlisting')
                        ->load($itemlistingId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($itemlistingIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'itemlisting.csv';
        $content    = $this->getLayout()->createBlock('items/adminhtml_itemlisting_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'itemlisting.xml';
        $content    = $this->getLayout()->createBlock('items/adminhtml_itemlisting_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }
	
	 public function importCsvAction(){	
		try{
			$commonModel = Mage::getModel('items/common');
			$id = $this->getRequest()->getParam('id');
			$model  = Mage::getModel('items/melicategories')->load($id);
			if ($model->getId() || $id == 0) {
				$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
				if (!empty($data)) {
					$model->setData($data);
				}
				Mage::register('items_data', $model);
				$this->loadLayout();
				$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
				$this->_addContent($this->getLayout()->createBlock('items/adminhtml_itempublishing_edit'))
					->_addLeft($this->getLayout()->createBlock('items/adminhtml_itempublishing_edit_tabs'));
				$this->renderLayout();
			} else {
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('items')->__('Item does not exist'));
				$this->_redirect('*/*/');
			}
		}catch(Exception $e){
				$commonModel->saveLogger($this->moduleName, "Exception", $this->fileName, $e->getMessage());	
		}
	}
	
	public function saveAction() {
			try{
				$commonModel = Mage::getModel('items/common');
				if ($data = $this->getRequest()->getPost() && isset($_FILES['filename']['name']) && $_FILES['filename']['error'] == 0) {
				$ext = pathinfo($_FILES['filename']['name'],PATHINFO_EXTENSION);
				if($ext == 'csv'){
					$homepage = file_get_contents($_FILES['filename']['tmp_name']);
					$i = 0;
					if (($handle = fopen($_FILES['filename']['tmp_name'], "r")) !== FALSE) {
						$arraySuccess = array();
						$arrayError = array();
						while (($data = fgetcsv($handle)) !== FALSE) {
							if($i!=0){
								$MlItemsFilter =  Mage::getModel('items/mercadolibreitem')
											  -> getCollection()
											  -> addFieldToFilter('meli_item_id',mysql_escape_string($data['1']))
											  -> getColumnValues('item_id');
							    $item_id = '';
								if(count($MlItemsFilter)){		
									$item_id = ($MlItemsFilter['0'])?$MlItemsFilter['0']:'';
								}
								if(trim($data['0'])!=''){
									$site_id = 'NULL';
									$site_id = substr($data['3'],0,3);
									$store_id = 0;
									$store_id = Mage::helper('items')->_getStore()->getId();
									$dataToSave = array(
														'title' => mysql_escape_string($data['0']),
														'meli_item_id' => mysql_escape_string($data['1']),
														'product_id' => mysql_escape_string($data['5']),
														'meli_category_id' => mysql_escape_string($data['3']),
														'site_id' =>  mysql_escape_string($site_id),
														'sent_to_publish' => 'Published',
														'store_id' => $store_id,
														'sub_status' => 'imported',
														'status' => 'active'
														);
									if($item_id){
										$dataToSave['item_id'] = $item_id;
									}
									
									$MlItemsFilterModel =  Mage::getModel('items/mercadolibreitem');
									$MlItemsFilterModel ->setData($dataToSave);
									$MlItemsFilterModel -> save();
									$arraySuccess[] = $data['1'];
								}else{
									$arrayError[] = $data['1'];
								}
							}
							$i++;
						}
							fclose($handle);
							
							if(count($arraySuccess) > 0){
								$successMagePublishedItems = implode(',',$arraySuccess);
								 Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('items')->__("Meli Item Id(s)(".$successMagePublishedItems.") has been saved successfully."));
							}
							if(count($arrayError) > 0){
								$errorMagePublishedItems = implode(',',$arrayError);
								/* Error Message */
								Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('items')->__("Error :: Meli Item Id(s) (".$errorMagePublishedItems.") unable save"));
							}
					}
				}	else {
					$this->errorMessage = 'Error :: Invalid File Upload';
					$commonModel->saveLogger($this->moduleName, "Error", $this->fileName, $this->errorMessage);
					Mage::getSingleton('adminhtml/session')->addError(Mage::helper('items')->__('Invalid file upload. Please upload only csv file.'));
       				$this->_redirect('*/*/importCsv');
					return;
					
				}
				} else {
					 Mage::getSingleton('adminhtml/session')->addError(Mage::helper('items')->__('Please upload csv file.'));
       				 $this->_redirect('*/*/importCsv');
					 return;
				}
			} catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/importCsv');
                return;
            }
			 Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('items')->__('Publised Item(s) has been saved successfully.'));
       		 $this->_redirect('*/*/');
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
	
	public function getIframHtml($Uri){
			$html = '';
			if($Uri){
				$curl = curl_init($Uri);
				curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:text/xml'));
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
				curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
				curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
				curl_setopt($curl, CURLOPT_ENCODING, 1);
				curl_setopt($curl, CURLOPT_ENCODING, 1);
				$data = curl_exec($curl);
				return $data;
				curl_close($curl);
			}
			
			
	}
}
