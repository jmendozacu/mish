<?php

class Mercadolibre_Items_Model_Melicategories extends Mage_Core_Model_Abstract
{
   
    private $site_id = 'NULL';
	private $root_id = '';
   	private $moduleName = "Items";
	private $fileName = "MeliCategories.php";
	private $fileNameAttr = '';
	private $fileNameCat = 'allCategoryJsonData.txt';
	private $to = '';
	
	//message variable
	private $infoMessage = "";
	private $errorMessage = "";
	private $successMessage = "";
	private $BASE_API_URL = "https://api.Mercadolibre.com/";
   
   
    public function _construct()
    {
        parent::_construct();
        $this->_init('items/melicategories');
    }
	
	
	public function getMLCatergoriesAllData()
    {	
		try{
			//Initilize logger model
			$commonModel = Mage::getModel('items/common');
			$filename = $commonModel->forwardSlashToBackSlash(Mage::getBaseDir('code').DS.'community\Mercadolibre\dump\category\meli_categories.csv');
			if (file_exists($filename)) { // Make File Empty
				file_put_contents($filename,'');
			}
			/* Get Site Id */
			$sitesModel = Mage::getModel('items/melisites')
						-> getCollection()
						-> getData();
			$siteIdsArr = array();
			if(count($sitesModel) > 0){
				foreach($sitesModel as $sitedata){
					$siteIdsArr[] = $sitedata['site_id'];
				}
			}
			$siteIdCountry = '';
			$siteIdCountry =  Mage::helper('items')->getMlSiteId();
			/* Get Base URL Id */
			if(Mage::getStoreConfig("mlitems/mltokenaccess/mlapiurl",Mage::app()->getStore())){
				$api_url = Mage::getStoreConfig("mlitems/mltokenaccess/mlapiurl",Mage::app()->getStore());
			} else {
				$this->errorMessage = "Error :: Api Url Not Found OR Invalid";
				$this->saveLogger($this->moduleName, "Exception", $this->fileName, $this->errorMessage);
				$commonModel->sendNotificationMail($this->to, 'ML Catergories All Data Cron Error', $this->errorMessage);
			}
			$this->to = Mage::getStoreConfig("mlitems/meligeneralsetting/notificationemailid",Mage::app()->getStore());
			$this->infoMessage ="INFORMATION:: getMLCatergoriesAllDataAction Started";
			$commonModel->saveLogger($this->moduleName, "Information", $this->fileName, $this->infoMessage);
			$commonModel->sendNotificationMail($this->to, 'ML Catergories All Data Cron Started', $this->infoMessage);
			$resCheckUpdate = array();
			
			/*  Get & save all category json data into allCategoryJsonData.txt */
			$service_url = $api_url.'/sites/'.$siteIdCountry.'/categories/all';
			$x_content_created = $this->getMLXContentCreated($service_url);
			$resMelicategoryupdate = Mage::getModel('items/melicategoryupdate')->getCollection()->addFieldToFilter('created_datetime',$x_content_created);
			$resCheckUpdate = $resMelicategoryupdate->getData();
			
			if(empty($resCheckUpdate)){
				$resCheckUpdate['0']['update_id'] = 0;
			}
			if(trim($resCheckUpdate['0']['update_id']) == '' || trim($resCheckUpdate['0']['update_id']) == '0'){
				/** TRUNCATE TABLE `meli_categories` to add new data again */
				$write = Mage::getSingleton('core/resource')->getConnection('core_write');
				$write->query("TRUNCATE TABLE `mercadolibre_categories`");
				$fileName = 'allCategoryJsonData';
				//$dir = $commonModel->getChangedSlash(Mage::getBaseDir('code').DS.'community/Mercadolibre/dump/category'); // FOR LINUX
				$dir = $commonModel->getChangedSlash(Mage::getBaseDir('code').DS.'community\Mercadolibre\dump\category');  // FOR WINDOWS
				//try{
//					if (!is_dir($dir)) {
//						if (!@mkdir($dir , 0777, true)) {
//						}
//					} else {
//						chmod($dir ,0777);						
//					}
//				}catch(Exception $e){
//					$this->errorMessage = "Error::Unable to directory create (".$dir.")";
//					$commonModel->saveLogger($this->moduleName, "Error", $this->fileName, $this->errorMessage);
//				}

				foreach($siteIdsArr as $siteId){
					if(trim($siteId)!=''){
						try{
							/*  Get & save all category json data into allCategoryJsonData.txt */
							$service_url = $api_url.'/sites/'.$siteId.'/categories/all';
							$data = $commonModel ->connect($service_url);
							try{
								if (!@file_put_contents($dir . DS . $fileName . '.txt',  $data)) {
									return false;
								}
							}catch(Exception $e){
								$this->errorMessage = "Error::Unable to write data in file(".$dir . DS . $fileName ."txt)";
								$commonModel->saveLogger($this->moduleName, "Error", $this->fileName, $this->errorMessage);
							}
							try{
								$dataFile = $dir . DS . $fileName . '.txt';
								if (file_exists($dataFile) && is_readable($dataFile)) {
									$dataFileData  = file_get_contents($dataFile);
								}
							}catch(Exception $e){
									$this->errorMessage = "Error::Unable to read data in file(".$dir . DS . $fileName ."txt)";
									$commonModel->saveLogger($this->moduleName, "Error", $this->fileName, $this->errorMessage);
							}
							/* Get Json data to array*/
							$dataArr = json_decode($dataFileData, true);
							$catList = array();
							if(count($dataArr) > 0){
								foreach($dataArr as $row){
										$shipping_modes = '';	
									if(trim($siteId) == trim($siteIdCountry)){
										$site_id = 'NULL';
										$site_id = substr($row['id'],0,3);
										$root_id = (Mage::helper('items')->getMLRootId($row['path_from_root'])) ? Mage::helper('items')->getMLRootId($row['path_from_root']):0;
										$has_attributes = (isset($row['attribute_types']) && trim($row['attribute_types']) == 'variations')?1:0;
										$listing_allowed = (isset($row['settings']['listing_allowed']) && $row['settings']['listing_allowed'])?$row['settings']['listing_allowed']:0;	
										$buying_allowed = (isset($row['settings']['buying_allowed']) && $row['settings']['buying_allowed'])?$row['settings']['buying_allowed']:0;	
										$shipping_modes = serialize($row['settings']['shipping_modes']);														
										$catList[] = array('NULL', $row['id'],$row['name'], $site_id, $has_attributes,$root_id,$listing_allowed,$buying_allowed,$shipping_modes);
									} else if(!Mage::helper('items')->getMLRootId($row['path_from_root'])) {
										$site_id = 'NULL';
										$site_id = substr($row['id'],0,3);
										$root_id = (Mage::helper('items')->getMLRootId($row['path_from_root'])) ? Mage::helper('items')->getMLRootId($row['path_from_root']):0;
										$has_attributes = (isset($row['attribute_types']) && trim($row['attribute_types']) == 'variations')?1:0;	
										$listing_allowed = (isset($row['settings']['listing_allowed']) && $row['settings']['listing_allowed'])?$row['settings']['listing_allowed']:0;	
										$buying_allowed = (isset($row['settings']['buying_allowed']) && $row['settings']['buying_allowed'])?$row['settings']['buying_allowed']:0;
										$shipping_modes = serialize($row['settings']['shipping_modes']);
										$catList[] = array('NULL', $row['id'],$row['name'], $site_id, $has_attributes,$root_id,$listing_allowed,$buying_allowed,$shipping_modes);
									}
								}
							}
							/* get category data into meli_categories.csv*/
							//$fp = fopen($dir . DS.'meli_categories.csv', 'w');
							$fp = fopen($dir . DS.'meli_categories.csv', 'a+');
							foreach ($catList as $fields) {
								fputcsv($fp, $fields);
							}
							fclose($fp);  
						}catch(Exception $e){
							$this->infoMessage = "INFORMATION::All Categories already added in database";
							$commonModel->saveLogger($this->moduleName, "Information", $this->fileName, $this->infoMessage);
						}
					}
					$this->successMessage = 'Data has been saved for URL:'.$service_url. ' Successfully.';
					$commonModel->saveLogger($this->moduleName, "Meaasge::", $this->fileName, $this->successMessage);
				}
				try{
					$filename = $commonModel->forwardSlashToBackSlash(Mage::getBaseDir('code').DS.'community\Mercadolibre\dump\category\meli_categories.csv');
					$sql = "LOAD DATA  INFILE '".$filename."' INTO TABLE `mercadolibre_categories` character set UTF8 FIELDS TERMINATED BY ',' enclosed by '\"' lines terminated by '\n'";
					$write -> query($sql);
				} catch(PDOException $e){
					$this->errorMessage = $e->getTrace()."::".$e->getMessage();
					$commonModel->saveLogger($this->moduleName, "PDOException", $this->fileName, $this->errorMessage);
				}
				/* Update run_datetime time & created_datetime */
				$runDateTime = date('Y-m-d h:i:s', time());
				$melicategoryupdate = Mage::getModel('items/melicategoryupdate');
				$melicategoryupdate->setCreatedDatetime($x_content_created);
				$melicategoryupdate->setUpdateId('1'); 
				$melicategoryupdate->setRunDatetime($runDateTime);
				$melicategoryupdate->save();
				$write->query("UPDATE core_config_data  set value = '".$x_content_created."' where path='mlitems/categoriesupdateinformation/contentcreationdate'"); 
				$write->query("UPDATE core_config_data  set value = '".$runDateTime."' where path='mlitems/categoriesupdateinformation/lastrundata'"); 
				echo "Categories data has been imported successfully.";
			}
			
		}catch(Exception $e){
				$commonModel->saveLogger($this->moduleName, "Exception", $this->fileName, $e->getMessage());
				$commonModel->sendNotificationMail($this->to, 'Exception::ML Catergories All Data Cron', $e->getMessage());
		}	
		$this->infoMessage ="INFORMATION::Finished getMLCatergoriesAllDataAction ";
		$commonModel->saveLogger($this->moduleName, "Information", $this->fileName, $this->infoMessage);
		$commonModel->sendNotificationMail($this->to, 'ML Catergories All Data Cron Finished', $this->infoMessage);
    }
	
