<?php  
/** includes meli.php for get api data */
$meliClass = Mage::getBaseDir('code').DS. "community/Mercadolibre/items/helper/meli.php";
if (file_exists($meliClass)) {
    include_once $meliClass;
}

class Mercadolibre_Items_Model_Common extends Mage_Core_Model_Abstract
{
	private $moduleName = "Items";
	private $fileName = "Common.php";
	private $loggerFileName =  'meli_logger';
	
	//message variable
	private $infoMessage = "";
	private $errorMessage = "";
	private $successMessage = "";

	const LOGGERESYSTEM_ID = 'Yes'; 
    public static $CURL_OPTS = array(CURLOPT_USERAGENT => 'MeliPHP-sdk-0.0.3', CURLOPT_CONNECTTIMEOUT => 10, CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 60);
	private $params = array();
	
	
	
    public function _construct()
    {
        parent::_construct();
        $this->_init('items/common');
    }
	
	/**
	* connect ML API
	* Return json data
	* this can be use 
	* 1-custom code
	* 2-ML SDK
	* 3-Zend_Rest_Client of magento
	*/
    public function connect($service_url,$method='GET')
    {   
		try{
			$curl = curl_init($service_url);
			curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json;charset=UTF-8'));
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($curl, CURLOPT_ENCODING, 1);
			curl_setopt($curl, CURLOPT_ENCODING, 1);
			if($method == "POST")
				curl_setopt($curl, CURLOPT_POST, true);
			else if ($method == "PUT")
				curl_setopt($curl, CURLOPT_PUT, true);

			$data = curl_exec($curl);
			$dataArr = (array) json_decode($data, true);
			if(isset($dataArr['error']) && trim($dataArr['error'])!=''){
				$this->errorMessage = 'Error :: '.$dataArr['status'].' '.$dataArr['message'];
				$this->saveLogger($this->moduleName, "Error", $this->fileName, $this->errorMessage);
				$this->sendNotificationMail($to='', 'Connection Error Report', $this->errorMessage);
			}
			return $data;
			curl_close($curl);
		}catch(Exception $e){		
			$this->errorMessage = $e->getErrorTrace()."Exception:: Could not connect to API server";
			$this->saveLogger($this->moduleName, "Exception", $this->fileName, $this->errorMessage);
		}
    }

	public function connect1($service_url)
    {
		try{
			$curl = curl_init($service_url);
			curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json;charset=UTF-8'));
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_HEADER, 1);
			curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($curl, CURLOPT_ENCODING, 1);
			curl_setopt($curl, CURLOPT_NOBODY, true); 
			return curl_exec($curl);
			curl_close($curl);
		}catch(Exception $e){
			$this->errorMessage = $e->getErrorTrace()."Exception:: Could not connect to API server";
			$this->saveLogger($this->moduleName, "Exception", $this->fileName, $this->errorMessage);
		}
    }
    
    public function meliConnect($service_url,$method='GET', $params = null)
    {
		try{
			$this->params = $params;
			$response = $this->execute($method, $service_url);
			return $response;

		}catch(Exception $e){
			$this->errorMessage = $e->getErrorTrace()."Exception:: Could not connect to API server";
			$this->saveLogger($this->moduleName, "Exception", $this->fileName, $this->errorMessage);
		}
    }

   protected function execute($method, $url) {
   			try{
				$response = $this -> makeRequest($method, $url);
				$response['json'] = json_decode($response['body'], true);
				return $response;	
			}catch(Exception $e){
				$this->errorMessage = "Exception:: Exception occusred while executing the request";
				$this->saveLogger($this->moduleName, "Error", $this->fileName, $this->errorMessage);
	 		}
    }
	
	  public function getDescTemplates($store_id) {
		$descTemplates = Mage::getModel('items/meliitemprofiledetail')-> getCollection()
						-> addFieldToFilter('store_id', $store_id );
		if(count($descTemplates)>0){
			 $descTemplatesArr = array(''=>'Please Select');
			 foreach($descTemplates as $tempData){
				$key  = $tempData['profile_id'];
				$descTemplatesArr[$key] = $tempData['profile_name'] ;
			 }
		}else{
			$descTemplatesArr = array(''=>'No Template');
		}
		return $descTemplatesArr;
  }
  
  public function getShippingTemplates($store_id) {
		$shippingTemplates = Mage::getModel('items/melishipping')->getCollection()
					-> addFieldToFilter('store_id',$store_id);
		if(count($shippingTemplates)>0){
			 $shippingTemplatesArr = array(''=>'Please Select');
			 foreach($shippingTemplates as $tempData){
				$key  = $tempData['shipping_id'];
				$shippingTemplatesArr[$key] = $tempData['shipping_profile'] ;
			 }
		}else{
			$shippingTemplatesArr = array(''=>'No Template');
		}
		return $shippingTemplatesArr;
  }
  
   public function getPaymentTemplates($store_id) {
		$paymentTemplates = Mage::getModel('items/melipaymentmethods')->getCollection();
		if(count($paymentTemplates)>0){
			foreach($paymentTemplates as $tempData){
				$key  = $tempData['id'];
				$paymentTemplatesArr[] = array('value'=>$key, 'label'=>$tempData['payment_name']);
			}
		}else{
			$paymentTemplatesArr =array('value'=>'', 'label'=>'No Template');  
		}
		return $paymentTemplatesArr;
  }
  
   public function getListingTemplates($store_id) {
		$listingTemplates = Mage::getModel('items/meliproducttemplates')->getCollection();
		if(count($listingTemplates)>0){
			$listingTemplatesArr = array(''=>'Please Select');
			 foreach($listingTemplates as $tempData){
				$key  = $tempData['template_id'];
				$listingTemplatesArr[$key] = $tempData['title'] ;
			 }
		}else{
			$listingTemplatesArr = array(''=>'No Template');
		}
		return $listingTemplatesArr;
  }

