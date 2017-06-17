<?php
ob_start("ob_gzhandler");
ini_set('max_execution_time',0);
ini_set('memory_limit','1024M');
ini_set('max_input_time',0);
class Mercadolibre_Items_Adminhtml_ItemlistingController extends Mage_Adminhtml_Controller_action
{

	private $moduleName = "Items";
	private $fileName = "ItemListingController.php";
	
	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('items/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
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
			$ItemImage =  $this->getRequest()->getPost('item_image');
			$EntityId = $this->getRequest()->getPost('entity_id');
			$MageProductName = $this->getRequest()->getPost('title');
			$Price = $this->getRequest()->getPost('price');  // Base Price 
			$AvailableQuantity = $this->getRequest()->getPost('available_quantity');
			$MeliCategoryName = $this->getRequest()->getPost('meli_category_name');
			$MeliPrice = $this->getRequest()->getPost('meli_price'); // Price
			$MeliQuantity = $this->getRequest()->getPost('meli_quantity');
			$MageCategoryIds = $this->getRequest()->getPost('mage_category_id');
			$master_temp_id  = $this->getRequest()->getPost('master_temp_id');
			$storeId = Mage::helper('items')->_getStore()->getId();


			$data = array();
			if(count($master_temp_id) > 0){
							$flag  = 0;
							$itemDetails =0;
							$countToSaveToPublish = 0;
                            foreach($master_temp_id as $key => $masterTempId){
									$itemDetails++;
									$title = $MageProductName[$key];
									if(trim($master_temp_id[$key])!=''){
										$flag++;
										if($MeliCategoryName[$key] !=''){ 
												$successItem[] = $EntityId[$key]; 
												$mage_type_id ='';
												$mage_type_id = $this->getRequest()->getPost('mage_type_id_'.$EntityId[$key]);
												$checkboxOn = $this->getRequest()->getPost('checkbox_'.$EntityId[$key]);
												if($checkboxOn == 'on'){
													$countToSaveToPublish++;
													$main_image = '';
													$main_image_required = 0;
													$ModelProduct = Mage::getModel('catalog/product')->load($EntityId[$key]);
													
													if(Mage::getStoreConfig("mlitems/meliinventorysetting/mainimageintemplatebody",Mage::app()->getStore())){
														$main_image_required = Mage::getStoreConfig("mlitems/meliinventorysetting/mainimageintemplatebody",Mage::app()->getStore());
														$main_image = $ModelProduct->getData('image');
													}
													//$ProductData = $ModelProduct->getData();
													$categoryIdArr = $ModelProduct->getCategoryIds($EntityId[$key]);
													$product_id = $EntityId[$key];
													$site_id  = '';
													if($site_id = Mage::helper('items')->getMlSiteId()){
														$site_id = $site_id;
													} else {
														$this->errorMessage = "Error :: Site Id Not Found.".
														$commonModel->saveLogger($this->moduleName, "Exception", $this->fileName, $this->errorMessage);	
														Mage::getSingleton('adminhtml/session')->addError(Mage::helper('items')->__("Error :: Site Id Not Found."));
														$this->_redirect('*/*/index');
													}
		
													/* Product Images */
													$ItemImagesSerialize = '';
													if(isset($ItemImage[$product_id]) && is_array($ItemImage[$product_id]) && count($ItemImage[$product_id]) > 0){
														$ItemImagesSerialize = serialize($ItemImage[$product_id]);
													}
												   $data = array('product_id'=>$commonModel->inputData($EntityId[$key]),
																 'pictures' => $commonModel->inputData($ItemImagesSerialize),
																 'site_id'=>$site_id,
																 'title'=>$commonModel->inputData($title),
																 'category_id'=>$MageCategoryIds[$key],
																 'price'=> $commonModel->inputData($MeliPrice[$key]),
																 'base_price'=>$commonModel->inputData($Price[$key]),
																 'initial_quantity'=>$commonModel->inputData(ceil($MeliQuantity[$key])),
																 'available_quantity'=>$commonModel->inputData(ceil($MeliQuantity[$key])),
																 'created_datetime'=>now(), 
																 'master_temp_id' => $master_temp_id[$key],
																 'main_image_required' => $main_image_required,
																 'main_image' => Mage::getBaseUrl('media').'catalog/product'.$main_image,
																 'mage_type_id' => $mage_type_id,
																 'store_id' => $storeId
																);

													$itemsCollection =  Mage::getModel('items/mercadolibreitem')
																	 -> getCollection()
																	 -> addFieldToFilter('product_id',$EntityId[$key])
																	 -> addFieldToFilter('store_id',$storeId);
													$itemsCollection -> addFieldToSelect('item_id');
													if(count($itemsCollection->getData()) > 0){
														$itemIdArr = $itemsCollection->getData();
														$data['item_id'] = $itemIdArr['0']['item_id']; 
													}
													if (!empty($data)) {
														$ModelItemListing  = Mage::getModel('items/mercadolibreitem');
														$ModelItemListing->setData($data);
														$ModelItemListing->save();
														$successItemIdsArr[] =  $EntityId[$key];
														$last_inserted_id = $EntityId[$key];
														/* Save Attribute, Price, Quantity */
														$attributeidArr = $this->getRequest()->getPost('attribute_'.$EntityId[$key]);
														$attributeidValue2Arr = $this->getRequest()->getPost('attribute_value_'.$EntityId[$key]);
														$attributeidValueArr = array();
														$attributeValueOptionIdArr = array();
														if(count($attributeidValue2Arr) > 0){
															foreach($attributeidValue2Arr as $atrVal){
																$explodeAtrVal = explode('#Option_Id#',$atrVal);
																$attributeidValueArr[] = $explodeAtrVal['1'];
																$attributeValueOptionIdArr[] = $explodeAtrVal['0'];
															}
														}
														$attribute_priceArr = $this->getRequest()->getPost('attribute_price_'.$EntityId[$key]);
														$attribute_qtyArr = $this->getRequest()->getPost('attribute_qty_'.$EntityId[$key]);
														
														
														if(count($attributeidArr) > 0){
															/* Delete item attribute before add new one */
															$write = Mage::getSingleton('core/resource')->getConnection('core_write');
															$write->query("DELETE FROM `mercadolibre_item_attributes` WHERE item_id = '".$last_inserted_id."' and store_id = '".$storeId."'");
															
															$attributeidName ='';
															foreach($attributeidArr as $k => $val){
															
																$meli_value_idArr = $this->getRequest()->getPost('meli_value_id_'.$EntityId[$key].'_'.$val.'_'.str_replace(' ','_',$attributeValueOptionIdArr[$k]));
																$meli_attribute_idArr = $this->getRequest()->getPost('meli_attribute_id_'.$EntityId[$key].'_'.$val.'_'.str_replace(' ','_',$attributeValueOptionIdArr[$k]));
																$attributeidName = $attributeidArr[$k];
	
																for($V=0; $V<count($meli_value_idArr); $V++){
																	$attribute_type = '';
																	$attribute_type = ($val)?$val:'';
																	$attributeData = array();
																	$attributeData['attribute_id'] = $attributeidName;
																	$attributeData['value_id'] = $attributeidValueArr[$k];
																	$attributeData['meli_attribute_id'] = $meli_attribute_idArr[$V];
																	$attributeData['meli_value_id'] = $meli_value_idArr[$V];
																	$attributeData['item_id'] = $last_inserted_id;
																	$attributeData['store_id'] = $storeId;
																	
																	//$attributeData['attribute_price'] = $attribute_priceArr[$k];
																	//$attributeData['quantity'] = $attribute_qtyArr[$k];
																	$itemAttributesModel  = Mage::getModel('items/meliitemattributes');
																	$itemAttributesModel->setData($attributeData);
																	$itemAttributesModel->save();
	
																}
																
																
															}
															// Saving price / Qty 
															 $write->query("DELETE FROM mercadolibre_item_price_qty WHERE item_id = '".$last_inserted_id."' and store_id = '".$storeId."'");
															 $sql_insert_priceQty =" insert into mercadolibre_item_price_qty (item_id, meli_price, meli_qty,store_id) values (".$last_inserted_id.", '".$attribute_priceArr['0']."', '".$attribute_qtyArr['0']."' , '".$storeId."')";
															 $write->query($sql_insert_priceQty);
														
														}
													}
													
														/* add simple product used in configurable */
														if(count($this->getRequest()->getPost('configurable_id_'.$EntityId[$key])) > 0){
															$configurable_idArr = $this->getRequest()->getPost('configurable_id_'.$EntityId[$key]);
															foreach($configurable_idArr as $m => $cpIds){
																
																$attributeidArr = $this->getRequest()->getPost('configurable_attribute_'.$cpIds);
																$attributeidValue2Arr = $this->getRequest()->getPost('configurable_attribute_value_'.$cpIds);
																$attributeidValueArr = array();
																$attributeValueOptionIdArr = array();
																if(count($attributeidValue2Arr) > 0){
																	foreach($attributeidValue2Arr as $atrVal){
																		$explodeAtrVal = explode('#Option_Id#',$atrVal);
																		$attributeidValueArr[] = $explodeAtrVal['1'];
																		$attributeValueOptionIdArr[] = $explodeAtrVal['0'];
																	}
																}
																$attribute_priceArr = $this->getRequest()->getPost('configurable_attribute_price_'.$cpIds);
																$attribute_qtyArr = $this->getRequest()->getPost('configurable_attribute_qty_'.$cpIds);

																if(count($attributeidArr) > 0){
																	/* Delete item attribute before add new one */
																	$write = Mage::getSingleton('core/resource')->getConnection('core_write');
																	$write->query("DELETE FROM `mercadolibre_item_attributes` WHERE item_id = '".$cpIds."' and store_id = '".$storeId."'");
				
																	foreach($attributeidArr as $k => $val){
																		$meli_value_idArr = $this->getRequest()->getPost('configurable_meli_value_id_'.$cpIds.'_'.$val.'_'.str_replace(' ','_',$attributeValueOptionIdArr[$k]));
																		$meli_attribute_idArr = $this->getRequest()->getPost('configurable_meli_attribute_id_'.$cpIds.'_'.$val.'_'.str_replace(' ','_',$attributeValueOptionIdArr[$k]));

																		$attributeidName = $attributeidArr[$k];
																		for($V=0; $V<count($meli_value_idArr); $V++){
																			$attribute_type = '';
																			$attribute_type = ($val)?$val:'';
																			$attributeData = array();
																			$attributeData['attribute_id'] = $attributeidName;
																			$attributeData['value_id'] = $attributeidValueArr[$k]; 
																			$attributeData['meli_attribute_id'] = $meli_attribute_idArr[$V];
																			$attributeData['meli_value_id'] = $meli_value_idArr[$V];
																			$attributeData['item_id'] = $cpIds;
																			$attributeData['store_id'] = $storeId;
																			//$attributeData['attribute_price'] = $attribute_priceArr[$k];
																			//$attributeData['quantity'] = $attribute_qtyArr[$k];					
																			$itemAttributesModel  = Mage::getModel('items/meliitemattributes');
																			$itemAttributesModel->setData($attributeData);
																			$itemAttributesModel->save();
	
																		}
																	}
																		// Saving price / Qty 
																		 $write->query("DELETE FROM mercadolibre_item_price_qty WHERE item_id = '".$cpIds."' and store_id = '".$storeId."'");
																		 $sql_insert_priceQty =" insert into mercadolibre_item_price_qty (item_id, meli_price, meli_qty, store_id) values (".$cpIds.", '".$attribute_priceArr['0']."', '".$attribute_qtyArr['0']."', '".$storeId."')";
																		 $write->query($sql_insert_priceQty);
																	}
															}
														}
												}
										} else {
												$errorItemIdsArr[] = $EntityId[$key];
										}
									} 
								
                            }
							if(!$countToSaveToPublish){
								Mage::getSingleton('adminhtml/session')->addError(Mage::helper('items')->__("There is no item selected to save. Please select item. "));
							}
							if(!$flag){
								Mage::getSingleton('adminhtml/session')->addError(Mage::helper('items')->__("No Data Saved. Template is required. "));
							}

			} else {
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('items')->__("Item(s) Profiles required."));
			}
			if(is_array($successItemIdsArr) && count($successItemIdsArr) > 0){
				$successItemIds = implode(' , ',$successItemIdsArr);
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('items')->__("Item('s) ($successItemIds ) has been  mapped successfully."));
			}
			if(is_array($errorItemIdsArr) && count($errorItemIdsArr) > 0 ){
					$errorItemIds = implode(' , ',$errorItemIdsArr);
					Mage::getSingleton('adminhtml/session')->addError(Mage::helper('items')->__("Following Item('s) ($errorItemIds) could't be mapped.<br />".$this->errorMessage));
				}
			$rootIdArr =  explode('root_id', $_SERVER['HTTP_REFERER']);
			if(count($rootIdArr)== 2){
					$this->_redirect('*/*/index/root_id'.$rootIdArr['1']);
					return;
			}else {
				$this->_redirect('*/*/index');
				return;
			}
		}catch(Exception $e){
			$commonModel->saveLogger($this->moduleName, "Exception", $this->fileName, $e->getMessage());	
		}
  }

	public function editAction() {
		
		$id     = $this->getRequest()->getParam('id');		
		$model  = Mage::getModel('items/mercadolibreitem')->load($id);

		if ($model->getItemId() || $id == 0) {

			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('itemlisting', $model);

			$this->loadLayout();
			$this->_setActiveMenu('items/items');
			
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('items/adminhtml_itemlisting_edit'))
				->_addLeft($this->getLayout()->createBlock('items/adminhtml_itemlisting_edit_tabs'));
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
			
			$model = Mage::getModel('items/mercadolibreitem')->load($this->getRequest()->getParam('id'));		
			//$model->setData($data)->setId($this->getRequest()->getParam('id'));
			
			$model->setDescriptions($this->getRequest()->getParam('descriptions'));			
			$model->save();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('items')->__('Item was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
          
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('items')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
	
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