	public function getMLCategoryHasAttributes()
	{
			try{
				/* Get Base URL Id */
				if(Mage::getStoreConfig("mlitems/mltokenaccess/mlapiurl",Mage::app()->getStore())){
					$api_url = Mage::getStoreConfig("mlitems/mltokenaccess/mlapiurl",Mage::app()->getStore());
				} else {
					$this->errorMessage = "Error :: Api Url Not Found OR Invalid";
					$this->saveLogger($this->moduleName, "Exception", $this->fileName, $this->errorMessage);
					$commonModel->sendNotificationMail($this->to, 'ML Catergories All Data Cron Error', $this->errorMessage);
				}
				
				$this->to = Mage::getStoreConfig("mlitems/meligeneralsetting/notificationemailid",Mage::app()->getStore());
				//Initilize logger model
				$commonModel = Mage::getModel('items/common');
			
				$this->infoMessage ="INFORMATION::Started getMLCategoryHasAttributes ";
				$commonModel->saveLogger($this->moduleName, "Information", $this->fileName, $this->infoMessage);
				$commonModel->sendNotificationMail($this->to, 'ML Catergories HasAttributes Cron Started', $this->infoMessage);
				
				/** Get meli_category_id from meli_categories table */
				$melicategories = Mage::getModel('items/melicategories')->getCollection()->addFieldToFilter('has_attributes','1');
				$dataMLcat = $melicategories->getData();
				
				/**  Check for meli_categories data exist */
				try{
					if(count($dataMLcat) > 0){
						
						//$dir = Mage::getBaseDir('code').DS.'community/Mercadolibre/dump/category-attributes'; // FOR LINUX
						$dir = Mage::getBaseDir('code').DS.'community\Mercadolibre\dump\category-attributes';  // FOR WINDOWS
						
						try{
							if (!is_dir($dir)) 
							{
								if (!@mkdir($dir , 0777, true)) 
								{
								}
							}
						}catch(Exception $e){
							 $this->errorMessage ="Error::Unable to create directory (".$dir.")";
							 $commonModel->saveLogger($this->moduleName, "Error", $this->fileName, $this->errorMessage);
						}
					
					foreach($dataMLcat as $row){
					
						$meli_category_id = '';
						$meli_category_id = $row['meli_category_id'];
						$category_id = $row['category_id'];
						$service_url_category = $api_url."/categories/".$meli_category_id."/attributes";
						$jsonDataResp = $commonModel ->meliConnect($service_url_category);
						$json_data = $jsonDataResp['body'];
						$attributesArr = $jsonDataResp['json'];
						try{
							if(count($attributesArr) > 0)
							{
								/**
								* Update table meli_categories for has_attributes
								*/
								/*$melicateUpdate = Mage::getModel('items/melicategories');
								$melicateUpdate->setCategoryId($category_id);
								$melicateUpdate->setHasAttributes('1');
								$melicateUpdate->save();*/
								/*Save Category Attributes Json Data*/
								$this->fileNameAttr = $meli_category_id;
								if (!@file_put_contents($dir . DS . $this->fileNameAttr . '.json',  $json_data)) {
									return false;
								}
							}else {
								$this->infoMessage ="INFORMATION::No Attributes Data Found In (Meli Category Id:".$meli_category_id.")";
								$commonModel->saveLogger($this->moduleName, "Information", $this->fileName, $this->infoMessage);
							} 
						}catch(Exception $e){
							$this->errorMessage = "Error::Unable to write data in file(".$dir . DS . $this->fileNameAttr . ".json)";
							$commonModel->saveLogger($this->moduleName, "Error", $this->fileName, $this->errorMessage);
						}
					}
						
				} 
			} catch(Exception $e){
				$this->infoMessage = "INFORMATION::No Categories Data Found";
				$commonModel->saveLogger($this->moduleName, "Information", $this->fileName, $this->infoMessage);
			}
		}catch(Exception $e){
				$commonModel->saveLogger($this->moduleName, "Exception", $this->fileName,$e->getMessage());
				$commonModel->sendNotificationMail($this->to, 'Exception::ML Catergories HasAttributes Cron Started', $e->getMessage());
		}
		$this->infoMessage = "INFORMATION::Finished getMLCategoryHasAttributes";
		$commonModel->saveLogger($this->moduleName, "Information", $this->fileName, $this->infoMessage);
		$commonModel->sendNotificationMail($this->to, 'ML Catergories HasAttributes Cron Finished', $this->infoMessage);
	}
	
