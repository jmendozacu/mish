<?php
ob_start("ob_gzhandler");
class Mercadolibre_Items_Adminhtml_AttributemappingController extends Mage_Adminhtml_Controller_action
{
	private $moduleName = "Items";
	private $fileName = "AttributemappingController.php";
	
		//message variable
	private $infoMessage = "";
	private $errorMessage = "";
	private $successMessage = "";
	private $errorMessageLog = "";
	
	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('items/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Attribute Mapping'), Mage::helper('adminhtml')->__('Attribute Mapping'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}
	
	public function postAction() {
		try{
		//echo '<pre>';
		//print_r($_POST); die;
			$commonModel = Mage::getModel('items/common');
			$mage_attribute_valuesArr = $this->getRequest()->getPost('mage_attribute_value');
			$mage_attributesArr = $this->getRequest()->getPost('mage_attribute');
			$category_id = $this->getRequest()->getPost('category_id');
			$mage_attr_id = $this->getRequest()->getPost('attribute_id_hidden');
			$mageAttributeOptionIds = $this->getRequest()->getPost('mage_attribute_option_id');
			
			//get Store ID
			$storeID = $this->getRequest()->getPost('store_id');
			
			if(Mage::getStoreConfig("mlitems/globalattributesmapping/magesizeattr",Mage::app()->getStore($storeID))){
				$mageSizeAttrIds = Mage::getStoreConfig("mlitems/globalattributesmapping/magesizeattr",Mage::app()->getStore($storeID));
				$mageSizeAttrIds = explode(",", $mageSizeAttrIds);
			}
			if(Mage::getStoreConfig("mlitems/globalattributesmapping/magecolorattr",Mage::app()->getStore($storeID))){
				$mageColorAttrIds = Mage::getStoreConfig("mlitems/globalattributesmapping/magecolorattr",Mage::app()->getStore($storeID));
				$mageColorAttrIds = explode(",", $mageColorAttrIds);
			}

			$meliAtrCount = 0;
			if(count($mage_attribute_valuesArr) > 0){
				foreach($mage_attribute_valuesArr as $key => $mage_attribute_value){
					
					$mage_attribute_option_id = $mageAttributeOptionIds[$key];	
					$meli_attribute_idArr = $this->getRequest()->getPost('meli_attribute_id_'.$mage_attribute_option_id);
					$mage_attribute = $mage_attributesArr[$key];
					if(count($meli_attribute_idArr) > 0){
						foreach($meli_attribute_idArr as $k => $meli_attribute_id){
								$meli_value_idArr = $this->getRequest()->getPost('meli_value_id_'.$meli_attribute_id.'_'.$mage_attribute_option_id);
								$attribute_mapping_idArr = $this->getRequest()->getPost('attribute_mapping_id_'.$meli_attribute_id.'_'.$mage_attribute_option_id);	
								$mage_attribute_temp = '';
								if(in_array($mage_attr_id, $mageSizeAttrIds)){
									$mage_attribute_temp = 'size';
								}elseif(in_array($mage_attr_id, $mageColorAttrIds)){
									$mage_attribute_temp = 'color';
								}
								
								if(trim($meli_value_idArr['0'])!=''){
									$meliAtrCount ++;
									$data = array(
													'category_id'				=> $category_id,
													'mage_attribute'			=> $mage_attribute_temp,
													'mage_attribute_original'	=> $mage_attribute,
													'mage_attribute_value'		=> $mage_attribute_value,
													'meli_attribute_id'			=> $meli_attribute_id,
													'meli_value_id'				=> $meli_value_idArr['0'],
													'store_id'					=> $storeID,
													'mage_attribute_option_id'  => $mage_attribute_option_id,
													'sort_order' => $k
													);
									if($attribute_mapping_idArr['0']){
										$data['attribute_mapping_id'] = $attribute_mapping_idArr['0'];
									}
									$model = Mage::getModel('items/meliattributevaluemapping');	
									$model->setData($data);
									$model->save();
								}
							}
						}
					}
				}
		}catch(Exception $e){
			$commonModel->saveLogger($this->moduleName, "Exception", $this->fileName, $e->getMessage());	
		}
		if(!$meliAtrCount){
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('items')->__("Attribute value(s) required for mapping."));
		} else {
			Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('items')->__("Attribute has been  mapped successfully."));
		}
		$urlParam ='';
		
		if($this->getRequest()->getPost('store_id')){	$urlParam .= 'store/'.$this->getRequest()->getPost('store_id').'/'; }
		if($this->getRequest()->getPost('root_id_hidden')){	$urlParam .= 'root_id/'.$this->getRequest()->getPost('root_id_hidden').'/'; }
		if($this->getRequest()->getPost('category_id_hidden')){ $urlParam .= 'category_id/'.$this->getRequest()->getPost('category_id_hidden').'/'; }
		if($this->getRequest()->getPost('attribute_id_hidden')){ $urlParam .= 'attribute_id/'.$this->getRequest()->getPost('attribute_id_hidden').'/'; }
		
		$this->_redirect('*/*/index/'.$urlParam);
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
			try{
				$commonModel = Mage::getModel('items/common');
				$store_id = Mage::helper('items')->_getStore()->getId();
				if ($data = $this->getRequest()->getPost() && isset($_FILES['filename']['name']) && $_FILES['filename']['error'] == 0) {
				$ext = pathinfo($_FILES['filename']['name'],PATHINFO_EXTENSION);
				if($ext == 'csv'){
					$homepage = file_get_contents($_FILES['filename']['tmp_name']);
					$i = 0;
					if (($handle = fopen($_FILES['filename']['tmp_name'], "r")) !== FALSE) {
						$arraySuccess = array();
						$arrayError = array();
						$attrData = array();
						while (($data = fgetcsv($handle)) !== FALSE) {
							if($i!=0){
								if(trim($data['0'])!=''){
								
										$collection =  Mage::getModel('eav/entity_attribute_option')
													-> getCollection()
													-> setStoreFilter($store_id)
													-> join('attribute','attribute.attribute_id=main_table.attribute_id', 'attribute_code')
													-> addFieldToFilter('attribute.attribute_code',mysql_escape_string($data['1']))
													-> addFieldToFilter('tsv.value',mysql_escape_string($data['2']));			

										$option_id = '';
										$optionIdArr = $collection->getData();
										$option_id = $optionIdArr['0']['option_id'];
										
										if(trim($data['0'])=='color'){
											//check if the data already exist with the same relation
											$collection  = Mage::getModel('items/meliattributevaluemapping')->getCollection()
														-> addFieldToFilter('main_table.mage_attribute_option_id',mysql_escape_string($option_id)) 
														-> addFieldToFilter('mage_attribute',mysql_escape_string($data['0']))
														-> addFieldToFilter('mage_attribute_original',mysql_escape_string($data['1']))
														-> addFieldToFilter('main_table.category_id',mysql_escape_string($data['5']))
														-> addFieldToFilter('main_table.store_id',mysql_escape_string($data['7']))
														-> addFieldToFilter('mage_attribute_value',mysql_escape_string($data['2']));

											$attrExistColorArr = array();			
											$attrExistColorArr = $collection->getData();

											$collectionPrimary = Mage::getModel('items/melicategories')->getCollection()
																->addFieldToFilter('main_table.meli_category_id',mysql_escape_string($data['5']))
																->addFieldToFilter('mcav.meli_value_name',mysql_escape_string($data['3']))
																->addFieldToFilter('mca.meli_attribute_name','Color Primario')
																->addFieldToFilter('mca.meli_attribute_type ',mysql_escape_string($data['0']))
																->addFieldToFilter('mcav.meli_category_id',mysql_escape_string($data['5']));
											$collectionPrimary -> getSelect()
																-> joinleft(array('mca'=>'mercadolibre_category_attributes'), "main_table.meli_category_id = mca.category_id and mca.meli_attribute_type = '".mysql_escape_string($data['0'])."'",array('mca.*'))
																	-> joinleft(array('mcav'=>'mercadolibre_category_attribute_values'), "mca.meli_attribute_id = mcav.attribute_id AND mcav.meli_category_id = '".mysql_escape_string($data['5'])."'",array('mcav.meli_value_id','mcav.meli_value_name'));
																				
											
											$collectionSecundario = Mage::getModel('items/melicategories')->getCollection()
																->addFieldToFilter('main_table.meli_category_id',mysql_escape_string($data['5']))
																->addFieldToFilter('mcav.meli_value_name',mysql_escape_string($data['3']))
																->addFieldToFilter('mca.meli_attribute_name','Color Secundario')
																->addFieldToFilter('mca.meli_attribute_type ',mysql_escape_string($data['0']))
																->addFieldToFilter('mcav.meli_category_id',mysql_escape_string($data['5']));
											$collectionSecundario -> getSelect()
																-> joinleft(array('mca'=>'mercadolibre_category_attributes'), "main_table.meli_category_id = mca.category_id and mca.meli_attribute_type = '".mysql_escape_string($data['0'])."'",array('mca.*'))
																	-> joinleft(array('mcav'=>'mercadolibre_category_attribute_values'), "mca.meli_attribute_id = mcav.attribute_id AND mcav.meli_category_id = '".mysql_escape_string($data['5'])."'",array('mcav.meli_value_id','mcav.meli_value_name'));
																
												$attrColorArr = array();			
												foreach($collectionPrimary->getData() as $rowPrimary){
														$attrColorArr[] = $rowPrimary;
												}	
												foreach($collectionSecundario->getData() as $rowSecundario){
														$attrColorArr[] = $rowSecundario;
												}	

												if(count($attrColorArr) > 0){
													$k = 0;
													foreach($attrColorArr as $atrKey => $valAtr){
														$mappingModel = Mage::getModel('items/meliattributevaluemapping');
														//update the new value
														$setDataArr = array(
																	'category_id' => mysql_escape_string($valAtr['category_id']),
																	'meli_attribute_id' => $valAtr['meli_attribute_id'],
																	'meli_value_id' => $valAtr['meli_value_id'],
																	'mage_attribute' => mysql_escape_string($data['0']),
																	'mage_attribute_original' => mysql_escape_string($data['1']),	
																	'mage_attribute_value' => mysql_escape_string($data['2']),
																	'mage_attribute_option_id' => mysql_escape_string($option_id),
																	'store_id' => mysql_escape_string($data['7']),
																	'sort_order' => $k
																	);

														if(isset($attrExistColorArr[$atrKey]['attribute_mapping_id']) && $attrExistColorArr[$atrKey]['attribute_mapping_id']){
															 $setDataArr['attribute_mapping_id'] =  $attrExistColorArr[$atrKey]['attribute_mapping_id'];
														}
														$mappingModel->setData($setDataArr);
														$mappingModel-> save();
														$arraySuccess[] = $data['0'];
														$k ++;
													}
												} else {
														$errorCategoryArr[] = $data['4'];
												}
											
										}else{
											//check if the data already exist with the same relation
											$collection = Mage::getModel('items/meliattributevaluemapping')->getCollection() 
														->addFieldToFilter('main_table.mage_attribute_option_id',mysql_escape_string($option_id))  	
														->addFieldToFilter('mage_attribute',mysql_escape_string($data['0']))
														->addFieldToFilter('mage_attribute_original',mysql_escape_string($data['1']))
														->addFieldToFilter('mage_attribute_value',mysql_escape_string($data['2']))
														->addFieldToFilter('main_table.category_id',mysql_escape_string($data['4']))
														->addFieldToFilter('main_table.store_id',mysql_escape_string($data['6']))
														->addFieldToFilter('mcav.meli_value_name',mysql_escape_string($data['3']))
														->addFieldToFilter('mcav.meli_category_id',mysql_escape_string($data['4']));
											$collection -> getSelect()
														-> joinleft(array('melicat'=>'mercadolibre_categories'), "main_table.category_id = melicat.meli_category_id" ,array('melicat.category_id','melicat.meli_category_name','melicat.meli_category_id'))
														-> joinleft(array('mca'=>'mercadolibre_category_attributes'), "main_table.category_id = mca.category_id and mca.meli_attribute_type = '".mysql_escape_string($data['0'])."'",array('mca.*'))
														-> joinleft(array('mcav'=>'mercadolibre_category_attribute_values'), "mca.meli_attribute_id = mcav.attribute_id AND mcav.meli_category_id = '".mysql_escape_string($data['4'])."'",array('mcav.meli_value_id','mcav.meli_value_name'));
														
												//update the new value id data doesnt exists
												$melicategoriesColl = Mage::getModel('items/melicategories')->getCollection()
																	->addFieldToFilter('main_table.meli_category_id',mysql_escape_string($data['4']))
																	->addFieldToFilter('mcav.meli_value_name',mysql_escape_string($data['3']))
																	->addFieldToFilter('mcav.meli_category_id',mysql_escape_string($data['4']));
												$melicategoriesColl -> getSelect()
																	-> joinleft(array('mca'=>'mercadolibre_category_attributes'), "main_table.meli_category_id = mca.category_id and mca.meli_attribute_type = '".mysql_escape_string($data['0'])."'",array('mca.*'))
																	-> joinleft(array('mcav'=>'mercadolibre_category_attribute_values'), "mca.meli_attribute_id = mcav.attribute_id AND mcav.meli_category_id = '".mysql_escape_string($data['4'])."'",array('mcav.meli_value_id','mcav.meli_value_name'));

												$melicategoriesArr = array();
												$melicategoriesArr = $melicategoriesColl->getData();

												if(count($melicategoriesArr) > 0){
													$mappingModel = Mage::getModel('items/meliattributevaluemapping');
													//update the new value
													$setDataArr = array(
																	'category_id' => $melicategoriesArr['0']['meli_category_id'],
																	'meli_attribute_id' => $melicategoriesArr['0']['meli_attribute_id'],
																	'meli_value_id' => $melicategoriesArr['0']['meli_value_id'],
																	'mage_attribute' => mysql_escape_string($data['0']),
																	'mage_attribute_original' => mysql_escape_string($data['1']),	
																	'mage_attribute_value' => mysql_escape_string($data['2']),
																	'mage_attribute_option_id' => mysql_escape_string($option_id),
																	'store_id' => mysql_escape_string($data['6']),
																	'sort_order' => 0
																	);

													if(count($collection->getData()) > 0){
														 $meliMappingArr = array();
														 $meliMappingArr = $collection->getData();
														 $setDataArr['attribute_mapping_id'] = $meliMappingArr['0']['attribute_mapping_id'];
													}
													$mappingModel-> setData($setDataArr);
													$mappingModel-> save();
													$arraySuccess[] = $data['0'];
												} else {
													$errorCategoryArr[] = $data['3'];
												}
										}
								}else{
									$arrayError[] = $data['0'];
								}
							}
							$i++;
						}
						
							fclose($handle);
							
							if(count($arraySuccess) > 0){
								$successMageAttr = implode(',',$arraySuccess);
								/* Success Message */
								$this->successMessage = 'Message :: Mage Attribute ('.$successMageAttr.') has been mapped successfully.';
								$commonModel->saveLogger($this->moduleName, "Error", $this->fileName, $this->successMessage);
							}
							if(count($arrayError) > 0){
								$errorMageAttr = implode(',',$arrayError);
								/* Error Message */
								$this->errorMessage = 'Error :: Mage Attribute ('.$errorMageAttr.') unable mapped';
								$commonModel->saveLogger($this->moduleName, "Error", $this->fileName, $this->errorMessage);
							}
							
							$this->successMessage = 'Message :: Import Attribute mapped successfully.';
							$commonModel->saveLogger($this->moduleName, "Error", $this->fileName, $this->successMessage);
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
			 Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('items')->__('Attributes are mapped successfully.'));
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

    public function massDeleteAction() {
        $itemsIds = $this->getRequest()->getParam('items');
        if(!is_array($itemsIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($itemsIds as $itemsId) {
                    $items = Mage::getModel('items/melicategories')->load($itemsId);
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
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
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
					$this->_addContent($this->getLayout()->createBlock('items/adminhtml_attributemapping_edit'))
						->_addLeft($this->getLayout()->createBlock('items/adminhtml_attributemapping_edit_tabs'));
					$this->renderLayout();
				} else {
					Mage::getSingleton('adminhtml/session')->addError(Mage::helper('items')->__('Item does not exist'));
					$this->_redirect('*/*/');
				}
			}catch(Exception $e){
					$commonModel->saveLogger($this->moduleName, "Exception", $this->fileName, $e->getMessage());	
			}
		}
     public function exportCsvAction()
    {
       try{
			$commonModel = Mage::getModel('items/common');
			$category_id = '';
			
			$store_id = Mage::helper('items')->_getStore()->getId();
			$attribute_id = '';
			if($this->getRequest()->getParam('attribute_id')!=''){
				$attribute_id = $this->getRequest()->getParam('attribute_id');
			} 
			
			$category_id = 0;
			if($this->getRequest()->getParam('category_id')){
				$category_id = $this->getRequest()->getParam('category_id');
			}
			
			$collection =  Mage::getModel('eav/entity_attribute_option')
						-> getCollection()
						//-> setStoreFilter($store_id)
						 -> addFieldToFilter('mavm.store_id', $store_id)
						-> join('attribute','attribute.attribute_id=main_table.attribute_id', 'attribute_code')
						-> addFieldToFilter('attribute.attribute_id',$attribute_id);
						//-> addFieldToFilter('tsv.value', array('neq' => 'NULL' ));			
						
			$collection -> getSelect()
						-> joinleft(array('mavm'=>'mercadolibre_attribute_value_mapping'), " main_table.option_id = mavm.mage_attribute_option_id AND mavm.category_id = '".$category_id."'",array('mavm.*'))
						-> joinleft(array('melicat'=>'mercadolibre_categories'), 'mavm.category_id = melicat.meli_category_id',array('melicat.category_id','melicat.meli_category_name','melicat.meli_category_id'))
						-> joinleft(array('mca'=>'mercadolibre_category_attributes'), 'mavm.category_id = mca.category_id and mavm.meli_attribute_id = mca.meli_attribute_id ',array('mca.meli_attribute_name'))
						-> joinleft(array('mcav'=>'mercadolibre_category_attribute_values'), 'mavm.meli_value_id = mcav.meli_value_id and mavm.category_id = mcav.meli_category_id ',array('mcav.meli_value_name'));
			
			$MappedData = $collection->getData();
			if(isset($MappedData['0']['attribute_code']) && trim($MappedData['0']['attribute_code'])!=''){
				$fileName = 'attributemapping-'.$MappedData['0']['attribute_code'].'.csv';
			}
			$content = '"Magento Attribute","Magento Attribute Code","Magento Attribute Value","Meli Value ","Meli Category ID","Meli Category Name","Store Id"'."\n";
			$meliPrimaryVal = array(); $meliSecondaryVal = array(); $mageAttr = array(); $mageAttrVal = array(); $meliCatId = array(); $meliCatName = array(); $total = 1;
			if(count($MappedData) > 0){
				foreach($MappedData as $row){
					if($row['mage_attribute'] =='color'){
						if($row['meli_attribute_name']=='Color Primario'){ $meliPrimaryVal[] = $row['meli_value_name']; }
						if($row['meli_attribute_name']=='Color Secundario'){ $meliSecondaryVal[] = $row['meli_value_name']; }
						if($total%2==0){
							$mageAttr[] = $row['mage_attribute'];
							$mageAttrCode[] = $MappedData['0']['attribute_code'];
							$mageAttrVal[] = $row['mage_attribute_value'];
							$meliCatId[] = $row['meli_category_id'];
							$meliCatName[] = $row['meli_category_name'];
						}
						$total++;
					}else{
						$content .= '"'.$row['mage_attribute'].'","'.$MappedData['0']['attribute_code'].'","'.$row['mage_attribute_value'].'","'.$row['meli_value_name'].'","'.$row['meli_category_id'].'","'.$row['meli_category_name'].'",'.$store_id.''."\n";

					}
				}
				if($total!=1){
					$content = '"Magento Attribute","Magento Attribute Code","Magento Attribute Value","Meli Value Primary","Meli Value Secondary","Meli Category ID","Meli Category Name","Store Id"'."\n";
					for ($i=0; $i<count($meliPrimaryVal); $i++){
						$content .= '"'.$mageAttr[$i].'","'.$mageAttrCode[$i].'","'.$mageAttrVal[$i].'","'.$meliPrimaryVal[$i].'","'.$meliSecondaryVal[$i].'","'.$meliCatId[$i].'","'.$meliCatName[$i].'",'.$store_id.''."\n";
					}
				}
			}	 
			$this->_sendUploadResponse($fileName, $content);
		}catch(Exception $e){
				$commonModel->saveLogger($this->moduleName, "Exception", $this->fileName, $e->getMessage());	
		}
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
}