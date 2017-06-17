<?php
ob_start("ob_gzhandler");
class Mercadolibre_Items_Adminhtml_CategorymappingController extends Mage_Adminhtml_Controller_action
{

	private $moduleName = "Items";
	private $fileName = "CategoryMappingController.php";
	
	//message variable
	private $infoMessage = "";
	private $errorMessage = "";
	private $successMessage = "";
	
	
	protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', Mage::helper('items')-> getMlDefaultStoreId());
        return Mage::app()->getStore($storeId);
    }
	
	protected function _initAction() {

		$this->loadLayout()
			->_setActiveMenu('categorymapping/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
		return $this;
	}   
 
	public function indexAction() {		
		try{
			$catFinalAddArr = array();
			$commonModel = Mage::getModel('items/common');
			/* Insert Not Existing mage_category_id Into meli_categories_mapping Table  */
			$collection =  Mage::getModel('items/melicategoriesmapping')->getCollection()
					    -> addFieldToSelect('mage_category_id')
					    -> addFieldToFilter('store_id', $this->_getStore()->getId());
						
			$mageCategoryIdMapping = $collection->getData();
			$mageCatInCatMappingArr = array();
			if(count($mageCategoryIdMapping) > 0){
				foreach($mageCategoryIdMapping as $magCatRow){
					$mageCatInCatMappingArr[] =  $magCatRow['mage_category_id'];
				}
			}
			$catDeleteArr = array();
			if(count($mageCatInCatMappingArr) > 0){
				$mageCatInCatMappingArr = array_unique($mageCatInCatMappingArr);
				$catDeleteArr = array_unique($mageCatInCatMappingArr);
			}
			/************************************************************************************/
			//$category = array();
			$catData = Mage::app()->getStore($this->_getStore()->getId())->getRootCategoryId();
			$category = $commonModel -> getMageFilterCategoryIds($catData);
			if(count($category) > 0){
				$category = array_unique($category);
				$catFinalAddArr = array_unique($category);
			}
			/************************************************************************************/		
			if(count($mageCatInCatMappingArr) > 0 && count($category) > 0){
					$catFinalAddArr = array();
					$catDeleteArr = array();
					$catFinalAddArr = array_diff($category,$mageCatInCatMappingArr);
					$catDeleteArr = array_diff($mageCatInCatMappingArr,$category);
			}
			
			$write = Mage::getSingleton('core/resource')->getConnection('core_write');
			$sql_meli_mapping = '';
			if (is_array($catFinalAddArr) && count($catFinalAddArr) > 0){
				foreach($catFinalAddArr as $Catkey => $category_id){
					if($category_id != 3){
						$cat = Mage::getModel('catalog/category');
						$cat->load($category_id);
						 if($cat->getIsActive()){ // if category is active
							$entity_id = $cat->getId();
							//echo "<br>".$name = $cat->getName();
							$sql_meli_mapping .= "insert into `mercadolibre_categories_mapping` set store_id = '".$this->_getStore()->getId()."' , mage_category_id ='".$entity_id."'".";";	
					
						 }
					}
				}
				$write->multiQuery($sql_meli_mapping); 
			}
			// Delete mage_category_id
			if(count($catDeleteArr) > 0){
				$deleteCatIds = implode(',',$catDeleteArr);
				$delete_meli_mapping = "DELETE FROM mercadolibre_categories_mapping where  mage_category_id IN($deleteCatIds) AND store_id = '".$this->_getStore()->getId()."'";
				$write->Query($delete_meli_mapping); 
			}
		} catch(Exception $e){
				$commonModel->saveLogger($this->moduleName, "Exception", $this->fileName, $e->getMessage());	
		}
						
		$this->_initAction()
			->renderLayout();	
	}
	public function postAction() {
		try{
			$commonModel = Mage::getModel('items/common');
			$MappingId = $this->getRequest()->getPost('mapping_id');
			$MageCategoryId = $this->getRequest()->getPost('mage_category_id');
			$MeliCategoryId = $this->getRequest()->getPost('meli_category_id');
			$store_id = $this->getRequest()->getPost('store_id');
			$meli_root_id = $this->getRequest()->getPost('meli_root_id');

			$data = array();
			$mappedCat = 0;
			if(count($MageCategoryId) > 0){
				foreach($MageCategoryId as $key => $value){
					if(trim($value)!=''){
						if($MeliCategoryId[$key] !='PLEASE_SELECT'){                                
							$MeliCateId = ($MeliCategoryId[$key] !='NO_MAPPING')?$MeliCategoryId[$key]: 'NO_MAPPING';
							$data = array('mapping_id'=>$MappingId[$key], 'mage_category_id'=>$value,'meli_category_id'=>$MeliCateId, 'root_id'=>trim($meli_root_id));
							$model  = Mage::getModel('items/melicategoriesmapping');
							if (!empty($data)) {
									$mappedCat++;
									$model->setData($data);
									$model->setCreatedTime(now());
									$model->setUpdateTime(now());   
									$model->setStoreId($store_id); 
									$model->save();
							}
						}
					}                
				}
			}
			$rootIdArr =  explode('root_id', $_SERVER['HTTP_REFERER']);
			if(count($rootIdArr)== 2){
					if($mappedCat){
				 		Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('items')->__('Categories have been mapped successfully.'));
					}
					$this->_redirect('*/*/index/root_id'.$rootIdArr['1']);
			}else {
				if($mappedCat){
				 	Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('items')->__('Categories have been mapped successfully.'));
				}
				$this->_redirect('*/*/index');
			}
		}catch(Exception $e){
			$commonModel->saveLogger($this->moduleName, "Exception", $this->fileName, $e->getMessage());	
		}
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
				$this->_addContent($this->getLayout()->createBlock('items/adminhtml_categorymapping_edit'))
					->_addLeft($this->getLayout()->createBlock('items/adminhtml_categorymapping_edit_tabs'));
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
								$resMlMapping = Mage::getModel('items/melicategoriesmapping')
											 -> getCollection()
											 -> addFieldToFilter('mage_category_id',mysql_escape_string($data['0']))
											 -> addFieldToFilter('store_id',mysql_escape_string($data['5']))
											 -> getColumnValues('mapping_id');
											 
								$MageCat =  Mage::getModel('catalog/category')
										   ->getCollection()
										   ->addFieldToFilter('entity_id',mysql_escape_string($data['0']))
										   ->addFieldToFilter('is_active', array('eq'=>'1'))
										   ->getColumnValues('entity_id');
								
								$mapping_id = ($resMlMapping['0'])?$resMlMapping['0']:'';
								if(trim($data['2'])!='' && count($MageCat) > 0){
									$mappingModel = Mage::getModel('items/melicategoriesmapping');
									$mappingModel->setMeliCategoryId(mysql_escape_string($data['2']));
									$mappingModel->setMageCategoryId(mysql_escape_string($data['0']));
									$mappingModel->setRootId(mysql_escape_string($data['4']));
									$mappingModel->setCreatedTime(now());
									$mappingModel->setStoreId(mysql_escape_string($data['5']));
									if($mapping_id){
										$mappingModel->setMappingId($mapping_id);
										$mappingModel->setUpdateTime(now());
									}
									$mappingModel-> save();
									$arraySuccess[] = $data['0'];
								}else{
									$arrayError[] = $data['0'];
								}
							}
							$i++;
						}
							fclose($handle);
							
							if(count($arraySuccess) > 0){
								$successMageCate = implode(',',$arraySuccess);
								/* Success Message */
								$this->successMessage = 'Message :: Mage Category ('.$successMageCate.') has been mapped successfully.';
								$commonModel->saveLogger($this->moduleName, "Error", $this->fileName, $this->successMessage);
							}
							if(count($arrayError) > 0){
								$errorMageCate = implode(',',$arrayError);
								/* Error Message */
								$this->errorMessage = 'Error :: Mage Category ('.$errorMageCate.') unable mapped';
								$commonModel->saveLogger($this->moduleName, "Error", $this->fileName, $this->errorMessage);
							}
							
							$this->successMessage = 'Message :: Import Category mapped successfully.';
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
			 Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('items')->__('Category mapped successfully.'));
       		 $this->_redirect('*/*/');
        }
 

    public function exportCsvAction()
    {
        try{
			$commonModel = Mage::getModel('items/common');
			$fileName   = 'categorymapping.csv';				 
			$collection =  Mage::getModel('items/melicategoriesmapping')
					-> getCollection()
					-> addFieldToFilter('main_table.store_id', $this->_getStore()->getId())
					-> addFieldToFilter('eav_entity_type.entity_type_code', 'catalog_category')
					-> addFieldToFilter('e.children_count', '0');
			$collection -> getSelect()
					-> join(array('e'=>'catalog_category_entity'),'e.entity_id = main_table.mage_category_id')
					-> join(array('at_is_active'=>'catalog_category_entity'),'at_is_active.entity_id = e.entity_id')
					-> join(array('eav_entity_type'=>'eav_entity_type'),'eav_entity_type.entity_type_id = e.entity_type_id',array('eav_entity_type.entity_type_code'))
					-> joinleft(array('mc' => 'mercadolibre_categories'), 'mc.meli_category_id = main_table.meli_category_id ', array("if(main_table.meli_category_id = 'NO_MAPPING','No Mapping', mc.meli_category_name) as meli_category_name","if(mc.has_attributes = 1,'Yes','NO') as has_attributes", 'mc.category_id as mc_category_id'))
					-> joinleft(array('mcf' => 'mercadolibre_categories_filter'), "mcf.meli_category_id = main_table.meli_category_id AND mcf.store_id ='".$this->_getStore()->getId()."'" , array('mcf.meli_category_path'));
					
							
			$MappedData = $collection->getData();

			$content = '"Mage Category Id","Mage Category Name","Maped MercadoLibre Category Id","Maped MercadoLibre Category Name","Mercado Libre Root Category id","Store Id"'."\n";
			if(count($MappedData) > 0){
				foreach($MappedData as $row){
					
					$cat = Mage::getModel('catalog/category');
					$cat->load($row['mage_category_id']);

					$content .= '"'.$cat->getId().'","'.$cat->getName().'","'.$row['meli_category_id'].'","'.$row['meli_category_name'].'",'.$row['root_id'].', '.$row['store_id'].''."\n";
				}
			}	 
			$this->_sendUploadResponse($fileName, $content);
		}catch(Exception $e){
				$commonModel->saveLogger($this->moduleName, "Exception", $this->fileName, $e->getMessage());	
		}
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