	public function getMLFilteredCategoryHasAttributes()
	{
			try{
				/* Get Base URL Id */
				if(Mage::getStoreConfig("mlitems/mltokenaccess/mlapiurl",Mage::app()->getStore())){
					$api_url = Mage::getStoreConfig("mlitems/mltokenaccess/mlapiurl",Mage::app()->getStore());
				} else {
					$this->errorMessage = "Error :: Api Url Not Found OR Invalid";
					$this->saveLogger($this->moduleName, "Exception", $this->fileName, $this->errorMessage);
					$commonModel->sendNotificationMail($this->to, 'ML Catergories All Data Cron Error', $this->errorMessage);
				}
				
				$this->to = Mage::getStoreConfig("mlitems/meligeneralsetting/notificationemailid",Mage::app()->getStore());
				//Initilize logger model
				$commonModel = Mage::getModel('items/common');
			
				$this->infoMessage ="INFORMATION::Started getMLFilteredCategoryHasAttributes ";
				$commonModel->saveLogger($this->moduleName, "Information", $this->fileName, $this->infoMessage);
				$commonModel->sendNotificationMail($this->to, 'ML Filtered Catergories HasAttributes Cron Started', $this->infoMessage);
				
				/** Get meli_category_id from meli_categories table */
				$melicategories = Mage::getModel('items/melicategoriesfilter')->getCollection()->addFieldToFilter('has_attributes','1');
				$dataMLcat = $melicategories->getData();
				
				/**  Check for meli_categories data exist */
				try{
					if(count($dataMLcat) > 0){
						//$dir = Mage::getBaseDir('code').DS.'community/Mercadolibre/dump/category-attributes'; // FOR LINUX
						$dir = Mage::getBaseDir('code').DS.'community\Mercadolibre\dump\category-attributes';  // FOR WINDOWS
						
						try{
							if (!is_dir($dir)) 
							{
								if (!@mkdir($dir , 0777, true)) 
								{
								}
							}
						}catch(Exception $e){
							 $this->errorMessage ="Error::Unable to create directory (".$dir.")";
							 $commonModel->saveLogger($this->moduleName, "Error", $this->fileName, $this->errorMessage);
						}
					
					foreach($dataMLcat as $row){
					
						$meli_category_id = '';
						$meli_category_id = $row['meli_category_id'];
						$category_id = $row['category_id'];
						$service_url_category = $api_url."/categories/".$meli_category_id."/attributes";
						$jsonDataResp = $commonModel ->meliConnect($service_url_category);
						$json_data = $jsonDataResp['body'];
						$attributesArr = $jsonDataResp['json'];
						try{
							if(count($attributesArr) > 0)
							{
								/**
								* Update table meli_categories for has_attributes
								*/
								/*$melicateUpdate = Mage::getModel('items/melicategoriesfilter');
								$melicateUpdate->setCategoryId($category_id);
								$melicateUpdate->setHasAttributes('1');
								$melicateUpdate->save();*/
								/*Save Category Attributes Json Data*/
								$this->fileNameAttr = $meli_category_id;
								if (!@file_put_contents($dir . DS . $this->fileNameAttr . '.json',  $json_data)) {
									return false;
								}
							}else {
								$this->infoMessage ="INFORMATION::No Attributes Data Found In (Meli Category Id:".$meli_category_id.")";
								$commonModel->saveLogger($this->moduleName, "Information", $this->fileName, $this->infoMessage);
							} 
						}catch(Exception $e){
							$this->errorMessage = "Error::Unable to write data in file(".$dir . DS . $this->fileNameAttr . ".json)";
							$commonModel->saveLogger($this->moduleName, "Error", $this->fileName, $this->errorMessage);
						}
					}
						
				} 
			} catch(Exception $e){
				$this->infoMessage = "INFORMATION::No Categories Data Found";
				$commonModel->saveLogger($this->moduleName, "Information", $this->fileName, $this->infoMessage);
			}
		}catch(Exception $e){
				$commonModel->saveLogger($this->moduleName, "Exception", $this->fileName,$e->getMessage());
				$commonModel->sendNotificationMail($this->to, 'Exception::ML Filtered Catergories HasAttributes Cron Started', $e->getMessage());
		}
		$this->infoMessage = "INFORMATION::Finished getMLFilteredCategoryHasAttributes";
		$commonModel->saveLogger($this->moduleName, "Information", $this->fileName, $this->infoMessage);
		$commonModel->sendNotificationMail($this->to, 'ML Filtered Catergories HasAttributes Cron Finished', $this->infoMessage);
	}
	