protected function makeRequest($method, $url) {
	try{
		$ch = '';
		$params = $this->params;
		if (!$ch) {
            $ch = curl_init();
        }

        $opts = self::$CURL_OPTS;
        
		if ($method == 'GET') {
            if ($params) {
                if (strpos($url, '?') !== false) {
                    $url .= '&' . http_build_query($params, null, '&');
                } else {
                    $url .= '?' . http_build_query($params, null, '&');
                }               
            }
        } else {

            if(!isset($opts[CURLOPT_HTTPHEADER]) || $opts[CURLOPT_HTTPHEADER] == null){
                $opts[CURLOPT_HTTPHEADER] = array();
            }

			$opts[CURLOPT_CUSTOMREQUEST] = $method;
			
			if ($params) {
                
                if (is_array($params)) {
                    $opts[CURLOPT_POSTFIELDS] = http_build_query($params, null, '&');

                } else {                    
                    $opts[CURLOPT_POSTFIELDS] = $params;
					$opts[CURLOPT_HTTPHEADER] = array_merge($opts[CURLOPT_HTTPHEADER], array('Content-Type: application/json;charset=utf-8', 'Content-Length: ' . strlen($params)));
                }
            }

        }
		
		$opts[CURLOPT_URL] = $url;

        curl_setopt_array($ch, $opts);

        curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
		
		$content = curl_exec($ch);
		$response = curl_getinfo($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);        
        $startBody = false;
        $data = explode("\n", $content);
        $headers = array();
        $body = "";
        foreach ($data as $line) {
            if ((strlen($line) == 1 && ord($line) == 13) || $startBody) {
                if ($startBody) {
                    $body = $line;
                } else {
                    $startBody = true;
                }
            } else {
                if (ord(strpos($line, 'HTTP')) != 0) {
                    $key = 'Status-Code';
                    $value = intval(substr($line, 9, 3));
                } else {
                    list($key, $value) = explode(":", $line);
                }
                $headers[$key] = $value;
            }
        }        
        if ($content === false) {
				
				$this->errorMessage = "Exception:: No response returned from the REST request";
				$this->saveLogger($this->moduleName, "Error", $this->fileName, $this->errorMessage);
        }
       
		curl_close($ch);
        return array('statusCode' => $httpCode, 'body' => $body, 'headers' => $headers);
    
	 }catch(Exception $e){
				$this->errorMessage = "Exception:: Exception occusred while executing the request";
				$this->saveLogger($this->moduleName, "Error", $this->fileName, $this->errorMessage);
	 }
	
	}

	
	/*
		This function will be called by any module in magento to register their log on custom
		log table
	*/
	public function saveLogger($moduleKey, $status, $fileName, $description)
	{
	
		//Check logger setting enable/disable
		$enableLoggerMethod = Mage::getStoreConfig('mlitems/meligeneralsetting/enablelogging',Mage::app()->getStore());;
		if($enableLoggerMethod){
			$description = htmlspecialchars($description, ENT_QUOTES);
			//magento date time
			$currentTime =Mage::helper('items');
			$time = $currentTime->getCurrentDateTime('Y-m-d H:i:s');
			//Check logger setting for files system or database
			$loggerMethod = self::LOGGERESYSTEM_ID;
			
			if($loggerMethod){
				//Mage::log($time." | ".$moduleKey." | ".$status." | ".$fileName." | ".$description, Zend_Log::DEBUG, $this->loggerFileName);
				Mage::log($description, Zend_Log::DEBUG, $this->loggerFileName);
			}
		}
		
		
	}
	
	/*
		sendNotificationMail function will send mail to the admin for any log or cron update
	*/
	public function sendNotificationMail($to='', $mailSubject, $message)
	{
	   
	   if(trim($to) == ''){ 
	   		/* Admin email id */
			$to = Mage::getModel('admin/user')->load('1')->getEmail();
		}
		$toName = "";
		$mailTemplate = Mage::getModel('core/email_template');
        $translate  = Mage::getSingleton('core/translate');

		//magento template_code for email notification
		$emailTemplate  = Mage::getModel('core/email_template')->loadDefault('meli_notification-template');  

		$from_email = Mage::getStoreConfig('trans_email/ident_general/email',Mage::app()->getStore()); //fetch sender email
        $from_name = Mage::getStoreConfig('trans_email/ident_general/name',Mage::app()->getStore()); //fetch sender name
		
		$emailTemplate->setSenderName($from_name);
		$emailTemplate->setSenderEmail($from_email);
		$emailTemplate->setTemplateSubject($mailSubject);

		$emailTemplateVariables = array('message'=>$message);
		$processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);	
		if($emailTemplate) {
			try {
				/* Remove this comment for sent email */
				//$emailTemplate->send($to,$toName, $emailTemplateVariables);
				Mage::getSingleton('adminhtml/session')->addSuccess('Notification email sent.');
			}catch(Exception $e){
				return false;
			}
			return true;
		}
                
	}
	
	function rrmdir($dir) {
	
		 foreach(glob($dir . '/*') as $file) {
			 if(is_dir($file))
				 rrmdir($file);
			 else
				 unlink($file);
		 }
		 rmdir($dir);
	 
   }
   
   public function getCleanUpLog(){
   		try{
			$commonModel = Mage::getModel('items/common');
			$write = Mage::getSingleton('core/resource')->getConnection('core_write');
			$res = $write->fetchAll("select DATEDIFF(CURDATE(),DATE_FORMAT(run_datetime,'%Y-%m-%d')) as days from mercadolibre_category_update");	
			if(trim($res['0']['days']) == Mage::getStoreConfig("mlitems/meligeneralsetting/logcleanup",Mage::app()->getStore())){
				$dir = Mage::getBaseDir('var').DS.'log'.DS.$this->loggerFileName;
				if(is_file($dir)){
					@unlink($dir);
				}
			}
		} catch(Exception $e){
				 $commonModel->client_credentials($this->moduleName, "Exception", $this->fileName, $e->getMessage());
		}
	}	

  
	public function getMLRefreshToken ($storeId) {
		 try{
			$commonModel = Mage::getModel('items/common');
			if(Mage::getStoreConfig("mlitems/mltokenaccess/mltokenclientid",Mage::app()->getStore($storeId))){
				$client_id = Mage::getStoreConfig("mlitems/mltokenaccess/mltokenclientid",Mage::app()->getStore($storeId));
			} else {
				$this->errorMessage = "Error :: Client Id Not Found OR Invalid";
				$this->saveLogger($this->moduleName, "Exception", $this->fileName, $this->errorMessage);
			}
			if(Mage::getStoreConfig("mlitems/mltokenaccess/mltokenclientsecret",Mage::app()->getStore($storeId))){
				$client_secretd = Mage::getStoreConfig("mlitems/mltokenaccess/mltokenclientsecret",Mage::app()->getStore($storeId));
			} else {
				$this->errorMessage = "Error :: Client Secret Not Found OR Invalid";
				$this->saveLogger($this->moduleName, "Exception", $this->fileName,$this->errorMessage);
			}			
			
			/* Get Base URL Id */
			if(Mage::getStoreConfig("mlitems/mltokenaccess/mlapiurl",Mage::app()->getStore($storeId))){
				 $api_url = Mage::getStoreConfig("mlitems/mltokenaccess/mlapiurl",Mage::app()->getStore($storeId));
			} else {
				$this->errorMessage = "Error :: Api Url Not Found OR Invalid";
				$commonModel->saveLogger($this->moduleName, "Exception", $this->fileNamegetMLRefreshToken, $this->errorMessage);
				$commonModel->sendNotificationMail($this->to, 'getMLRefreshToken Error', $this->errorMessage);
			}
			
			$service_url = $api_url."/oauth/token";
			$param = array( 
							'grant_type'=>'client_credentials',
							'client_id'=>trim($client_id),
							'client_secret'=>trim($client_secretd),
							);

			$result = $this -> meliConnect($service_url,"POST",$param);

			if(isset($result['json']['access_token'])){
				$result = array('value' => $result['json']['access_token'], 'expires' => time() + $result['json']['expires_in'], 'scope' => $result['json']['scope'], 'refresh_token' => $result['json']['refresh_token']);	
	
				$write = Mage::getSingleton('core/resource')->getConnection('core_write');
				$rowExist = $write->fetchCol("SELECT token_expires from  mercadolibre_token_details WHERE store_id ='".$storeId."'");
				if(count($rowExist)){	
					$service_url_user = $api_url."/users/me?access_token=".$result['value'];	
					$resultUser = $this -> meliConnect($service_url_user,"GET");
					$write->query("UPDATE mercadolibre_token_details set seller_id = '".$resultUser['json']['id']."',access_token = '".$result['value']."', token_expires ='".$result['expires']."' WHERE store_id= '".$storeId."'");
				} else {
					$service_url_user = $api_url."/users/me?access_token=".$result['value'];	
					$resultUser = $this -> meliConnect($service_url_user,"GET");
					$write->query("INSERT INTO mercadolibre_token_details set seller_id = '".$resultUser['json']['id']."',access_token = '".$result['value']."', token_expires ='".$result['expires']."', store_id= '".$storeId."'");
				}
			}
			

			return true ;
		 
		 }catch(Exception $e){
				$this->saveLogger($this->moduleName, "Exception", $this->fileName, $e->getMessage());
		 }
     }
	 
