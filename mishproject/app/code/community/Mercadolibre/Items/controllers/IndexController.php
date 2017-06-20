<?php
ini_set('max_execution_time',0);
ini_set('memory_limit','1024M');
ini_set('max_input_time',0);
date_default_timezone_set('Asia/Kolkata');


class Mercadolibre_Items_IndexController extends Mage_Core_Controller_Front_Action
{
	
	private $moduleName = "Items";
	private $fileName = "IndexController.php";
	
	//message variable
	private $infoMessage = "";
	private $errorMessage = "";
	private $successMessage = "";
	private $to ='';
	
	public function indexAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}
	
	public function getUpdateCategoriesTableAction(){
		/** Get meli_category_id from meli_categories table */
		//Initilize logger model
		$commonModel = Mage::getModel('items/common');
		$melicategories = Mage::getModel('items/melicategories')->getCollection()->addFieldToFilter('has_attributes','0');
		//$melicategories->getSelect()->limit(100);
		$dataMLcat = $melicategories->getData();	
		$dir = $commonModel->getChangedSlash(Mage::getBaseDir('code').DS.'community\Mercadolibre\dump\category-attributes');
		foreach($dataMLcat as $row){
			$meli_category_id = '';
			$meli_category_id = $row['meli_category_id'];
			$category_id = $row['category_id'];
			$fileNameAttr = $meli_category_id;
			$dataFile = $dir . DS . $fileNameAttr . '.json';
			if(file_exists($dataFile))
				{
					$melicateUpdate = Mage::getModel('items/melicategories');
					$melicateUpdate->setCategoryId($category_id);
					$melicateUpdate->setHasAttributes('1');
					$melicateUpdate->save();
				}
		}
		echo "---->ok done";
		exit;
		
		/****************************For Filtered data----------------------*/
		/** Get meli_category_id from meli_categories table */
		//Initilize logger model
		$commonModel = Mage::getModel('items/common');
		$melicategories = Mage::getModel('items/melicategoriesfilter')->getCollection()->addFieldToFilter('has_attributes','0');
		//$melicategories->getSelect()->limit(100);
		$dataMLcat = $melicategories->getData();	
		$dir = $commonModel->getChangedSlash(Mage::getBaseDir('code').DS.'community\Mercadolibre\dump\category-attributes');
		foreach($dataMLcat as $row){
			$meli_category_id = '';
			$meli_category_id = $row['meli_category_id'];
			$category_id = $row['category_id'];
			$fileNameAttr = $meli_category_id;
			$dataFile = $dir . DS . $fileNameAttr . '.json';
			if(file_exists($dataFile))
				{
					$melicateUpdate = Mage::getModel('items/melicategoriesfilter');
					$melicateUpdate->setCategoryId($category_id);
					$melicateUpdate->setHasAttributes('1');
					$melicateUpdate->save();
				}
		}
		echo "---->ok done";
		exit;
	}

    public function getMLCatergoriesAllDataAction()
    {
		$melicategoriesModel = Mage::getModel('items/melicategories');
		$melicategoriesModel -> getMLCatergoriesAllData();
	}
	
	public function getMLCategoryHasAttributesAction()
	{
		$melicategoriesModel = Mage::getModel('items/melicategories');
		$melicategoriesModel -> getMLCategoryHasAttributes();
	} 
	public function getMLFilteredCategoryHasAttributesAction()
	{
		$melicategoriesModel = Mage::getModel('items/melicategories');
		$melicategoriesModel -> getMLFilteredCategoryHasAttributes();
	} 
	
	public function getMLCategoryAttributesAction()
	{
		$melicategoriesModel = Mage::getModel('items/melicategories');
		$melicategoriesModel -> getMLCategoryAttributes();
	}
	
	public function getMLFilteredCategoryAttributesAction()
	{
		$melicategoriesModel = Mage::getModel('items/melicategories');
		$melicategoriesModel -> getMLFilteredCategoryAttributes();
	}
	
	public function getCleanUpLogAction(){	
			$commonModel = Mage::getModel('items/common');
			$commonModel->getCleanUpLog();
	}
	
	public function getMLCatergoriesWithFilterAction(){
			$melicategoriesModel = Mage::getModel('items/melicategories');
			$melicategoriesModel -> getMLCatergoriesWithFilter();
	}

	public function getMLRefreshTokenAction(){
			
			$CommonModel = Mage::getModel('items/common');
			$CommonModel -> getMLRefreshToken();
	}
	
	
	
	public function notificationsAction(){
			  try{ 	
					
					$commonModel = Mage::getModel('items/common');
					$mercadolibre_notifications = $commonModel->forwardSlashToBackSlash(Mage::getBaseDir('log').DS.'mercadolibre_notifications.log');
					if(Mage::helper('items')->getMlAccessToken()){
						$access_token = Mage::helper('items')->getMlAccessToken();
					} else {
						$this->errorMessage = "Error :: Access Token Not Found OR Invalid";
						$commonModel->saveLogger($this->moduleName, "Exception", $this->fileName, $this->errorMessage);
						$commonModel->sendNotificationMail($this->to, 'Notifications Error', $this->errorMessage);
					}
					
					/* Get Base URL Id */
					if(Mage::getStoreConfig("mlitems/mltokenaccess/mlapiurl",Mage::app()->getStore())){
						 $api_url = Mage::getStoreConfig("mlitems/mltokenaccess/mlapiurl",Mage::app()->getStore());
					} else {
						$this->errorMessage = "Error :: Api Url Not Found OR Invalid";
						$commonModel->saveLogger($this->moduleName, "Exception", $this->fileName, $this->errorMessage);
						$commonModel->sendNotificationMail($this->to, 'Notifications Error', $this->errorMessage);
					}	
					if(error_log("Post Called::", 3, $mercadolibre_notifications)==TRUE)
					{
					   //echo 'Message logged<BR>';
					}

				 if ($_SERVER['REQUEST_METHOD'] === 'POST') {
					$input = file_get_contents('php://input');
				    //$input = '{"user_id": 107245974,"resource": "/questions/2707043134","topic": "questions","received": "2011-10-19T16:38:34.425Z","sent" : "2011-10-19T16:40:34.425Z"}';
					//$input = '{"user_id": 107245974,"resource": "/items/MLA469462986","topic": "items","received": "2011-10-19T16:38:34.425Z","sent" : "2011-10-19T16:40:34.425Z"}';
					//$input = '{"user_id": 107245974,"resource": "/orders/774020308","topic": "orders","received": "2011-10-19T16:38:34.425Z", "sent" : "2011-10-19T16:40:34.425Z"}';

					// jsonObj is empty, not working
					$jsonObj = json_decode($input, true);
					$user_id = $jsonObj['user_id'];
					$resource = $jsonObj['resource'];
					$topic   = $jsonObj['topic'];
					$received = $jsonObj['received'];
					$sent    = $jsonObj['sent'];

					$currentDateTime = date('m/d/Y h:i:s a', time());
					error_log($currentDateTime.' :: ', 3, $mercadolibre_notifications);
					error_log($jsonObj['user_id'], 3, $mercadolibre_notifications);
					error_log("::", 3, $mercadolibre_notifications);
					error_log($jsonObj['resource'], 3, $mercadolibre_notifications);
					error_log("::", 3, $mercadolibre_notifications);
					error_log($jsonObj['topic'], 3, $mercadolibre_notifications);
					error_log("\n", 3, $mercadolibre_notifications);
					/* Magento database connection */
					$write = Mage::getSingleton('core/resource')->getConnection('core_write');
					if(!$write)
					{
					   error_log("Failed to connect to DB", 3, $mercadolibre_notifications);
					}
					else
					{
					   error_log("logged to DB\n", 3, $mercadolibre_notifications);
					   $sql = "INSERT INTO mercadolibre_notifications(user_id,resource,topic,received,sent)
							  VALUES ('";
					   $sql = $sql.$user_id."','";
					   $sql = $sql.$resource."','".$topic."','".$received."','".$sent."');";
					   /* Insert data in to  mercadolibre_notifications table*/
					   $write->query($sql);
						/*  Get & save all questions json data into DB table meli_questions */
						switch($topic){
						case "questions":
							$service_url = $api_url.$resource.'?access_token='.$access_token;
							/* Get Call */      
							$jsonDataResp = $commonModel ->meliConnect($service_url);
	
							if(isset($jsonDataResp['statusCode']) && $jsonDataResp['statusCode'] == '200'){
								$meliquestions = Mage::getModel('items/meliquestions');
								$dataArry = array(                            
												'question_id'=> trim($jsonDataResp['json']['id']),    
												'question'=>$commonModel ->inputData($jsonDataResp['json']['text']),
												'itemid'=>$jsonDataResp['json']['item_id'],
												'question_date'=>$jsonDataResp['json']['date_created'],
												'answer_date'=>$jsonDataResp['json']['date_created'],    
												'created_at'=>$jsonDataResp['json']['date_created'], 
												'status' => $jsonDataResp['json']['status']    
												 ); 
								if(isset($jsonDataResp['json']['answer']['buyer']) && trim($jsonDataResp['json']['answer']['buyer'])!=''){
									$dataArry['buyer'] = $jsonDataResp['json']['answer']['buyer'];
								}
								if(trim($jsonDataResp['json']['answer']['text'])!=''){
									$dataArry['answer'] = $commonModel->inputData($jsonDataResp['json']['answer']['text']); 
							   }	
								/* Check for exist question */
								$collectionVar = Mage::getModel('items/meliquestions')->getCollection()-> addFieldToFilter('question_id',trim($jsonDataResp['json']['id']));
								$questionArr  = $collectionVar->getData();
								if(isset($questionArr['0']['id']) && count($questionArr) > 0){
								   $dataArry['id'] = $questionArr['0']['id'];                           
								}   
								$meliquestions->setData($dataArry);                 
								$meliquestions->save();
								
								$this->successMessage = "Message::".$resource." has been saved successfully.";
								$commonModel->saveLogger($this->moduleName, "Exception", $this->fileName, $this->successMessage);
								$commonModel->sendNotificationMail($this->to, 'Notifications Error', $this->successMessage); 
							  }
							  break;
						case "items":
								$service_url = $api_url.$resource.'?access_token='.$access_token;   
								/* Get Call */    
								$response = $commonModel ->meliConnect($service_url);
								if(isset($response['statusCode']) && $response['statusCode'] == '200'){
									/* Get autoincrement Id 'item_id' form mercadolibre_item table */
									$ItemListingCollection  =  Mage::getModel('items/mercadolibreitem')
															-> getCollection()
															-> addFieldToFilter('meli_item_id',trim($response['json']['id']));								
									$itemIdArr = $ItemListingCollection->getData();
									if(isset($itemIdArr['0']['item_id']) && count($itemIdArr) > 0){
										$itemId =  $itemIdArr['0']['item_id'];
										/* Load Model mercadolibreitem  to change status */
										$ModelItemListing  = Mage::getModel('items/mercadolibreitem')->load($itemId);
										$ModelItemListing->setStatus($response['json']['status']); 
										$ModelItemListing->save();
									}
								}
							 break;
						case "orders":
								/* Service Url For get All orders data */
								$service_url = $api_url.$resource.'?access_token='.$access_token;
								/* Get Call */      
								$response = $commonModel ->meliConnect($service_url);
								if(isset($response['statusCode']) && $response['statusCode'] == '200'){
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
													'payment_status' =>$payment_status,
													'shipping_status' => $shipping_status,
													'tags' => serialize($response['json']['tags'])
												);
									$meliOrderModel->setData($data);
									$meliOrderModel->save();
								}
							  break;
						default:
							  break;
							}
					   // set http response code to 200
					   header(' ',true,200);
					}
				}
			}catch(Exception $e){
				$commonModel->saveLogger($this->moduleName, "Exception", $this->fileName, $e->getMessage());
				$commonModel->sendNotificationMail($this->to, 'Exception::Notifications Action', $e->getMessage());
			}
		}
		
	 public function exportCVSFromAttributeTempContainerAction(){
        try{                
            //Initilize logger model
	   	    $commonModel = Mage::getModel('items/common');
            $fileName   = 'temp_attribute.csv';	
            $readConnection = Mage::getSingleton('core/resource')->getConnection('core_read');
            $query="SELECT * FROM mercadolibre_category_attributes_temp";
            $results = $readConnection->fetchAll($query);
            //echo sprintf('<pre>%s</pre>', print_r($results, true));exit;
            /* put temp attribute data into attribute.csv*/
            $dir = Mage::getBaseDir('code').DS.'local\Mercadolibre\dump\category-attributes';            
            $fp = fopen($dir . DS.$fileName, 'w');
            
            if(count($results) > 0){
                foreach($results as $row)
                {                       
                    $dataList[] = array($row['attribute_id'],$row['category_id'], $row['meli_attribute_id'], $row['meli_attribute_name'],$row['meli_attribute_type'],$row['required']);                        
                }
            } 
            //echo sprintf('<pre>%s</pre>', print_r($dataList, true));exit;
            foreach ($dataList as $fields) {
                fputcsv($fp, $fields);
            }
            fclose($fp);  
            try{				

                    /** TRUNCATE TABLE `meli_categories` to add new data again */
                    $write = Mage::getSingleton('core/resource')->getConnection('core_write');
                    $write->query("TRUNCATE TABLE `mercadolibre_category_attributes`");

                    /* import category data into meli_categories*/
                    $db = Mage::getSingleton('core/resource')->getConnection('core_write');
                    $filepath = implode('/',explode('\\',Mage::getBaseDir('code').DS.'local\Mercadolibre\dump\category-attributes\\'.$fileName));
                    $sql = "LOAD DATA LOCAL INFILE '".$filepath."' INTO TABLE `mercadolibre_category_attributes` FIELDS TERMINATED BY ',' enclosed by '\"' lines terminated by '\n'";
                    $db->query($sql);

            } catch(PDOException $e){
                    $this->errorMessage = $e->getTrace()."::".$e->getMessage();
                    $commonModel->saveLogger($this->moduleName, "PDOException", $this->fileName, $this->errorMessage);
            }
            
        }catch(Exception $e){
            $commonModel->saveLogger($this->moduleName, "Exception", $this->fileName, $e->getMessage());	
	}
    }
    public function exportCVSFromAttributeValueTempContainerAction(){
        try{                
            //Initilize logger model
	    $commonModel = Mage::getModel('items/common');
            $fileName   = 'temp_attribute_value.csv';	
            $readConnection = Mage::getSingleton('core/resource')->getConnection('core_read');
            $query="SELECT * FROM  mercadolibre_category_attribute_values_temp";
            $results = $readConnection->fetchAll($query);
            //echo sprintf('<pre>%s</pre>', print_r($results, true));exit;
            /* put temp attribute data into attribute.csv*/
            $dir = Mage::getBaseDir('code').DS.'local\Mercadolibre\dump\category-attributes';            
            $fp = fopen($dir . DS.$fileName, 'w');
            
            if(count($results) > 0){
                foreach($results as $row)
                {                       
                    $dataList[] = array($row['value_id'],$row['attribute_id'], $row['meli_value_id'], $row['meli_value_name'],$row['meli_value_name_extended']);                        
                }
            } 
            //echo sprintf('<pre>%s</pre>', print_r($dataList, true));exit;
            foreach ($dataList as $fields) {
                fputcsv($fp, $fields);
            }
            fclose($fp);  
            try{				
                    /** TRUNCATE TABLE `meli_categories` to add new data again */
                    $write = Mage::getSingleton('core/resource')->getConnection('core_write');
                    $write->query("TRUNCATE TABLE `mercadolibre_category_attribute_values`");
                    /* import category data into meli_categories*/
                    $db = Mage::getSingleton('core/resource')->getConnection('core_write');
                    $filepath = implode('/',explode('\\',Mage::getBaseDir('code').DS.'local\Mercadolibre\dump\category-attributes\\'.$fileName));
                    $sql = "LOAD DATA LOCAL INFILE '".$filepath."' INTO TABLE `mercadolibre_category_attribute_values` FIELDS TERMINATED BY ',' enclosed by '\"' lines terminated by '\n'";
                    $db->query($sql);

            } catch(PDOException $e){
                    $this->errorMessage = $e->getTrace()."::".$e->getMessage();
                    $commonModel->saveLogger($this->moduleName, "PDOException", $this->fileName, $this->errorMessage);
            }
            
        }catch(Exception $e){
            $commonModel->saveLogger($this->moduleName, "Exception", $this->fileName, $e->getMessage());	
	}
    }
		
			
	public function getMLInventoryPriceUpdateAction(){
		 Mage::getModel('items/common')->getMLInventoryPriceUpdate();
	} 
	
	public function checkMLAppkeyAction()
    {
			try{
				$commonModel = Mage::getModel('items/common');
				$clientID = Mage::app()->getRequest()->getParam('clientID');
				$clientSecret = Mage::app()->getRequest()->getParam('clientSecret');
				/* Get Base URL Id */
				if(Mage::getStoreConfig("mlitems/mltokenaccess/mlapiurl",Mage::app()->getStore())){
					 $api_url = Mage::getStoreConfig("mlitems/mltokenaccess/mlapiurl",Mage::app()->getStore());
				} else {
					$this->errorMessage = "Error :: Api Url Not Found OR Invalid";
					$commonModel->saveLogger($this->moduleName, "Exception", $this->fileNamegetMLRefreshToken, $this->errorMessage);
					$commonModel->sendNotificationMail($this->to, 'checkMLAppkey Error', $this->errorMessage);
				}
				$service_url = $api_url."/oauth/token";
				$param = array( 
								'grant_type'=>'client_credentials',
								'client_id'=>trim($clientID),
								'client_secret'=>trim($clientSecret),
								);
			
				$result = $commonModel -> meliConnect($service_url,"POST",$param);
				
				if(isset($result['json']['access_token'])){
					$result = array('value' => $result['json']['access_token'], 'expires' => time() + $result['json']['expires_in'], 'scope' => $result['json']['scope'], 'refresh_token' => $result['json']['refresh_token']);	
			
					$write = Mage::getSingleton('core/resource')->getConnection('core_write');
					$service_url_user = $api_url."/users/me?access_token=".$result['value'];	
					$resultUser = $commonModel -> meliConnect($service_url_user,"GET");
					$storeID = Mage::app()->getRequest()->getParam('store');
					$token_exist = $write->fetchCol("SELECT id from mercadolibre_token_details WHERE store_id = '".$storeID."'");
					//insert or update the new generated access token
					if(count($token_exist)>0){
						$write->query("UPDATE mercadolibre_token_details set access_token = '".$result['value']."', token_expires ='".$result['expires']."', seller_id = '".$resultUser['json']['id']."', seller_nickname = '".$resultUser['json']['nickname']."' WHERE store_id = '".$storeID."'");
					}else{
						$write->query("INSERT INTO mercadolibre_token_details (seller_id, seller_nickname, access_token, token_expires, store_id) values ('".$resultUser['json']['id']."', '".$resultUser['json']['nickname']."', '".$result['value']."', '".$result['expires']."', '".$storeID."')");
					}
					$successMess = 1;
				}else{
					$successMess = 0;
				}
			   Mage::app()->getResponse()->setBody($successMess);
			  }catch(Exception $e){
				$commonModel->saveLogger($this->moduleName, "Exception", $this->fileName, $e->getMessage());	
		}
	}
	
	public function getMLSubCatAjaxAction(){
		  //save the new categories into core_config_data
		  $catIds = Mage::app()->getRequest()->getParam('catids'); 
		  $storeId =  Mage::app()->getRequest()->getParam('storeId'); 

		  $configObj = new Mage_Core_Model_Config();
		  if(!$storeId){
		 	 $configObj ->saveConfig('mlitems/categoriesupdateinformation/mlrootcategories', $catIds, 'default', 0);
		  } else {
		  	 $configObj ->saveConfig('mlitems/categoriesupdateinformation/mlrootcategories', $catIds, 'stores', $storeId);
		  	
		  }
		  //get all subcategories now
		  $succMess =  Mage::getModel('items/melicategories')-> getMLCatergoriesWithFilter($catIds,$storeId);
		  return $succMess;
	} 
	public function getVariationAjaxAction(){
			$pId = Mage::app()->getRequest()->getParam('Pid');
			$mage_category_id = Mage::app()->getRequest()->getParam('MageCatId');
			###################################################################################################
			$store = Mage::helper('items')->_getStore();
			$storeId = 	$store->getId();
			$collection = Mage::getModel('catalog/product')->getCollection()
						->addAttributeToSelect('sku')
						->addAttributeToSelect('name')
						->addAttributeToSelect('attribute_set_id')
						->addAttributeToSelect('type_id')
						->setStoreId($store->getId())
						-> addAttributeToFilter('entity_id',$pId);
						
						
			$collection -> joinField('category_id','catalog/category_product','category_id','product_id=entity_id',null,'inner');
			/* join to check category mapping */	
			$collection->joinTable('items/melicategoriesmapping','mage_category_id=category_id',array('meli_category_id','store_id'),"meli_category_id != '' AND meli_category_id != 'NO_MAPPING' AND mercadolibre_categories_mapping.store_id = '".$store->getId()."'");
			/* Get Meli Category Name */	
			$collection -> getSelect()
						-> joinleft(array('melicat'=>'mercadolibre_categories'), 'mercadolibre_categories_mapping.meli_category_id = melicat.meli_category_id',array('melicat.meli_category_name','melicat.category_id as mc_category_id','melicat.has_attributes as has_attributes'));
			/* Get Saved Listing data from Mercadolibre_item */
			$collection -> getSelect()
						-> joinleft(array('mlitem'=>'mercadolibre_item'), "e.entity_id = mlitem.product_id and mlitem.store_id = '".$storeId."'", array("if(mlitem.meli_item_id !='',mlitem.meli_category_id,melicat.meli_category_id) as meli_category_id",'mlitem.title as meli_product_name','mlitem.price as meli_price','mlitem.available_quantity as meli_qty','mlitem.descriptions as meli_descriptions','mlitem.pictures as meli_images', 'mlitem.item_id as item_id','mlitem.sent_to_publish','mlitem.main_image'));

							
			if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
				$collection->joinField('qty',
					'cataloginventory/stock_item',
					'qty',
					'product_id=entity_id',
					'{{table}}.stock_id=1',
					'left');
			}
			$collection->addAttributeToSelect('price');
			$collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
			$collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner')
						-> addAttributeToFilter('visibility', array('neq' => 1))
						-> addFieldToFilter('category_id',$mage_category_id);
			
			foreach($collection as $row){
				$meli_category_id = $row->getData('meli_category_id');
				$meliCatCollection = Mage::getModel('items/melicategories')
								   -> getCollection()
								   -> addFieldToFilter('meli_category_id',$meli_category_id)
								   -> addFieldToSelect('has_attributes')
								   ->getData();

				$has_attributes = $meliCatCollection['0']['has_attributes'];
				
				######################################################
				//check for the condition for color and size 
				$mageSizeAttrIdsArr = array();
				if(Mage::getStoreConfig("mlitems/globalattributesmapping/magesizeattr",Mage::app()->getStore($storeId))){
				$mageSizeAttrIds = Mage::getStoreConfig("mlitems/globalattributesmapping/magesizeattr",Mage::app()->getStore($storeId));
				$mageSizeAttrIdsArr = explode(",", $mageSizeAttrIds);
				}
				$mageColorAttrIdsArr = array();
				if(Mage::getStoreConfig("mlitems/globalattributesmapping/magecolorattr",Mage::app()->getStore($storeId))){
				$mageColorAttrIds = Mage::getStoreConfig("mlitems/globalattributesmapping/magecolorattr",Mage::app()->getStore($storeId));
				$mageColorAttrIdsArr = explode(",", $mageColorAttrIds);
				}
				$mageAttrIdsArr = array();
				if(count($mageSizeAttrIdsArr) > 0 && count($mageColorAttrIdsArr) > 0){
				$mageAttrIdsArr = array_merge($mageSizeAttrIdsArr,$mageColorAttrIdsArr);
				} else if(count($mageSizeAttrIdsArr) > 0 && count($mageColorAttrIdsArr) == 0) {
				$mageAttrIdsArr = $mageSizeAttrIdsArr;
				}  else if(count($mageSizeAttrIdsArr) == 0 && count($mageColorAttrIdsArr) > 0) {
				$mageAttrIdsArr = $mageColorAttrIdsArr;
				}
				//ends
				if($has_attributes){
					$_product = $row->load($row->getData('entity_id'));						
					/* Get simple product used by configurable*/
					if($_product->getData('type_id') == 'configurable'){
					$associated_prods = $_product->getTypeInstance()->getUsedProducts();
					$html = '';
					$headerCount = 0;
					if(count($associated_prods) > 0){	
					foreach ($associated_prods as $assoc_product) {
						$assoc_products[] = $assoc_product;
						$assoc_productIds[] =  $assoc_product->getId();
						$priceAsso = '';
						$priceAsso =  $assoc_product->getPrice();
						$qtyAsso ='';
						$qtyAsso = $assoc_product->getData('stock_item')->getData('qty');
					
						$productAssoId ='';		
						$productAssoId = $assoc_product->getId(); 	
						
							
						$_productAsso = Mage::getModel('catalog/product')->load($productAssoId);
						$attSet = $_productAsso-> getAttributeSetId();
						$attributes = Mage::getResourceModel('eav/entity_attribute_collection')
									 ->setEntityTypeFilter($_productAsso->getResource()->getTypeId())	
									 ->setAttributeSetFilter($attSet)
									//->addFieldToFilter('attribute_code',array('shoe_size','color'));
									->addFieldToFilter('additional_table.attribute_id',$mageAttrIdsArr);
						$attributes->getSelect()->order( array('attribute_code Desc') );
					
						$attributesArr = $attributes->getData();
						$html .= '<table  border="0" cellspacing="0" cellpadding="0" ><tr>';
						$html .= '<input type="hidden" value="'.$assoc_product->getId().'" name="configurable_id_'.$row->getData('entity_id').'[]">';	
						$headerCount  ++;	

						if(count($attributesArr) > 0){
							foreach($attributesArr as $rowAttribute){	
								$attribute_id ='';
								$attribute_code = '';
								$attribute_id = $rowAttribute['attribute_id'];
								if(in_array($attribute_id, $mageColorAttrIdsArr )){
									$attribute_code = 'color';
								}
								if(in_array($attribute_id, $mageSizeAttrIdsArr )){
									$attribute_code = 'size';
								}
								$option_id = '';
								$option_id = $_productAsso->getData($rowAttribute['attribute_code']);
								
								$collectionOption	= Mage::getModel('eav/entity_attribute_option')
													->getCollection()->setStoreFilter($storeId)
													->join('attribute','attribute.attribute_id=main_table.attribute_id', 'attribute_code')
													->addFieldToFilter('attribute.attribute_id',$attribute_id)
													->addFieldToFilter('main_table.option_id',$option_id);
								$collectionOption -> getSelect()
				 								  -> joinleft(array('mavm'=>'mercadolibre_attribute_value_mapping'), " main_table.option_id = mavm.mage_attribute_option_id AND mavm.store_id = '".$storeId."'",array('mavm.*'));	

								$optionArr = array();
								$attributeOptionText = '';
								$mage_attribute_option_id = 0;
								$optionArr = $collectionOption->getData();
								if(count($optionArr) > 0){	
									$attributeOptionText = trim($optionArr['0']['value']);
									$mage_attribute_option_id = (int) $optionArr['0']['mage_attribute_option_id'];
								}	
								
								if($attributeOptionText){
									$html .= '<td style=" width:100px; border:none !important;"><table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-bottom:5px; width:80px; border:none !important;">';
									$html .= '<tr><td style="border:none !important;" nowrap="nowrap"><strong>'.ucfirst($attribute_code).'</strong></td><td style="border:none !important; " nowrap="nowrap"><strong>Meli '.ucfirst($attribute_code).'</strong></td></tr>';
									$html .= '<tr>';
									$html .= '<td style="border:none !important; "><div style="margin:2px; padding:2px;">';
									$html .= '<input id="configurable_attributeid_'.$productAssoId.'[]"  type="hidden" name="configurable_attribute_'.$productAssoId.'[]" value="'.$rowAttribute['attribute_code'].'">
									<input id="configurable_attribute_value_'.$productAssoId.'[]"  type="hidden" name="configurable_attribute_value_'.$productAssoId.'[]" value="'.$mage_attribute_option_id.'#Option_Id#'.$attributeOptionText.'">'.$attributeOptionText.'</div><div style="margin:2px; padding:2px;">&nbsp;</div></td>';
											 
									$html .='<td style="vertical-align:middle; border:none !important;">';
									/*######################## Get Meli_Attribute_Value_Mapping Data ##################################################*/
									$meliattributevaluemappings  = Mage::getModel('items/meliattributevaluemapping')->getCollection()
																->addFieldToFilter('main_table.store_id',$storeId)
																->addFieldToFilter('main_table.category_id',$meli_category_id)
																->addFieldToFilter('main_table.mage_attribute_original',$rowAttribute['attribute_code'])
																->addFieldToFilter('main_table.mage_attribute_option_id',trim($mage_attribute_option_id));
									$meliattributevaluemappings -> getSelect()
																-> joinleft(array('mca'=>'mercadolibre_category_attributes'), "main_table.meli_attribute_id  = mca.meli_attribute_id and mca.category_id = main_table.category_id",array('mca.meli_attribute_name'))
																-> joinleft(array('mcav'=>'mercadolibre_category_attribute_values'), "main_table.meli_value_id  = mcav.meli_value_id and mcav.meli_category_id = main_table.category_id ",array('mcav.meli_value_name','mcav.meli_value_name_extended'));

								
					
									/*##########################################################################*/
									/*--------------------------------------------------------------*/
									
									$ModelItemAttribute  =  Mage::getModel('items/meliitemattributes')
														-> getCollection()
														-> addFieldToFilter('main_table.store_id',$storeId)
														-> addFieldToFilter('main_table.item_id',$productAssoId);
									$ModelItemAttribute -> getSelect()
														-> joinleft(array('melipq'=>'mercadolibre_item_price_qty'), " main_table.item_id = melipq.item_id and melipq.store_id = '".$storeId."' ",array('melipq.meli_price','melipq.meli_qty'));;
									$melipqArr = $ModelItemAttribute->getData();
																
									$selMeliValueIds = array();
									if(count($ModelItemAttribute->getData()) > 0){
										foreach($ModelItemAttribute->getData() as $selValAtr){
											if($selValAtr['meli_value_id']!=''){
												$selMeliValueIds[] = $selValAtr['meli_value_id'];
											}
										}
									}			
					
									$melicategoryattributes = Mage::getModel('items/melicategoryattributes')->getCollection()
															->addFieldToFilter('category_id',$meli_category_id)
															->addFieldToFilter('meli_attribute_type',$attribute_code);
					
									if(count($melicategoryattributes->getData()) > 0){
										/*foreach($melicategoryattributes->getData() as $rowAtr){	
											$html .= '<input id="configurable_meli_attribute_id_'.$productAssoId.'_'.$rowAttribute['attribute_code'].'_'.$mage_attribute_option_id.'[]"  type="hidden" name="configurable_meli_attribute_id_'.$productAssoId.'_'.$rowAttribute['attribute_code'].'_'.$mage_attribute_option_id.'[]" value="'.$rowAtr['meli_attribute_id'].'">';
					
										}*/
									}

									if(count($meliattributevaluemappings->getData()) > 0 ){
											$secColor=''; $firstColor=''; $htmlHidden = '';
											foreach($meliattributevaluemappings->getData() as $meliattributevaluemapping){
												if($meliattributevaluemapping['mage_attribute'] == 'color'){
													if($meliattributevaluemapping['meli_attribute_name']=="Color Secundario"){
														$secColor = $meliattributevaluemapping['meli_value_name_extended'];
														$htmlHidden .= '<input type="hidden" name="configurable_meli_value_id_'.$productAssoId.'_'.$rowAttribute['attribute_code'].'_'.$mage_attribute_option_id.'[]" value="'.$meliattributevaluemapping['meli_value_id'].'">';
														
														$html .= '<input id="configurable_meli_attribute_id_'.$productAssoId.'_'.$rowAttribute['attribute_code'].'_'.$mage_attribute_option_id.'[]"  type="hidden" name="configurable_meli_attribute_id_'.$productAssoId.'_'.$rowAttribute['attribute_code'].'_'.$mage_attribute_option_id.'[]" value="'.$meliattributevaluemapping['meli_attribute_id'].'">';
														
													}elseif($meliattributevaluemapping['meli_attribute_name']=='Color Primario'){
														$firstColor = $meliattributevaluemapping['meli_value_name_extended'];
														$htmlHidden .= '<input type="hidden" name="configurable_meli_value_id_'.$productAssoId.'_'.$rowAttribute['attribute_code'].'_'.$mage_attribute_option_id.'[]" value="'.$meliattributevaluemapping['meli_value_id'].'">';
														
														$html .= '<input id="configurable_meli_attribute_id_'.$productAssoId.'_'.$rowAttribute['attribute_code'].'_'.$mage_attribute_option_id.'[]"  type="hidden" name="configurable_meli_attribute_id_'.$productAssoId.'_'.$rowAttribute['attribute_code'].'_'.$mage_attribute_option_id.'[]" value="'.$meliattributevaluemapping['meli_attribute_id'].'">';
														
													}
													
												} else {
													$html .= '<div style="margin:2px; padding:2px;"><input type="hidden" name="configurable_meli_value_id_'.$productAssoId.'_'.$rowAttribute['attribute_code'].'_'.$mage_attribute_option_id.'[]" value="'.$meliattributevaluemapping['meli_value_id'].'">'.$meliattributevaluemapping['meli_value_name'].'</div><div style="margin:2px; padding:2px;">&nbsp;</div>';

													$html .= '<input id="configurable_meli_attribute_id_'.$productAssoId.'_'.$rowAttribute['attribute_code'].'_'.$mage_attribute_option_id.'[]"  type="hidden" name="configurable_meli_attribute_id_'.$productAssoId.'_'.$rowAttribute['attribute_code'].'_'.$mage_attribute_option_id.'[]" value="'.$meliattributevaluemapping['meli_attribute_id'].'">';
													
												}
											}
											 if($secColor!='' && $firstColor!='' ){
													$html .= $htmlHidden.'<div class="variation"><div class="varBox color"><span class="varColor" title="Marrn" style="background-color:'.$secColor.';"></span><div class="maskColor-two"><span class="varColor color-two" title="Naranja" style="background-color:'.$firstColor.';"></span></div></div></div></div>';
												}
									}
								/*-------------------------------------------------------------*/
									$html .='</td></tr></table></td>';		 
								}
							}
						}
						  /* Price */
							$priceVariation ='';
							if(isset($melipqArr['0']['meli_price'])){
									$priceVariation = $melipqArr['0']['meli_price'];
							}else{        	
									$priceVariation = $priceAsso;
							}
							$html .= '<td style=" width:88px; vertical-align:top; border:none !important;" ><div style="float:left; width:88px; margin-bottom:10px;"><strong>Price</strong> :<input id="configurable_attribute_price_'.$productAssoId.'" style="width:40px; padding:5px 0;" class="required-entry validate-zero-or-greater input-text required-entry validation-failed" type="text" name="configurable_attribute_price_'.$productAssoId.'[]" value="'.number_format($priceVariation, 2, '.', '').'" /></div>';
				
							/* Quantity */	
							$pubQtyVal = 0;
							if(isset($melipqArr['0']['meli_qty']) > 0 ){
									$qtyVariation = $melipqArr['0']['meli_qty'];
									$pubQtyVal  = $melipqArr['0']['meli_qty'];
							}else{        	
									$qtyVariation = $qtyAsso;
							}	
							$html .= '<div style="float:left; margin-left:10px; margin-bottom:5px;"><strong>Qty</strong> :<input  style="width:40px; padding:5px 0;" class="required-entry validate-zero-or-greater input-text required-entry validation-failed" maxlength="5" type="text" name="configurable_attribute_qty_'.$productAssoId.'[]" value="' . ceil($qtyVariation).'" /></div></td>';
							
							$html .= '<td style=" width:88px; border:none !important;"><div style="margin-left:10px; margin-bottom:5px;"><strong>Store Product Count</strong><br/> '. ceil($qtyAsso).'</div></td>';
							$html .= '<td style=" width:88px; border:none !important;"><div style="float:left; margin-left:10px; margin-bottom:5px;"><strong>Publish Item Count  </strong><br/> '. ceil($pubQtyVal).'</div></td></tr>';
							
							$html .= '</table>';
						}
					}
					} else {
					$attSet = $_product-> getAttributeSetId();
					$attributes = '';
					$attributes = Mage::getResourceModel('eav/entity_attribute_collection');
					$attributes	->setEntityTypeFilter($_product->getResource()->getTypeId())	
								->setAttributeSetFilter($attSet)
								->addFieldToFilter('additional_table.attribute_id',$mageAttrIdsArr);
					$attributes->getSelect()->order( array('attribute_code Desc') );
					
					
					$attributesArr = $attributes->getData();
					$html = '';
					$html .= '<table  border="0" cellspacing="0" cellpadding="0"   style="border:none !important;"  >';
					$html .= '<tr>';
						if(count($attributesArr) > 0){	
							foreach($attributesArr as $rowAttribute){	
								$attribute_id ='';
								$attribute_id = $rowAttribute['attribute_id'];
								
								if(in_array($attribute_id, $mageColorAttrIdsArr )){
									$attribute_code = 'color';
								}
								if(in_array($attribute_id, $mageSizeAttrIdsArr )){
									$attribute_code = 'size';
								}
								
								$option_id = '';
								$option_id = $_product->getData($rowAttribute['attribute_code']);
								
								$collectionOption	= Mage::getModel('eav/entity_attribute_option')
													->getCollection()->setStoreFilter()
													->join('attribute','attribute.attribute_id=main_table.attribute_id', 'attribute_code')
													->addFieldToFilter('attribute.attribute_id',$attribute_id)
													->addFieldToFilter('main_table.option_id',$option_id);
								$collectionOption -> getSelect()
				 								  -> joinleft(array('mavm'=>'mercadolibre_attribute_value_mapping'), " main_table.option_id = mavm.mage_attribute_option_id AND mavm.store_id = '".$storeId."'",array('mavm.*'));	
								$optionArr = $collectionOption->getData();
						
								$attributeOptionText = '';
								$mage_attribute_option_id = 0;
								if(count($optionArr) > 0){	
									$attributeOptionText = $optionArr['0']['value'];
									$mage_attribute_option_id = trim($optionArr['0']['mage_attribute_option_id']);
								}
								
								
								
								if($attributeOptionText){
									$html .= '<td style="width:100px; border:none !important;" ><table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-bottom:5px; border:none !important;" >';
									$html .= '<tr><td style="border:none !important;" nowrap="nowrap"><strong>'.ucfirst($attribute_code).'</strong></td><td nowrap="nowrap" style="border:none !important;"><strong>Meli '.ucfirst($attribute_code).'</strong></td></tr>';
									$html .= '<tr>';
									$html .= '<td  style="border:none !important;" ><input id="attributeid_'.$row->getData('entity_id').'[]"  type="hidden" name="attribute_'.$row->getData('entity_id').'[]" value="'.$rowAttribute['attribute_code'].'"><input id="attribute_value_'.$row->getData('entity_id').'[]"  type="hidden" name="attribute_value_'.$row->getData('entity_id').'[]" value="'.$mage_attribute_option_id.'">'.$attributeOptionText.'</td>';
											 
									$html .='<td style="border:none !important;">';
								
									/*######################## Get Meli_Attribute_Value_Mapping Data ##################################################*/
									$meliattributevaluemappings =  Mage::getModel('items/meliattributevaluemapping')->getCollection()
																-> addFieldToFilter('main_table.store_id',$storeId)
																-> addFieldToFilter('main_table.category_id',$meli_category_id)
																-> addFieldToFilter('main_table.mage_attribute_original',$rowAttribute['attribute_code'])
																-> addFieldToFilter('main_table.mage_attribute_option_id',$mage_attribute_option_id);
									$meliattributevaluemappings -> getSelect()
																-> joinleft(array('mca'=>'mercadolibre_category_attributes'), "main_table.meli_attribute_id  = mca.meli_attribute_id and mca.category_id = main_table.category_id",array('mca.meli_attribute_name'))
																-> joinleft(array('mcav'=>'mercadolibre_category_attribute_values'), "main_table.meli_value_id  = mcav.meli_value_id and mcav.meli_category_id = main_table.category_id ",array('mcav.meli_value_name','mcav.meli_value_name_extended'));								
																
							
									/*##########################################################################*/
									/*--------------------------------------------------------------*/
						
									$ModelItemAttribute  = Mage::getModel('items/meliitemattributes')
														-> getCollection()
														-> addFieldToFilter('main_table.item_id',$row->getData('entity_id'))
														-> addFieldToFilter('main_table.store_id',$storeId)
														-> addFieldToSelect('meli_value_id');
									
									$selMeliValueIds = array();
									if(count($ModelItemAttribute->getData()) > 0){
										foreach($ModelItemAttribute->getData() as $selValAtr){
											if($selValAtr['meli_value_id']!=''){
												$selMeliValueIds[] = $selValAtr['meli_value_id'];
											}
										}
									}				
						
									$melicategoryattributes = Mage::getModel('items/melicategoryattributes')->getCollection()
															->addFieldToFilter('category_id',$meli_category_id)
															->addFieldToFilter('meli_attribute_type',$attribute_code);
						
									if(count($melicategoryattributes->getData()) > 0){
										/*foreach($melicategoryattributes->getData() as $rowAtr){	
											$html .= '<input id="meli_attribute_id_'.$row->getData('entity_id').'_'.$rowAttribute['attribute_code'].'_'.$mage_attribute_option_id.'[]"  type="hidden" name="meli_attribute_id_'.$row->getData('entity_id').'_'.$rowAttribute['attribute_code'].'_'.$mage_attribute_option_id.'[]" value="'.$rowAtr['meli_attribute_id'].'">';		
										}*/
									}
									if(count($meliattributevaluemappings->getData()) > 0 ){
											$secColor=''; $firstColor=''; $htmlHidden = '';
											foreach($meliattributevaluemappings->getData() as $meliattributevaluemapping){
												if($meliattributevaluemapping['mage_attribute'] == 'color'){
													if($meliattributevaluemapping['meli_attribute_name']=="Color Secundario"){
														$secColor = $meliattributevaluemapping['meli_value_name_extended'];
														$htmlHidden .= '<input type="hidden" name="meli_value_id_'.$row->getData('entity_id').'_'.$rowAttribute['attribute_code'].'_'.$mage_attribute_option_id.'[]" value="'.$meliattributevaluemapping['meli_value_id'].'">';
														
														$html .= '<input id="meli_attribute_id_'.$row->getData('entity_id').'_'.$rowAttribute['attribute_code'].'_'.$mage_attribute_option_id.'[]"  type="hidden" name="meli_attribute_id_'.$row->getData('entity_id').'_'.$rowAttribute['attribute_code'].'_'.$mage_attribute_option_id.'[]" value="'.$meliattributevaluemapping['meli_attribute_id'].'">';	
														
													}elseif($meliattributevaluemapping['meli_attribute_name']=='Color Primario'){
														$firstColor = $meliattributevaluemapping['meli_value_name_extended'];
														$htmlHidden .= '<input type="hidden" name="meli_value_id_'.$row->getData('entity_id').'_'.$rowAttribute['attribute_code'].'_'.$mage_attribute_option_id.'[]" value="'.$meliattributevaluemapping['meli_value_id'].'">';
														
														$html .= '<input id="meli_attribute_id_'.$row->getData('entity_id').'_'.$rowAttribute['attribute_code'].'_'.$mage_attribute_option_id.'[]"  type="hidden" name="meli_attribute_id_'.$row->getData('entity_id').'_'.$rowAttribute['attribute_code'].'_'.$mage_attribute_option_id.'[]" value="'.$meliattributevaluemapping['meli_attribute_id'].'">';	
													}
												} else {
													$html .= '<div style="margin:2px; padding:2px;"><input type="hidden" name="meli_value_id_'.$row->getData('entity_id').'_'.$rowAttribute['attribute_code'].'_'.$mage_attribute_option_id.'[]" value="'.$meliattributevaluemapping['meli_value_id'].'">'.$meliattributevaluemapping['meli_value_name'].'</div><div style="margin:2px; padding:2px;">&nbsp;</div>';
													
													$html .= '<input id="meli_attribute_id_'.$row->getData('entity_id').'_'.$rowAttribute['attribute_code'].'_'.$mage_attribute_option_id.'[]"  type="hidden" name="meli_attribute_id_'.$row->getData('entity_id').'_'.$rowAttribute['attribute_code'].'_'.$mage_attribute_option_id.'[]" value="'.$meliattributevaluemapping['meli_attribute_id'].'">';	
													
												}
											}
											
											if($secColor!='' && $firstColor!='' ){
														$html .= $htmlHidden.'<div class="variation"><div class="varBox color"><span class="varColor" title="Marrn" style="background-color:'.$secColor.';"></span><div class="maskColor-two"><span class="varColor color-two" title="Naranja" style="background-color:'.$firstColor.';"></span></div></div></div></div>';
													}
									}
								/*-------------------------------------------------------------*/
									$html .='</td></tr></table></td>';			 
								}
							}
						}
						$ModelPriceQty   = Mage::getModel('items/meliitempriceqty')
										-> getCollection()
										-> addFieldToFilter('main_table.store_id',$storeId)
										-> addFieldToFilter('main_table.item_id',$row->getData('entity_id'));
						$priceQtyArr = $ModelPriceQty->getData();
				
						/* Price */
						if(isset($priceQtyArr['0']['meli_price'])){
								$priceVariation = $priceQtyArr['0']['meli_price'];
						}else{        	
								$priceVariation = $row->getData('price');
						}
						$html .='<td  style=" vertical-align:top; border:none !important; width:88px;" ><div style="float:left; width:88px; margin-bottom:10px;"><strong>Price</strong> :<input id="attribute_price_'.$row->getData('entity_id').'" style="width:40px; padding:5px 0;" class="required-entry validate-zero-or-greater input-text required-entry validation-failed" type="text" name="attribute_price_'.$row->getData('entity_id').'[]" value="'.number_format($priceVariation, 2, '.', '').'" /></div>';
				
						/* Quantity */	
						if(isset($priceQtyArr['0']['meli_qty'])){
								$qtyVariation = $priceQtyArr['0']['meli_qty'];
						}else{        	
								$qtyVariation = $row->getData('qty');
						}		
						$html .= '<div style="float:left; margin-left:10px; margin-bottom:5px;"><strong>Qty</strong> :<input  style="width:40px; padding:5px 0;" class="required-entry validate-zero-or-greater input-text required-entry validation-failed" maxlength="5" type="text" name="attribute_qty_'.$row->getData('entity_id').'[]" value="' . ceil($qtyVariation).'" /></div></td></tr>';
						
						$html .= '</table>';
						}
					} else {
					$html = 'No';
				}
						
				echo $html;
				########################################################################################################
			}
		}  

	public function getItemImagesAjaxAction(){
				$html = '';
				$checkBoxName = '';
				$ProductThumbImage='';
				$commonModel = Mage::getModel('items/common');
		  		$pId = Mage::app()->getRequest()->getParam('Pid');
				$_product = Mage::getModel('catalog/product')->load($pId);
				$MediaGalleryArr = $_product->getMediaGalleryImages();
				$checkBoxName = "item_image[".$pId."][]";
				//<associated product all images>
				if ($_product->getTypeId() == "configurable") {
					$associated_products = $_product->loadByAttribute('sku', $_product->getSku())->getTypeInstance()->getUsedProducts();
					foreach ($associated_products as $assoc){
						$assocProduct = Mage::getModel('catalog/product')->load($assoc->getId());		
						$i=0; 
						if (count($assocProduct->getMediaGalleryImages()) > 0) {
						   foreach ($assocProduct->getMediaGalleryImages() as $_image)
							{
								$imageFile = str_replace(Mage::getBaseUrl('media'),"",$_image->url);
								$imageId = $assoc->getId() . "_" . $i++;
								$html .= '<div style="width:50px; vertical-align:top; float:left; padding:5px;"><input style="display:none" type="checkbox" name="'.$checkBoxName.'" value="'. $_image['value_id'] . '" checked="checked" >';
								$html .=  '<img  style="vertical-align:middle; padding-left:5px; width:50px;" id="image'.$imageId.'" src="'.Mage::helper('catalog/image')->init($assocProduct, 'thumbnail2', $_image->getFile())->resize(50, 50).'" >';
								$html .=  '</div>';
							 }
						}
 						 /*change made by leandro*/
						else if(file_exists($commonModel->getChangedSlash(Mage::getBaseDir('code').DS.'local\CJM\ColorSelectorPlus\Helper\Data.php')) && class_exists(get_class(Mage::helper('colorselectorplus')))){
							$html='';
							$imageFile = '';
							$colorselectorplus_product_base = Mage::helper('colorselectorplus')->decodeImages($_product);
							if(count($colorselectorplus_product_base)>0 && count($colorselectorplus_product_base['image']) > 0){
								foreach ($colorselectorplus_product_base['image'] as $keyImage => $_colorselectorplus_image){
									$imageFile = $_colorselectorplus_image;
									$value_id = $colorselectorplus_product_base['id'][$keyImage];
									$imageId = $assoc->getId() . "_" . $i++;
									$html .= '<div style="width:50px; vertical-align:top; float:left; padding:5px;"><input style="display:none" type="checkbox" name="'.$checkBoxName.'" value="'. $value_id . '" checked="checked" >';
									$html .= '<img  style="vertical-align:middle; padding-left:5px; width:50px;" id="image'.$imageId.'" src="'. $imageFile  .'" >';
									$html .= '</div>';
								} 
							}
						}
                       /*change made by leandro*/ 
					}
				}elseif($_product->getTypeId() == "simple"){
					
					if(count($MediaGalleryArr) > 0){
						$html .= '<div style="width:160px;">';
						foreach($MediaGalleryArr as $row ){
							if($row -> disabled !=1){
								$html .= '<div style="width:50px; vertical-align:top; float:left; padding:5px;"><input style="display:none;" type="checkbox" name="'.$checkBoxName.'" value="'. $row['value_id'] . '" checked="checked" >';
								$html .= '<img style="vertical-align:middle; padding-left:5px; width:50px;"  src="'.Mage::helper('catalog/image')->init($_product, 'thumbnail2', $row->getFile())->resize(50, 50).'" />';
								$html .= '</div>';
							}
						}
						$html .= '</div>';
					} 
					
				}
				if(trim($html) == ''){
					$html = 'No Image';
				}
				echo $html;
		}
		
		public function getVariationOnPublishAction(){
					$pId = Mage::app()->getRequest()->getParam('Pid');
					$storeId = Mage::helper('items')->_getStore()->getId();	
					$revise = Mage::app()->getRequest()->getParam('Revise');
					/* get Filter CategoryIds*/
					$collection 	=  Mage::getModel('items/mercadolibreitem')->getCollection()
									-> addFieldToFilter('main_table.store_id', $storeId);
					if($revise){
						$collection -> addFieldToFilter('main_table.product_id', $revise);
					} else {
						$collection -> addFieldToFilter('main_table.product_id', $pId);
					}
					$collection -> getSelect()
								-> join(array('mapping'=>'mercadolibre_categories_mapping'), "main_table.category_id = mapping.mage_category_id AND mapping.store_id = '".$storeId."'", array('mapping.meli_category_id','mapping.store_id'))
								-> join(array('melicat'=>'mercadolibre_categories'), 'mapping.meli_category_id = melicat.meli_category_id',array('melicat.meli_category_name','melicat.has_attributes as has_attributes',"if(main_table.meli_item_id !='',main_table.meli_category_id,melicat.meli_category_id) as meli_category_id"));

				//check for the condition for color and size 
					$mageSizeAttrIdsArr = array();
					if(Mage::getStoreConfig("mlitems/globalattributesmapping/magesizeattr",Mage::app()->getStore($storeId))){
						$mageSizeAttrIds = Mage::getStoreConfig("mlitems/globalattributesmapping/magesizeattr",Mage::app()->getStore($storeId));
						$mageSizeAttrIdsArr = explode(",", $mageSizeAttrIds);
					}
					$mageColorAttrIdsArr = array();
					if(Mage::getStoreConfig("mlitems/globalattributesmapping/magecolorattr",Mage::app()->getStore($storeId))){
						$mageColorAttrIds = Mage::getStoreConfig("mlitems/globalattributesmapping/magecolorattr",Mage::app()->getStore($storeId));
						$mageColorAttrIdsArr = explode(",", $mageColorAttrIds);
					}
					$mageAttrIdsArr = array();
					if(count($mageSizeAttrIdsArr) > 0 && count($mageColorAttrIdsArr) > 0){
						$mageAttrIdsArr = array_merge($mageSizeAttrIdsArr,$mageColorAttrIdsArr);
					} else if(count($mageSizeAttrIdsArr) > 0 && count($mageColorAttrIdsArr) == 0) {
						$mageAttrIdsArr = $mageSizeAttrIdsArr;
					}  else if(count($mageSizeAttrIdsArr) == 0 && count($mageColorAttrIdsArr) > 0) {
						$mageAttrIdsArr = $mageColorAttrIdsArr;
					}
					//ends
					$html ='';
					if($revise){
						$revise = (int) $revise;
						$html .='<input type="hidden" name="revise" value="'.$revise.'">';
					}
				foreach($collection as $row){
				
					$meli_category_id = $row->getData('meli_category_id');
					$meliCatCollection = Mage::getModel('items/melicategories')
									   -> getCollection()
									   -> addFieldToFilter('meli_category_id',$row->getData('meli_category_id'))
									   -> addFieldToSelect('has_attributes')
									   ->getData();
					$has_attributes = $meliCatCollection['0']['has_attributes'];
				
				
					if($has_attributes){
						if($row->getData('mage_type_id') == 'configurable'){
							$_product = Mage::getModel('catalog/product')->load($row->getData('product_id'));
							$associated_prods = $_product->getTypeInstance()->getUsedProducts();
							foreach ($associated_prods as $assoc_product) {
							$html .= '<table  style="margin-bottom:5px; border:1px solid #D1E0E2;" border="0" cellspacing="0" cellpadding="0">
										<tr>
											<td style="border:none !important;">';
								$assoc_products[] = $assoc_product;
								$assoc_productIds[] =  $assoc_product->getId();
								$priceAsso = '';
								$priceAsso =  $assoc_product->getPrice();
										
								$productAssoId ='';		
								$productAssoId = $assoc_product->getId(); 	
								
								$qtyAsso ='';
								$qtyAsso = $assoc_product->getData('stock_item')->getData('qty');
								
									
								$_productAsso = Mage::getModel('catalog/product')->load($productAssoId);
								$attSet = $_productAsso-> getAttributeSetId();
								$attributes = Mage::getResourceModel('eav/entity_attribute_collection')
											->setEntityTypeFilter($_productAsso->getResource()->getTypeId())	
											->setAttributeSetFilter($attSet)
											//->addFieldToFilter('attribute_code',array('size','color'));
											->addFieldToFilter('additional_table.attribute_id',$mageAttrIdsArr);
								$attributes->getSelect()->order( array('attribute_code Desc') );
								
			
								$attributesArr = $attributes->getData();
								/*----------------------------Used Product-------------------------------------------------*/
	
							$priceVariation = '';
							$qtyVariation = '';
							$ModelItemAttribute = Mage::getModel('items/meliitemattributes')
												-> getCollection()
												-> addFieldToFilter('main_table.store_id',$storeId)
												-> addFieldToFilter('main_table.item_id',$productAssoId);
							$ModelItemAttribute -> getSelect()
												->joinleft(array('melipq'=>'mercadolibre_item_price_qty'), " main_table.item_id = melipq.item_id and melipq.store_id = '".$storeId."'" ,array('melipq.meli_price','melipq.meli_qty'))
												-> joinleft(array('melicatatt'=>'mercadolibre_category_attributes'), " main_table.meli_attribute_id = melicatatt.meli_attribute_id and melicatatt.category_id = '".$row->getData('meli_category_id')."'", array('melicatatt.meli_attribute_type','melicatatt.meli_attribute_id as melicatatt_meli_attribute_id','melicatatt.meli_attribute_name as melicatatt_meli_attribute_name', 'melicatatt.category_id as meli_category_id' ))
												
												-> joinleft(array('melicatattval'=>'mercadolibre_category_attribute_values'), "main_table.meli_value_id = melicatattval.meli_value_id and melicatattval.meli_category_id = '".$row->getData('meli_category_id')."' ", array('melicatattval.meli_value_id as melicatattval_meli_value_id','melicatattval.meli_value_name as melicatattval_meli_value_name','melicatattval.meli_value_name_extended as melicatattval_meli_value_name_extended'));
											
							$flg1 = 0;
							$flg2 = 0;	
							$priceCount = 0;
							$colorCount = 0;
							$htmlColor1 = '';
							$html .= '<input type="hidden" name="item_id[]" value="'.$productAssoId.'">';
	
							$colorArr = array();
							$htmlHidden = '';
							foreach($ModelItemAttribute->getData() as $rowAtr){
								$priceCount ++;
								
							 if($rowAtr['meli_attribute_type']){
								$html .= '<td style="border:none !important; width:"100px;" >
											<table width="100%" style="border:none !important;" border="5" cellspacing="0" cellpadding="0" >';
								if(!$flg1 && $rowAtr['meli_attribute_type']=='size'){
									 $flg1 ++;
									 $html .= '<tr>
												<td style="border:none !important;">
													<strong>'.ucwords($rowAtr['meli_attribute_type']).'</strong>
												</td>
												<td style="border:none !important;">
													<strong>Meli '.ucwords($rowAtr['meli_attribute_type']).'</strong>
												</td>
											</tr>';
								 }
								 if($rowAtr['meli_attribute_type']=='color' && $colorCount){
									 $colorCount ++;
									 $html .= '<tr>
												<td style="border:none !important;">
													<strong>'.ucwords($rowAtr['meli_attribute_type']).'</strong>
												</td>
												<td style="border:none !important;">
													<strong>Meli '.ucwords($rowAtr['meli_attribute_type']).'</strong>
												</td>
											</tr>';
								 } else {
									if($rowAtr['meli_attribute_type']=='color'){ $colorCount ++; }
								}
								
								  $html .= '<tr>';
								 
								  if($rowAtr['meli_attribute_type']=='color' && $colorCount == 2){
										 $html .= '<td style="border:none !important; width:"><input id="attribute_id_'.$rowAtr['item_id'].'[]"  type="hidden" name="attribute_id_'.$rowAtr['item_id'].'[]" value="'.$rowAtr['attribute_id'].'"><input id="attribute_value_id_'.$rowAtr['item_id'].'[]"  type="hidden" name="attribute_value_id_'.$rowAtr['item_id'].'[]" value="'.$rowAtr['value_id'].'">'.$rowAtr['value_id'].'</td>';
													
								 }
								 if($rowAtr['meli_attribute_type'] == 'color'){
								 
										$colorArr[] = $rowAtr['melicatattval_meli_value_name_extended'];
										$htmlHidden .= '<input id="meli_attribute_id_'.$rowAtr['item_id'].'[]"  type="hidden" name="meli_attribute_id_'.$rowAtr['item_id'].'[]" value="'.$rowAtr['meli_attribute_id'].'"><input id="meli_attribute_value_id_'.$rowAtr['item_id'].'[]"  type="hidden" name="meli_attribute_value_id_'.$rowAtr['item_id'].'[]" value="'.$rowAtr['meli_value_id'].'">';
										if(count($colorArr) == 2){ // display square image for color
										$html .= $htmlHidden.'<td style="border:none !important; width:"><div class="variation">
													<div class="varBox color"><span class="varColor" title="Marrn" style="background-color:'.$colorArr['1'].';"></span>
														<div class="maskColor-two"><span class="varColor color-two" title="Naranja" style="background-color:'.$colorArr['0'].';"></span>
														</div>
													</div>
												</div>
											</div></td>';
									 }
								 }
								 if($rowAtr['meli_attribute_type'] == 'size'){
									$html .= '<td style="border:none !important;"><input id="attribute_id_'.$rowAtr['item_id'].'[]"  type="hidden" name="attribute_id_'.$rowAtr['item_id'].'[]" value="'.$rowAtr['attribute_id'].'"><input id="attribute_value_id_'.$rowAtr['item_id'].'[]"  type="hidden" name="attribute_value_id_'.$rowAtr['item_id'].'[]" value="'.$rowAtr['value_id'].'">'.$rowAtr['value_id'].'</td>';
									$html .= '<td style="border:none !important;"><input id="meli_attribute_id_'.$rowAtr['item_id'].'[]"  type="hidden" name="meli_attribute_id_'.$rowAtr['item_id'].'[]" value="'.$rowAtr['meli_attribute_id'].'"><input id="meli_attribute_value_id_'.$rowAtr['item_id'].'[]"  type="hidden" name="meli_attribute_value_id_'.$rowAtr['item_id'].'[]" value="'.$rowAtr['meli_value_id'].'">'.$rowAtr['melicatattval_meli_value_name'].'</td>'; 
								 }
								
								 $html .= '</tr></table><td>';
								 }
	
								if($rowAtr['meli_price']!="" && $rowAtr['meli_qty']!="" && $priceCount == count($ModelItemAttribute->getData())){
									 $html .='<td style=" width:88px; vertical-align:top; border:none !important;">';
									 if($rowAtr['meli_price']){						
										$priceVariation = $rowAtr['meli_price']; 
										if($revise){
											$html .='<div style="float:left; width:88px; margin-bottom:10px;"><strong>Price</strong> :<input id="configurable_attribute_price_'.$productAssoId.'" style="width:40px; padding:5px 0;" class="required-entry validate-zero-or-greater input-text required-entry validation-failed" type="text" name="configurable_attribute_price_'.$productAssoId.'[]" value="'.number_format($priceVariation, 2, '.', '').'" /></div>';
										} else {
											$html .= '<div style="float:left; width:88px; margin-bottom:10px;"><strong>Price</strong> : '.$priceVariation.'</div>';
										}
									 }
									 if($rowAtr['meli_qty']!=''){
										 $qtyVariation = $rowAtr['meli_qty']; 
										if($revise){
											 $html .='<div style="float:left; margin-left:10px; margin-bottom:5px;"><strong>Qty</strong> :<input style="width:40px; padding:5px 0;" class="required-entry validate-zero-or-greater input-text required-entry validation-failed" maxlength="5" type="text" name="configurable_attribute_qty_'.$productAssoId.'[]" value="' . ceil($qtyVariation).'" /></div>';
										} else {
											 $html .= '<div style="float:left; margin-left:10px; margin-bottom:5px;"><strong>Qty</strong> : ' . $qtyVariation .'</div>';
										 }
									 }
									 
									 $html .='</td>';
								}
								
								
							  }
								$html .= '<td style=" width:88px; border:none !important;"><div style="margin-left:10px; margin-bottom:5px;"><strong>Store Product Count</strong><br/> '. ceil($qtyAsso).'</div></td>';
								$html .= '<td style=" width:88px; border:none !important;"><div style="float:left; margin-left:10px; margin-bottom:5px;"><strong>Publish Item Count  </strong><br/> '. ceil($qtyVariation).'</div></td>';  
								/*-------------------------------End Used Product-----------------------------------------------*/
								 $html .='</td></tr></table>';
							}
							
						} else {
							$priceVariation = '';
							$qtyVariation = '';
			
							$ModelItemAttribute  = Mage::getModel('items/meliitemattributes')
												-> getCollection()
												-> addFieldToFilter('main_table.store_id',$storeId)
												-> addFieldToFilter('main_table.item_id',$row->getData('product_id'));
							$ModelItemAttribute -> getSelect()
												-> joinleft(array('melipq'=>'mercadolibre_item_price_qty'), " main_table.item_id = melipq.item_id and melipq.store_id = '".$storeId."' ",array('melipq.meli_price','melipq.meli_qty'))
												-> joinleft(array('melicatatt'=>'mercadolibre_category_attributes'), " main_table.meli_attribute_id = melicatatt.meli_attribute_id and melicatatt.category_id = '".$row->getData('meli_category_id')."' ", array('melicatatt.meli_attribute_type','melicatatt.meli_attribute_id as melicatatt_meli_attribute_id','melicatatt.meli_attribute_name as melicatatt_meli_attribute_name'))
												
												-> joinleft(array('melicatattval'=>'mercadolibre_category_attribute_values'), "main_table.meli_value_id = melicatattval.meli_value_id and melicatattval.meli_category_id = '".$row->getData('meli_category_id')."' ", array('melicatattval.meli_value_id as melicatattval_meli_value_id','melicatattval.meli_value_name as melicatattval_meli_value_name','melicatattval.meli_value_name_extended as melicatattval_meli_value_name_extended'));
							
							$flg1 = 0;
							$flg2 = 0;
							$color = 0;	
							$html ='';	
							$priceCount = 0;
							$colorCount = 0;
							$html .= '<input type="hidden" name="item_id[]" value="'.$row->getData('product_id').'">';	
							$html .= '<table width="100" style="margin-bottom:5px; border:none !important;" border="0" cellspacing="0" cellpadding="0"><tr>';
							$htmlHidden = '';
							$colorArr = array();
							foreach($ModelItemAttribute->getData() as $rowAtr){
								$priceCount ++;
							 if($rowAtr['meli_attribute_type']){
								$html .= '<td style="width:100px;" >
											<table width="100%" style="margin-bottom:5px; border:none !important; border:1px solid #D1E0E2;" border="0" cellspacing="0" cellpadding="0">';
								if(!$flg1 && $rowAtr['meli_attribute_type']=='size'){
									 $flg1 ++;
									 $html .= '<tr>
												<td style="border:none !important;">
													<strong>'.ucwords($rowAtr['meli_attribute_type']).'</strong>
												</td>
												<td style="border:none !important;">
													<strong>Meli '.ucwords($rowAtr['meli_attribute_type']).'</strong>
												</td>
											</tr>';
								 }
								 if($rowAtr['meli_attribute_type']=='color' && $colorCount){
									 //$flg2 ++;
									 $colorCount++;
									 $html .= '<tr>
												<td style="border:none !important;">
													<strong>'.ucwords($rowAtr['meli_attribute_type']).'</strong>
												</td>
												<td style="border:none !important;">
													<strong>Meli '.ucwords($rowAtr['meli_attribute_type']).'</strong>
												</td>
											</tr>';
								 } else  if($rowAtr['meli_attribute_type']=='color' ){
									 $colorCount++;
								 }
								 
								 if($rowAtr['meli_attribute_type']=='color'){	$color ++; }
								
										$html .= '<tr>';
										if($colorCount == 2){
											$html .= '<td style="border:none !important;" > 
														<input id="attribute_id_'.$rowAtr['item_id'].'[]"  type="hidden" name="attribute_id_'.$rowAtr['item_id'].'[]" value="'.$rowAtr['attribute_id'].'"><input id="attribute_value_id_'.$rowAtr['item_id'].'[]"  type="hidden" name="attribute_value_id_'.$rowAtr['item_id'].'[]" value="'.$rowAtr['value_id'].'">'.$rowAtr['value_id'].'</td>';
										}
															 
								 if($rowAtr['meli_attribute_type'] == 'color'){
								 
										$colorArr[] = $rowAtr['melicatattval_meli_value_name_extended'];
										$htmlHidden .= '<input id="meli_attribute_id_'.$rowAtr['item_id'].'[]"  type="hidden" name="meli_attribute_id_'.$rowAtr['item_id'].'[]" value="'.$rowAtr['meli_attribute_id'].'"><input id="meli_attribute_value_id_'.$rowAtr['item_id'].'[]"  type="hidden" name="meli_attribute_value_id_'.$rowAtr['item_id'].'[]" value="'.$rowAtr['meli_value_id'].'">'; 
										if(count($colorArr) == 2){ // display square image for color
										$html .= $htmlHidden.'<td style="border:none !important; width:"><div class="variation">
													<div class="varBox color"><span class="varColor" title="Marrn" style="background-color:'.$colorArr['1'].';"></span>
														<div class="maskColor-two"><span class="varColor color-two" title="Naranja" style="background-color:'.$colorArr['0'].';"></span>
														</div>
													</div>
												</div>
											</div></td>';	
										}		
	
								 }
								 if($rowAtr['meli_attribute_type'] == 'size'){
									$html .= '<td style="border:none !important;" ><input id="attribute_id_'.$rowAtr['item_id'].'[]"  type="hidden" name="attribute_id_'.$rowAtr['item_id'].'[]" value="'.$rowAtr['attribute_id'].'"><input id="attribute_value_id_'.$rowAtr['item_id'].'[]"  type="hidden" name="attribute_value_id_'.$rowAtr['item_id'].'[]" value="'.$rowAtr['value_id'].'">'.$rowAtr['value_id'].'</td>';
									$html .= '<td style="border:none !important;">
												<input id="meli_attribute_id_'.$rowAtr['item_id'].'[]"  type="hidden" name="meli_attribute_id_'.$rowAtr['item_id'].'[]" value="'.$rowAtr['meli_attribute_id'].'"><input id="meli_attribute_value_id_'.$rowAtr['item_id'].'[]"  type="hidden" name="meli_attribute_value_id_'.$rowAtr['item_id'].'[]" value="'.$rowAtr['meli_value_id'].'">'.$rowAtr['melicatattval_meli_value_name'].'
											</td>'; 
								 }
								 $html .= '</tr></table></td>';
								 }
								if($rowAtr['meli_price']!="" && $rowAtr['meli_qty']!="" && $priceCount == count($ModelItemAttribute->getData())){
									 $html .='<td style=" vertical-align:top; border:none !important; width:88px;" >';
									 if($rowAtr['meli_price']){						
										$priceVariation = $rowAtr['meli_price']; 
										if($revise){
											$html .='<div style="float:left; width:88px; margin-bottom:10px;"><strong>Price</strong> :<input id="attribute_price_'.$row->getData('product_id').'" style="width:40px; padding:5px 0;" class="required-entry validate-zero-or-greater input-text required-entry validation-failed" type="text" name="attribute_price_'.$row->getData('product_id').'[]" value="'.number_format($priceVariation, 2, '.', '').'" /></div>';
										} else {
											$html .= '<div style="float:left; width:88px; margin-bottom:10px;"><strong>Price</strong> :'.$priceVariation.'</div>';
										}
									 }
									 if($rowAtr['meli_qty']!=''){
										 $qtyVariation = $rowAtr['meli_qty']; 
										
										 if($revise){
											 $html .='<div style="float:left; margin-left:10px; margin-bottom:5px;"><strong>Qty</strong> :<input  style="width:40px; padding:5px 0;" class="required-entry validate-zero-or-greater input-text required-entry validation-failed" maxlength="5" type="text" name="attribute_qty_'.$row->getData('product_id').'[]" value="' . ceil($qtyVariation).'" /></div>';
										 } else {
											 $html .= '<div style="float:left; margin-left:10px; margin-bottom:5px;" ><strong>Qty</strong> : ' . $qtyVariation .'</div>';
										 }
									 }
									 $html .='</td>';
								}
							  }
							  $html .= '</tr></table>';
							}
							
					  } else {
							$html = 'No';
				 }
				}
			echo $html;
	}
		
		
		
}