	public function getMLCategoryAttributes()
	{
			try{
				$this->to = Mage::getStoreConfig("mlitems/meligeneralsetting/notificationemailid",Mage::app()->getStore());
				
				//Initilize logger model
				$commonModel = Mage::getModel('items/common');
				$this->infoMessage = "INFORMATION::Started getMLCategoryAttributes ";
				$commonModel->saveLogger($this->moduleName, "Information", $this->fileName, $this->infoMessage);
				$commonModel->sendNotificationMail($this->to, 'ML Catergories Attributes Cron Started', $this->infoMessage);
				
				/**  Get meli_category_id from meli_categories table */
				$melicategories = Mage::getModel('items/melicategories')->getCollection()->addFieldToFilter('has_attributes','1');
				$dataMLcat = $melicategories->getData();
				//print_R($dataMLcat);
				/** Check for meli_categories data exist */			
				if(count($dataMLcat) > 0){
					/** TRUNCATE TABLE meli_category_attributes and meli_category_attribute_values before Insert data */
					
					//$dir =Mage::getBaseDir('code').DS.'community/Mercadolibre/dump/category-attributes';  // FOR LINUX
					$dir = $commonModel->getChangedSlash(Mage::getBaseDir('code').DS.'community\Mercadolibre\dump\category-attributes');// FOR WINDOWS
					$write = Mage::getSingleton('core/resource')->getConnection('core_write');
					
					$write->query("TRUNCATE TABLE `mercadolibre_category_attributes_temp`");
					$write->query("TRUNCATE TABLE `mercadolibre_category_attribute_values_temp`");		
					foreach($dataMLcat as $row){
					
						$meli_category_id = '';
						$meli_category_id = $row['meli_category_id'];
						$category_id = $row['category_id'];
						$this->fileNameAttr = $meli_category_id;
						$dataFile = $dir . DS . $this->fileNameAttr . '.json';
						try{
							if(file_exists($dataFile) && is_readable($dataFile)) {
								$json_data = file_get_contents($dataFile);
							}
						} catch(Exception $e){
							if(file_exists($dataFile)){
								$this->errorMessage = "Error:File:(".$dataFile.") not found";
							}else{
								$this->errorMessage = "Error::Permission denied (".$dataFile.")";
							}
							$commonModel->saveLogger($this->moduleName, "Error", $this->fileName, $this->errorMessage);
						}
						$attributesArr = json_decode($json_data);

						if(count($attributesArr) > 0)
						{
							$sql_meli_attr = '';
							foreach($attributesArr as $rowAttribute)
							{
								$rowAttribute = (array)$rowAttribute;
								if(isset($rowAttribute['name']) && trim($rowAttribute['name'])!=''){
									$type  = (isset($rowAttribute['type']))?$rowAttribute['type']:'NULL';
									$required  = (isset($rowAttribute['tags']->required))?1:0;	
									$sql_meli_attr .= "insert into `mercadolibre_category_attributes_temp` set category_id='".$meli_category_id."', meli_attribute_id='".$rowAttribute['id']."',meli_attribute_name='".$rowAttribute['name']."',meli_attribute_type='".$type."',required='".$required."'".";";	
									/* Last inserted id for this meli_category_attributes_temp */							
									if(isset($rowAttribute['values']) && is_array($rowAttribute['values']) && count($rowAttribute['values']) > 0)
									{
										/** Insert data into meli_category_attribute_values */
										$sql_meli_attr .= $this->getMLCategoryAttributesValue($rowAttribute['id'],$rowAttribute['values'],$meli_category_id);
									}
								}
							}
							try{
								$write->multiQuery($sql_meli_attr);
							} catch(Exception $e) {
								$this->errorMessage = $e->getTrace()."::".$e->getMessage();
								$commonModel->saveLogger($this->moduleName, "Exception", $this->fileName, $this->errorMessage);
							}
						}
					}
					
					$write->query("TRUNCATE TABLE `mercadolibre_category_attributes`");
					$write->query("TRUNCATE TABLE `mercadolibre_category_attribute_values`");
					
					$sql_final_dump = "insert into  mercadolibre_category_attributes (category_id, meli_attribute_id, meli_attribute_name, meli_attribute_type, required) select category_id, meli_attribute_id, meli_attribute_name, meli_attribute_type, required from mercadolibre_category_attributes_temp";						
					$write->query($sql_final_dump);

					$sql_final_dump = "insert into  mercadolibre_category_attribute_values (meli_category_id, attribute_id, meli_value_id, meli_value_name, meli_value_name_extended) select meli_category_id, attribute_id, meli_value_id, meli_value_name, meli_value_name_extended from mercadolibre_category_attribute_values_temp";						
					$write->query($sql_final_dump);

					//$commonModel->rrmdir($dir);
 
					
				}
			} catch(Exception $e){
					$commonModel->saveLogger($this->moduleName, "Exception", $this->fileName, $e->getMessage());
					$commonModel->sendNotificationMail($this->to, 'Exception::ML Catergories Attributes Cron Finished',$e->getMessage());
			}
			$this->infoMessage = "INFORMATION::Finished getMLCategoryAttributes ";
			$commonModel->saveLogger($this->moduleName, "Information", $this->fileName, $this->infoMessage);
			$commonModel->sendNotificationMail($this->to, 'ML Catergories Attributes Cron Finished', $this->infoMessage);	
	}
	