/*	  public function getMLRootCategoryRecursive($categories,$spacer=''){
	  
			try{
				$storeId = Mage::helper('items')->_getStore()->getId();
				$str = '';
				$spacer .= '*&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';

				
				foreach($categories as $key => $categoryId) {
					$commonModel = Mage::getModel('items/common');
					$write = Mage::getSingleton('core/resource')->getConnection('core_write');
					$sql_meli_cate_filter = '';
					$sql_meli_cate_filter = "SELECT meli_category_id, meli_category_name from mercadolibre_categories_filter where store_id = '".$storeId."' AND meli_category_id ='".$categoryId."'".";";	
					$dataMLcat = $write->fetchAll($sql_meli_cate_filter);
					if(count($dataMLcat) > 0){
						$str .= $dataMLcat['0']['meli_category_id'].'##MLCATID##'.$spacer.$dataMLcat['0']['meli_category_name']."=>";
					}	
					$array[] = $dataMLcat;
					if(count($dataMLcat) > 0){
						$sql_meli_cate_filter = "SELECT meli_category_id from mercadolibre_categories_filter where store_id = '".$storeId."' AND root_id ='".$categoryId."'".";";	
						$childs = $write->fetchCol($sql_meli_cate_filter);
						$str .=  $this->getMLRootCategoryRecursive($childs,$spacer);
					}
				}
				return $str;
			
			}catch(Exception $e){
					$commonModel->saveLogger($this->moduleName, "Exception", $this->fileName, $e->getMessage());	
			}
	}
*/	
	 public function getMLRootCategoryRecursive($categories,$spacer=''){
		try{
			$storeId = Mage::helper('items')->_getStore()->getId();
			$str = '';
			$spacer .= '*&nbsp;&nbsp;&nbsp;';
				$categoryId = $categories['0'];
				$commonModel = Mage::getModel('items/common');
				$write = Mage::getSingleton('core/resource')->getConnection('core_write');
				$sql_meli_cate_filter = '';
				$sql_meli_cate_filter = "SELECT meli_category_id, meli_category_name from mercadolibre_categories_filter where  store_id = '".$storeId."' AND  root_id ='".$categoryId."'".";";
				;
				$dataMLcat = $write->fetchAll($sql_meli_cate_filter);
				foreach($dataMLcat as $row) {			
					$str .= $row['meli_category_id'].'##MLCATID##'.$spacer.$row['meli_category_name']."=>";
					$str .= $this->getMLRootCategoryRecursive(array($row['meli_category_id']),$spacer);	
				}
				return $str;
		
		}catch(Exception $e){
				$commonModel->saveLogger($this->moduleName, "Exception", $this->fileName, $e->getMessage());	
		}
	}

	public function getMLCategoriesToMap($categories){
			
			try{
				
				//$commonModel = Mage::getModel('items/common');
                $mageCateCollection = array('PLEASE_SELECT'=>'Please Select','NO_MAPPING'=>'No Mapping');
				$data = $this->getMLRootCategoryRecursive($categories); // 
				$arrayMageCat = explode('=>',$data);
				foreach($arrayMageCat as $key => $value){
						$valArr = explode('##MLCATID##',$value);
						if(isset($valArr['1']) && trim($valArr['1'])!=''){
							//$mageCateCollection[$valArr['0']] =  $valArr['0'] . ' - ' .$valArr['1'];
							$mageCateCollection[$valArr['0']] = $valArr['1'];
						}
				}
				
				return $mageCateCollection;
			
			}catch(Exception $e){
					$this->saveLogger($this->moduleName, "Exception", $this->fileName, $e->getMessage());	
			}
	}
	
	public function getMLCategoriesToAttributeMap($categories){
			
			try{
				$store_id = Mage::helper('items')->_getStore()->getId();
				$catMappingCollection = Mage::getModel('items/melicategoriesmapping')
									  -> getCollection()
									  -> addFieldToFilter('main_table.meli_category_id',array('neq'=>$categories))
									  -> addFieldToFilter('main_table.root_id',$categories['0'])
									  -> addFieldToFilter('main_table.store_id',$store_id);
				$catMappingCollection -> getSelect()
									  -> join(array('mc' => 'mercadolibre_categories_filter'), 'mc.meli_category_id = main_table.meli_category_id ', array('mc.meli_category_name','mc.meli_category_path'));

                $mageCateCollection = array('PLEASE_SELECT'=>'Please Select');
				if(count($catMappingCollection->getData()) > 0){
					foreach($catMappingCollection->getData() as $rowCat){
						if(trim($rowCat['meli_category_id'])!='' && trim($rowCat['meli_category_name'])!=''){
							$mageCateCollection[$rowCat['meli_category_id']] = $rowCat['meli_category_path'];
						}
					}
				}
				
				return $mageCateCollection;
			}catch(Exception $e){
					$this->saveLogger($this->moduleName, "Exception", $this->fileName, $e->getMessage());	
			}
	}
	
	
		public function  getMageRootCategories($categories) {
			try{
				//$commonModel = Mage::getModel('items/common');
				$str = '';
				foreach($categories as $category) {
					if(trim($category->getId())!='3'){		
						$str .= $category->getId().'##MLCATID##'.$category->getName()."=>";		 
					}
					if($category->hasChildren() && $category->getLevel() == '1') {
						$children = Mage::getModel('catalog/category')->getCategories($category->getId());
						$str .= $this->getMageRootCategories($children);						 
					}
				}
				return  $str;
			}catch(Exception $e){
					$this->saveLogger($this->moduleName, "Exception", $this->fileName, $e->getMessage());	
				}
		}
		
		public function getMeliCategoryNameByMageCatId($mageCateId){
			try{
				$catArr = array();
				$data = '';
				if(count($mageCateId) > 0 ){
					foreach($mageCateId as $row){
						$mappingModel = Mage::getModel('items/melicategoriesmapping')->getCollection()->addFieldToFilter('mage_category_id',trim($row));
						$mappingModel->getSelect()->join(array('table_alias'=>'mercadolibre_categories'), 'main_table.meli_category_id = table_alias.meli_category_id', array('table_alias.meli_category_name'));	
						$data = $mappingModel->getData();
						if(trim($data[0]['meli_category_name']) !=''){
							$catArr[] = $data[0]['meli_category_name'];
						}
					}
					return implode(' , ',$catArr);
				} else {
					return '';
				}
			 } catch(Exception $e){
			 		//$commonModel = Mage::getModel('items/common');
					$this->saveLogger($this->moduleName, "Exception", $this->fileName, $e->getMessage());	
				}
		}
                
                
                
        public function  getMageRootCategoryIds($categories) {
			
			try{

				$str = '';
				foreach($categories as $category) {
					if(trim($category->getId())!='3'){		
						$str .= $category->getId()."=>";		 
					}
					if($category->hasChildren()) {
						$children = Mage::getModel('catalog/category')->getCategories($category->getId());
						$str .= $this->getMageRootCategoryIds($children);						 
					}
				}
				
				return  $str;
			
			}catch(Exception $e){
				$this->saveLogger($this->moduleName, "Exception", $this->fileName, $e->getMessage());	
			}
		}
                
	    public function getMageFilterCategoryIds($rootcatId){
					if($rootcatId > 0){
					$categories = Mage::getModel('catalog/category')
												->getCollection()
												->addFieldToFilter('parent_id', array('eq'=>$rootcatId))
												->addFieldToFilter('is_active', array('eq'=>'1'))
												->addAttributeToSort('position', 'ASC')
												->addAttributeToSelect('id')
												->addAttributeToSelect('name');
					}	else {
						$categories = Mage::getModel('catalog/category')
												->getCollection()
												//->addFieldToFilter('parent_id', array('eq'=>$rootcatId))
												->addFieldToFilter('is_active', array('eq'=>'1'))
												->addAttributeToSort('position', 'ASC')
												->addAttributeToSelect('id')
												->addAttributeToSelect('name');
					}

					$MageCateData =  $this->getMageRootCategoryIds($categories); 					
					$arrayMageCat = explode('=>',$MageCateData);
					$Finalcategories = array();
					foreach($arrayMageCat as $key => $value){
						if(trim($value)!=''){
								$Finalcategories[] = $value;
						}
					}
				return $Finalcategories;
			
	    }
                
      public function getBuyingType() {
           $buyingTypeArr = array(''=>'Please Select',
                                    'auction'=>'Auction',
                                    'buy_it_now'=>'Buy it now',
                                    'classified'=>'Classified'             
                                    );
           return $buyingTypeArr;
      }
       public function getListingType() {
             
			 $listingTypeArr = array(''=>'Please Select',
                                    'gold_premium'=>'Oro Premium',
                                    'gold'=>'Oro',
                                    'silver'=>'Plata',
                                    'bronze' => 'Bronce',
                                    'free' =>'Gratuita'
               );
           return $listingTypeArr;
      }
       public function getCondition() {
           $conditionArr = array(''=>'Please Select',
                                'new'=>'New',
                                'used'=>'Used'

               );
           return $conditionArr;
      }
	  
	  public function getMageFinalProducts($rootId){
	  			
			try{
				$productsArr = array();
				//$categories[] = $rootId;
				//$commonModel = Mage::getModel('items/common');

				$FilterCategory =  $this->getMageFilterCategoryIds($rootId); 
				$confiSimpleProductIds = $this -> confiSimpleProductIds();
				$confiSimpleProductIds = array_unique($confiSimpleProductIds);
				
				/* Product Collection  */
				$collection = Mage::getResourceModel('catalog/product_collection')
							 -> addAttributeToFilter('entity_id', array('nin' => $confiSimpleProductIds));
				/* Join to get Product Categories & categories filrter  */
				$collection -> joinField('category_id','catalog/category_product','category_id','product_id=entity_id',null,'inner')
							-> addAttributeToFilter('category_id', array('in' => $FilterCategory))
							->getSelect()->group('e.entity_id')->having('count(e.entity_id) =1');
		
				if(count($collection->getData()) > 0){
					foreach($collection->getData() as $row){
						$products[] = $row['entity_id'];
					}
					$productsArr = array_unique($products);
				}
			} catch(Exception $e){
					$this->saveLogger($this->moduleName, "Exception", $this->fileName, $e->getMessage());	
			}
			
			return $productsArr;

	  }
	  
		  public function confiSimpleProductIds(){
		  		/* get Simple product Used in configurable product */
		  		$assoc_productIds = array();
				$collection = Mage::getResourceModel('catalog/product_collection')-> addFieldToFilter('type_id', array('configurable'));
				foreach($collection->getAllIds() as $key => $pId){
					$product = Mage::getModel('catalog/product')->load($pId);
					$associated_prods = $product->getTypeInstance()->getUsedProducts();
					foreach ($associated_prods as $assoc_product) {
							$assoc_products[] = $assoc_product;
							$assoc_productIds[] =  $assoc_product->getId();
					}
				}
				return $assoc_productIds;
		  }
	  
	  public function inputData($str){
			//if (!get_magic_quotes_gpc()) {
				$str = mysql_escape_string($str);
			//}
		return $str;
	}
	
	public function forwardSlashToBackSlash($str){
		return implode('/',explode('\\',$str));
	}
	 
	 public function getShippingProfileType() {
           $shippingProfileTypeArr = array(''=>'Please Select',
                                    'me1'=>'me1',
                                    'me2'=>'me2',
                                    'custom '=>'custom',
									'unknown' => 'unknown'             
                                    );
           return $shippingProfileTypeArr;
      } 
	  public function getShippingTypeOption() {
           $shippingTypeOptionArr = array(''=>'Please Select',
                                    'free_shipping'=>'free shipping',
                                    'enter_shipping_cost'=>'enter shipping cost'
                                    );
           return $shippingTypeOptionArr;
      } 
	  
	  public function getChangedSlash($str){
				if(!strpos($_SERVER['PATH'],'Windows')){  // if server is linux
	  				$str = implode('/',explode('\\',$str));
				}
				return $str;
	  }
	  
	 public function getMLInventoryPriceUpdate(){
				
			$updatepriceqtycronjob = 0;
			$updatepriceqtycronjob = Mage::getStoreConfig("mlitems/meliinventorysetting/updatepriceqtycronjob",Mage::app()->getStore());
			if($updatepriceqtycronjob){
				try{
					$mlItemListingArr = array();
					$mlPriceQtyIdsArr = array();
					
					$commonModel = Mage::getModel('items/common');
					
					$commonModel->saveLogger($this->moduleName, "Message", $this->fileName,'Message:: Update Price & Qty on Mercadolibre Start.');
					
					$store = $this->_getStore();
					$mlItemListingColl =  Mage::getModel('items/meliitemattributes')
										-> getCollection()->addFieldToSelect('item_id')
										-> distinct(true);
					$mlItemListingColl -> getSelect()
										->joinleft(array('melipq'=>'mercadolibre_item_price_qty'), 'main_table.item_id = melipq.item_id',array('melipq.id','melipq.meli_price','melipq.meli_qty'))
										-> order( array('item_id ASC'));
										
					// Get product id from mkercadolibre tables.
					if(count($mlItemListingColl->getData()) > 0){
						foreach($mlItemListingColl->getData() as $mlpRow){
								$mlItemListingArr[] = $mlpRow['item_id'];
								$mlPriceQtyIdsArr[$mlpRow['item_id']] = $mlpRow['id'];
						}
					}
					if(count($mlItemListingArr) > 0){
						$productsCollection =  Mage::getModel('catalog/product')-> getCollection()
											-> addAttributeToFilter('entity_id', array('in' => $mlItemListingArr));
						$productsCollection -> joinField('qty','cataloginventory/stock_item','qty','product_id=entity_id','{{table}}.stock_id=1','left');
						$productsCollection -> joinAttribute('price','catalog_product/price','entity_id',null,'left',$store->getId());   
						if(count($productsCollection->getData()) > 0){
							foreach($productsCollection->getData() as $product){
								$priceQtyId ='';
								$priceQtyId = $mlPriceQtyIdsArr[$product['entity_id']];
								$meliItemPriceQtyModel =  Mage::getModel('items/meliitempriceqty')-> load($priceQtyId); 
								$data = array(
											'id' => $priceQtyId,
											'meli_price' => number_format($product['price'], 2, '.', ''),
											'meli_qty' => (int) $product['qty'],
										);
								$meliItemPriceQtyModel->setData($data);	
								$meliItemPriceQtyModel->save();
							}	
						}
						echo "Mercadolibre Item Listing price,qty have been updated successfully.";
					} else {
						echo "There is no data found for updated.";
					}
					/* Cron job to rvice published listing */
					$mlPublishItemColl  = Mage::getModel('items/mercadolibreitem')->getCollection()-> addFieldToFilter('main_table.status','active');
					$mlPublishItemColl -> getSelect()
									   -> join(array('mapping'=>'mercadolibre_categories_mapping'), 'main_table.category_id = mapping.mage_category_id',array('mapping.meli_category_id'));
					$successMessageArr = array();
					if(count($mlPublishItemColl->getData()) > 0){
						foreach($mlPublishItemColl->getData() as $reviseRow){
							$productId = '';
							$productId = $reviseRow['product_id'];
							$_product = Mage::getModel('catalog/product')->load($productId);
							if($_product->getData('type_id') == 'configurable'){
								$associated_prods = $_product->getTypeInstance()->getUsedProducts();
								$html = '';
								$variations = array();
								foreach ($associated_prods as $assoc_product) {
									$productAssoId ='';		
									$productAssoId = $assoc_product->getId(); 

									$ModelItemAttributeCon  = Mage::getModel('items/meliitemattributes')->getCollection()->addFieldToFilter('main_table.item_id',$productAssoId); 
									$ModelItemAttributeCon -> getSelect()
														   -> joinleft(array('melicatatt'=>'mercadolibre_category_attributes'), " main_table.meli_attribute_id = melicatatt.meli_attribute_id and melicatatt.category_id = '".$reviseRow['meli_category_id']."'", array('melicatatt.meli_attribute_id','melicatatt.meli_attribute_name'))
														   -> joinleft(array('melicatattval'=>'mercadolibre_category_attribute_values'), "main_table.meli_value_id = melicatattval.meli_value_id and melicatattval.meli_category_id = '".$reviseRow['meli_category_id']."' ", array('melicatattval.meli_value_id','melicatattval.meli_value_name'));
														   
								$attribute_combinations = array();
								if(count($ModelItemAttributeCon->getData()) > 0){
									foreach($ModelItemAttributeCon->getData() as $row){
										$attribute_combinations[] = (object) array('id'=>$row['meli_attribute_id'],'value_id' => $row['meli_value_id']);
									}
								}
								
								$PriceQtyAttributeCon  =  Mage::getModel('items/meliitempriceqty')->getCollection()->addFieldToFilter('main_table.item_id',$productAssoId); 
								$priceQtyArr = $PriceQtyAttributeCon->getData();
								if(count($attribute_combinations) > 0){
									   $variations[] = (object) array('attribute_combinations'=>$attribute_combinations,'available_quantity' => (int)$priceQtyArr['0']['meli_qty'],'price' => (int)$priceQtyArr['0']['meli_price']);
								  }	
								}
									
							} else {	
							
							/*----------Simple Product----------------*/	
							$ModelItemAttribute  = 	Mage::getModel('items/meliitemattributes')->getCollection()->addFieldToFilter('main_table.item_id',$productId);
							$ModelItemAttribute -> getSelect()
												-> joinleft(array('melicatatt'=>'mercadolibre_category_attributes'), " main_table.meli_attribute_id = melicatatt.meli_attribute_id and melicatatt.category_id = '".$ModelMeliCatArr['0']['meli_category_id']."' ", array('melicatatt.meli_attribute_id','melicatatt.meli_attribute_name'))
												-> joinleft(array('melicatattval'=>'mercadolibre_category_attribute_values'), "main_table.meli_value_id = melicatattval.meli_value_id  and melicatattval.meli_category_id = '".$ModelMeliCatArr['0']['meli_category_id']."' ", array('melicatattval.meli_value_id','melicatattval.meli_value_name'));		
													
								$variations = array();
								if(count($ModelItemAttribute->getData()) > 0){
									foreach($ModelItemAttribute->getData() as $row){
										$attribute_combinations[] = (object) array('id'=>$row['meli_attribute_id'],'value_id' => $row['meli_value_id']);
									}
								}
								$PriceQtyAttributeCon  =  Mage::getModel('items/meliitempriceqty')->getCollection()->addFieldToFilter('main_table.item_id',$productId); 
								$priceQtyArr = $PriceQtyAttributeCon->getData();
								if(count($attribute_combinations) > 0){
									   $variations[] = (object) array('attribute_combinations'=>$attribute_combinations,'available_quantity' => (int)$priceQtyArr['0']['meli_qty'],'price' => (int)$priceQtyArr['0']['meli_price']);
								  }			
							}
							 
							/* Start Common Data */  
							$data = array('variations'=>$variations);
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
							$errorItemIdsArr = array();
							/* Send Post Request & Get responce   */
							if(trim($access_token)!='' & trim($access_token)!=''){
									$meli_item_id ='';
									$meli_item_id = $reviseRow['meli_item_id'];
									$requestUrl = $apiUrl.'/items/'.$meli_item_id.'?access_token='.$access_token;
									$response = $commonModel-> meliConnect($requestUrl,'PUT',$requestData);
									if(isset($response['statusCode']) && $response['statusCode'] == 200){
										$this->successMessage = "Meli Item id( ".$meli_item_id." ) has been revise successfully.";
										$successMessageArr[] = "Meli Item id( ".$meli_item_id." ) has been revise successfully.";
										$commonModel->saveLogger($this->moduleName, "Message:: Update Price & Qty on Mercadolibre.", $this->fileName, $this->successMessage);
									} else if(isset($response['json']['message']))  {
										$errorItemIdsArr[] = $meli_item_id; 
										
										$this->errorMessage .= "Error :: Meli Item Id (". $meli_item_id .")". $response['json']['message'] .' '.$response['json']['status'].' ' . $response['json']['error']." <br />";
										$this->errorMessageLog = "Error :: Meli Item Id (". $meli_item_id .")". $response['json']['message'] .' '.$response['json']['status'].' ' . $response['json']['error'];
										$commonModel->saveLogger($this->moduleName, "Exception", $this->fileName, $this->errorMessageLog);
										if(isset($response['json']['cause']) && count ($response['json']['cause']) > 0 ){
											$this->errorMessage .= "Error Cause :: ";
											foreach($response['json']['cause'] as $row){
												$this->errorMessage .= $row['cause'].':'.$row['message'];
											}
										}
										
										echo '<br />'.$this->errorMessage.'<br />';
								}
							}
						}
					}
					$implodeMeaasge ='';
					if(count($successMessageArr) > 0){
						$implodeMeaasge = implode(', ', $successMessageArr);
						//echo "---->".$implodeMeaasge;
						$this->sendNotificationMail($to='', 'Message:: Update Price & Qty on Mercadolibre', $implodeMeaasge);
					}
				}catch(Exception $e){
					$commonModel->saveLogger($this->moduleName, "Exception", $this->fileName, $e->getMessage());
					$commonModel->sendNotificationMail($this->to, 'Exception::getMLInventoryPriceUpdate Action', $e->getMessage());
				}
			} else {
				echo "Cron job not reqiired for update.";
			}
		} 
		
	 protected function _getStore()
		{
			$storeId = (int) Mage::app()->getRequest()->getParam('store',Mage::helper('items')-> getMlDefaultStoreId());
			return Mage::app()->getStore($storeId);
		}
		
	public function createNotificationApplication(){
			$appNotifyStatus = 0;

			$storeGetId = Mage::app()-> getRequest()->getParam('store',Mage::helper('items')-> getMlDefaultStoreId());
			$data = Mage::app()->getStore($storeGetId);
			$storeId = $data->getData('store_id');
			
			$configVal = array();
			$checkNotification = 0;
			$db = Mage::getSingleton('core/resource')->getConnection('core_write');
			$configVal = $db->fetchCol("SELECT value from core_config_data where path = 'mlitems/notification/application/id' and scope_id = '".$storeId."'"); 
			
			if(count($configVal) > 0){
				$checkNotification = $configVal['0'];
			}
			
			$application_sellerid = 0;
			$configSellerId = $db->fetchCol("SELECT value from core_config_data where path = 'mlitems/notification/application/sellerid' and scope_id = '".$storeId."'"); 
			if(count($configSellerId)>0){
				$application_sellerid = $configSellerId['0'];
			}
			
			$sellerId = 0;
			$sellerIdArr = $db->fetchCol("SELECT seller_id from  mercadolibre_token_details WHERE store_id = '".$storeId."'");
			if(count($sellerIdArr) > 0){
				$sellerId = $sellerIdArr['0'];
			}
			if((!$checkNotification || ($application_sellerid != $sellerId))){
			
				try{
					$commonModel = Mage::getModel('items/common');
					if(Mage::helper('items')->getMlAccessToken($storeId)){
						$access_token = Mage::helper('items')->getMlAccessToken($storeId);
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
		
					/* Get User details */
					$service_url = $api_url.'/users/me?access_token='.$access_token;  
					$response = $commonModel ->meliConnect($service_url,'GET');
					$dataArr = array('site_id' => $response['json']['site_id'],
								   'name' => strtolower($response['json']['nickname']).'_'.$response['json']['site_id'].'_'.$response['json']['id'].'_'.time(),
								   'description' => 'Application to receive notifications from MercadoLibre',
								   'short_name' => $response['json']['id'].'_'.time(),
								   'url' => Mage::getBaseUrl(),
								   'callback_url' => Mage::getBaseUrl(),	
								   'notifications_callback_url' => Mage::getBaseUrl().'items/index/notifications',
								   'notifications_topics' => array('items','orders','questions'),
							);
					
					/* Post Call to create application to receive notifications */   
					$requestData = json_encode($dataArr); 
					$service_url_applications = $api_url.'/applications?access_token='.$access_token;;
					$respApp = $commonModel ->meliConnect($service_url_applications,'POST',$requestData);
					if($respApp['statusCode'] == 201){
						$commonModel->sendNotificationMail($this->to, 'Application to receive notifications has been created successfully', 'Application to receive notifications has been created successfully.');
						//save the app ID to  core_config_data table
						$configSett = new Mage_Core_Model_Config();
						if(!$storeId){
							$configSett ->saveConfig('mlitems/notification/application/id', 1, 'default', "0");
							$configSett ->saveConfig('mlitems/notification/application/sellerid',$response['json']['id'], 'default', "0");
						} else {
							$configSett ->saveConfig('mlitems/notification/application/id', 1, 'stores', $storeId);
							$configSett ->saveConfig('mlitems/notification/application/sellerid',$response['json']['id'], 'stores', $storeId);
						}
						if(count($sellerIdArr) > 0){
								$db->query("UPDATE mercadolibre_token_details SET seller_id = '".$response['json']['id']."' WHERE store_id = '".$storeId."'");
						} else {
								$db->query("INSERT INTO mercadolibre_token_details SET seller_id = '".$response['json']['id']."', store_id = '".$storeId."'");
						}
						Mage::getSingleton('adminhtml/session')->addSuccess('An application to receive notifications from Mercadolibre has been created successfully.');
					}else{
						Mage::getSingleton('adminhtml/session')->addError('Please enter correct Mercadolibre\'s Client Id and Client Secret.');
					}
		
				} catch(Exception $e){
					$commonModel->saveLogger($this->moduleName, "Exception", $this->fileName, $e->getMessage());	
				}
		}		
	} 
	
	 public function getMageFinalCategoryIds($rootId){
	  			
			try{
				$cateArr = array();
				//$categories[] = $rootId;
				//$commonModel = Mage::getModel('items/common');

				$FilterCategory =  $this->getMageFilterCategoryIds($rootId); 
				
				/* Product Collection  */
				$collection = Mage::getResourceModel('catalog/product_collection');
				/* Join to get Product Categories & categories filrter  */
				$collection -> joinField('category_id','catalog/category_product','category_id','product_id=entity_id',null,'inner')
							-> addAttributeToFilter('category_id', array('in' => $FilterCategory))
							->getSelect()->group('e.entity_id')->having('count(e.entity_id) =1');

				if(count($collection->getData()) > 0){
					foreach($collection->getData() as $row){
						$categories[] = $row['category_id'];
					}
					$cateArr = array_unique($categories);
				}
			} catch(Exception $e){
					$this->saveLogger($this->moduleName, "Exception", $this->fileName, $e->getMessage());	
			}
			
			return $cateArr;

	  }
}