	public function getMLFilteredCategoryAttributes()
	{
			try{
				$this->to = Mage::getStoreConfig("mlitems/meligeneralsetting/notificationemailid",Mage::app()->getStore());
				
				//Initilize logger model
				$commonModel = Mage::getModel('items/common');
				$this->infoMessage = "INFORMATION::Started getMLFilteredCategoryAttributes ";
				$commonModel->saveLogger($this->moduleName, "Information", $this->fileName, $this->infoMessage);
				$commonModel->sendNotificationMail($this->to, 'ML Filtered Catergories Attributes Cron Started', $this->infoMessage);
				
				/**  Get meli_category_id from meli_categories table */
				$melicategories = Mage::getModel('items/melicategoriesfilter')->getCollection()->addFieldToFilter('has_attributes','1');
				$dataMLcat = $melicategories->getData();
				
				/** Check for meli_categories data exist */			
				if(count($dataMLcat) > 0){
					/** TRUNCATE TABLE meli_category_attributes and meli_category_attribute_values before Insert data */
					
					//$dir =Mage::getBaseDir('code').DS.'community/Mercadolibre/dump/category-attributes';  // FOR LINUX
					$dir = $commonModel->getChangedSlash(Mage::getBaseDir('code').DS.'community\Mercadolibre\dump\category-attributes');// FOR WINDOWS
					$write = Mage::getSingleton('core/resource')->getConnection('core_write');
					
					$write->query("TRUNCATE TABLE `mercadolibre_category_attributes_temp`");
					$write->query("TRUNCATE TABLE `mercadolibre_category_attribute_values_temp`");		
					foreach($dataMLcat as $row){
					
						$meli_category_id = '';
						$meli_category_id = $row['meli_category_id'];
						$category_id = $row['category_id'];
						$this->fileNameAttr = $meli_category_id;
						$dataFile = $dir . DS . $this->fileNameAttr . '.json';
						try{
							if(file_exists($dataFile) && is_readable($dataFile)) {
								$json_data = file_get_contents($dataFile);
							}
						} catch(Exception $e){
							if(file_exists($dataFile)){
								$this->errorMessage = "Error:File:(".$dataFile.") not found";
							}else{
								$this->errorMessage = "Error::Permission denied (".$dataFile.")";
							}
							$commonModel->saveLogger($this->moduleName, "Error", $this->fileName, $this->errorMessage);
						}
						$attributesArr = json_decode($json_data);

						if(count($attributesArr) > 0)
						{
							$sql_meli_attr = '';
							foreach($attributesArr as $rowAttribute)
							{
								$rowAttribute = (array)$rowAttribute;
								if(isset($rowAttribute['name']) && trim($rowAttribute['name'])!=''){
									$type  = (isset($rowAttribute['type']))?$rowAttribute['type']:'NULL';
									$required  = (isset($rowAttribute['tags']->required))?1:0;	
									$sql_meli_attr .= "insert into `mercadolibre_category_attributes_temp` set category_id='".$meli_category_id."', meli_attribute_id='".$rowAttribute['id']."',meli_attribute_name='".$rowAttribute['name']."',meli_attribute_type='".$type."',required='".$required."'".";";	
									/* Last inserted id for this meli_category_attributes_temp */							
									if(isset($rowAttribute['values']) && is_array($rowAttribute['values']) && count($rowAttribute['values']) > 0)
									{
										/** Insert data into meli_category_attribute_values */
										$sql_meli_attr .= $this->getMLCategoryAttributesValue($rowAttribute['id'],$rowAttribute['values'],$meli_category_id);
									}
								}
							}
							try{
								$write->multiQuery($sql_meli_attr);
							} catch(Exception $e) {
								$this->errorMessage = $e->getTrace()."::".$e->getMessage();
								$commonModel->saveLogger($this->moduleName, "Exception", $this->fileName, $this->errorMessage);
							}
						}
					}
					
					$write->query("TRUNCATE TABLE `mercadolibre_category_attributes`");
					$write->query("TRUNCATE TABLE `mercadolibre_category_attribute_values`");
					
					$sql_final_dump = "insert into  mercadolibre_category_attributes (category_id, meli_attribute_id, meli_attribute_name, meli_attribute_type, required) select category_id, meli_attribute_id, meli_attribute_name, meli_attribute_type, required from mercadolibre_category_attributes_temp";						
					$write->query($sql_final_dump);

					$sql_final_dump = "insert into  mercadolibre_category_attribute_values (meli_category_id, attribute_id, meli_value_id, meli_value_name, meli_value_name_extended) select meli_category_id, attribute_id, meli_value_id, meli_value_name, meli_value_name_extended from mercadolibre_category_attribute_values_temp";						
					$write->query($sql_final_dump);

					//$commonModel->rrmdir($dir);
				}
			} catch(Exception $e){
					$commonModel->saveLogger($this->moduleName, "Exception", $this->fileName, $e->getMessage());
					$commonModel->sendNotificationMail($this->to, 'Exception::ML Catergories Attributes Cron Finished',$e->getMessage());
			}
			$this->infoMessage = "INFORMATION::Finished getMLCategoryAttributes ";
			$commonModel->saveLogger($this->moduleName, "Information", $this->fileName, $this->infoMessage);
			$commonModel->sendNotificationMail($this->to, 'ML Catergories Attributes Cron Finished', $this->infoMessage);	
	}
	
	/**
	* Insert data into meli_category_attribute_values
	*/
	public function getMLCategoryAttributesValue($attributeId, $arrayAttribute = array(),$meli_category_id='')
	{
		try{
			$commonModel = Mage::getModel('items/common');
			$this->to = Mage::getStoreConfig("mlitems/meligeneralsetting/notificationemailid",Mage::app()->getStore());
			$write = Mage::getSingleton('core/resource')->getConnection('core_write');
			$sql_meli_attr_vals = '';
			foreach($arrayAttribute as $rowAttriVal){
				
				$rowAttriVal = (array)$rowAttriVal;
				//echo "<pre>";
				//print_r($rowAttriVal['metadata']);
				if(isset($rowAttriVal['name']) && trim($rowAttriVal['name'])!=''){
					$sql_meli_attr_vals .= "insert into `mercadolibre_category_attribute_values_temp` set meli_category_id = '".$meli_category_id."', attribute_id='".$attributeId."', meli_value_id='".$rowAttriVal['id']."',meli_value_name='".$rowAttriVal['name']."'";

					if(isset($rowAttriVal['metadata'])){
						$metaArr =  (array)$rowAttriVal['metadata'];
					 	$sql_meli_attr_vals .= " , meli_value_name_extended='".$metaArr['rgb']."'".";";	
				    }else{
					  $sql_meli_attr_vals .=";";
					}	 
				}				
		   }
		   return $sql_meli_attr_vals;
	   
	   } catch(Exception $e){
			$commonModel->saveLogger($this->moduleName, "Exception", $this->fileName, $e->getMessage());
			$commonModel->sendNotificationMail($this->to, 'Exception::ML Catergories Attributes Value', $e->getMessage());		
		}
	}
	
	public function getMLCatergoriesWithFilter($catIds = '',$storeId = '0')
    {	
			try{

				$this->to = Mage::getStoreConfig("mlitems/meligeneralsetting/notificationemailid",Mage::app()->getStore());
				$commonModel = Mage::getModel('items/common');
				
				$this->infoMessage ="INFORMATION:: getMLCatergoriesWithFilter Started";
				$commonModel->saveLogger($this->moduleName, "Information", $this->fileName, $this->infoMessage);
				$commonModel->sendNotificationMail($this->to, 'ML Catergories All Data Cron Started', $this->infoMessage);
				/* Get Root Category To Be Filter */
				if(trim($catIds)!=''){
					$rootCategory = $catIds;
				}	else {
					$rootCategory = Mage::getStoreConfig("mlitems/categoriesupdateinformation/mlrootcategories",Mage::app()->getStore());
				}
				$rootCategoryArr = explode(',',$rootCategory);
				/* Category To Be Filter Start */
				if(count($rootCategoryArr) > 0){
					$write = Mage::getSingleton('core/resource')->getConnection('core_write');
					//$write->query("TRUNCATE TABLE `mercadolibre_categories_filter`");
					$checkStoreCat = $write->fetchCol("SELECT count(*) from `mercadolibre_categories_filter` where store_id = '".$storeId."'");
					if(count($checkStoreCat) > 0 && $checkStoreCat['0'] > 0){ 
						$write->query("DELETE FROM `mercadolibre_categories_filter` where store_id = '".$storeId."'");
					}
					foreach($rootCategoryArr as $key=>$value){
						$insert_root_categories = '';
						/* Save all child for this categoty */
						$this->getMLCategoryRecursive($value,$storeId);
					}
					
					$this->infoMessage ="INFORMATION:: Categories (".$rootCategory.") data has been filtered successfully.";
					$commonModel->saveLogger($this->moduleName, "Information", $this->fileName, $this->infoMessage);
					$commonModel->sendNotificationMail($this->to, 'Categories data has been filtered successfully.', $this->infoMessage);
					echo 1;
				} else {
					$this->infoMessage ="INFORMATION::No Category Selected TO Filter.";
					$commonModel->saveLogger($this->moduleName, "Information", $this->fileName, $this->infoMessage); 
					echo 0;
				}
			}catch(Exception $e){
				$commonModel->saveLogger($this->moduleName, "Exception", $this->fileName, $e->getMessage());	
			}
	}
	
	public function getMLCategoryRecursive($rootId,$storeId = '0'){
			
			try{
				 if(isset($rootId) && trim($rootId)!=''){
				 	$commonModel = Mage::getModel('items/common');
					$write = Mage::getSingleton('core/resource')->getConnection('core_write');
					$sql_meli_cate_filter = '';
					//get the full path for meli category
					$meli_cat_path = ''; $meli_cat_full_path = array();
					$meliCatPathVal = $this->getMLCategoryFullPath($rootId, $meli_cat_full_path);
					$session = Mage::getSingleton('core/session', array('name' => 'frontend'));
					$meliCatPathVal = $session->getMLCatFullPath();
					
					if(isset($meliCatPathVal) && $meliCatPathVal !=''){
						$sql_meli_cate_filter = "insert into  mercadolibre_categories_filter (meli_category_id, meli_category_name, meli_category_path, site_id, has_attributes, root_id, listing_allowed, buying_allowed, store_id) select meli_category_id, meli_category_name, '".$meliCatPathVal."', site_id, has_attributes, root_id, listing_allowed, buying_allowed , $storeId from mercadolibre_categories where meli_category_id ='".$rootId."'";
						$write->query($sql_meli_cate_filter);
						$melicategories = Mage::getModel('items/melicategories')->getCollection()->addFieldToFilter('root_id',$rootId);
						$dataMLcat = $melicategories->getData();
						if(count($dataMLcat) > 0){
							for($i=0; $i<count($dataMLcat); $i++){
								 $this->getMLCategoryRecursive($dataMLcat[$i]['meli_category_id'],$storeId);
							}
						} 
					}
				}
			}catch(Exception $e){
				$commonModel->saveLogger($this->moduleName, "Exception", $this->fileName, $e->getMessage());	
			}
	}

	public function getMLCategoryFullPath($rootId, $meli_cat_full_path)
	{
		try{
			if($rootId!=''){
				$commonModel = Mage::getModel('items/common');
				$write = Mage::getSingleton('core/resource')->getConnection('core_read');
				$melCatData = array(); $sql_meli_cate_filter = '';
				$sql_meli_cate_filter = "select root_id, meli_category_name from  mercadolibre_categories where meli_category_id ='".$rootId."'"; 
				$melCatData = $write->fetchAll($sql_meli_cate_filter);
				
				$categoryID = $melCatData['0']['root_id'];
				$categoryName = $melCatData['0']['meli_category_name'];
				if(empty($categoryID)){
					$meli_cat_full_path[] =  $categoryName;
					$meli_cat_full_path = array_reverse($meli_cat_full_path);
					$meliCatPathVal = implode(" > ", $meli_cat_full_path);
					Mage::getSingleton('core/session')->setMLCatFullPath($meliCatPathVal);
				}else{
					$meli_cat_full_path[] =  $categoryName;
					$this->getMLCategoryFullPath($categoryID, $meli_cat_full_path);
				}
			}
		} catch(Exception $e){
			$commonModel->saveLogger($this->moduleName, "Exception", $this->fileName, $e->getMessage());	
		}
	}
		
	public function getMLXContentCreated($service_url)
	{
		try{
			$commonModel = Mage::getModel('items/common');
			$data = $commonModel ->connect1($service_url);
			$dataArr =  explode('X-Content-Created:',$data);
			$ebay_date = substr(trim($dataArr['1']),0,26);
			return Mage::helper('items')->getMLebayDateToDateTime($ebay_date);
		} catch(Exception $e){
			$commonModel->saveLogger($this->moduleName, "Exception", $this->fileName, $e->getMessage());	
		}
	}
	
	
	
}