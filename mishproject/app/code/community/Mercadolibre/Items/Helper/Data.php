<?php

class Mercadolibre_Items_Helper_Data extends Mage_Core_Helper_Abstract
{
	public $datetimeformat = 'Y-m-d H:i:s';

 	 /*
	 * Return the current date in format provided
	 * 
	 * @params string
	 * @return string
	 */
	 
	 public function _getStore()
		{
			$storeId = (int) Mage::app()->getRequest()->getParam('store',Mage::helper('items')-> getMlDefaultStoreId());
			return Mage::app()->getStore($storeId);
		}
	 
	public function getAllParamsStr($getParamsArr,$exclude = array()){
			$params = '';
			foreach($getParamsArr as $paramKey => $paramVal){
				if(!in_array($paramKey,$exclude)){
					$params .= $paramKey.'/'.$paramVal.'/';
				}
			}
			
			return $params;
	}
	
	public function getMlDefaultStoreId(){
			$storeId ='';
			$defStoreViewId = 1;
			$cache = Mage::app()->getCache();
			if(Mage::app()->getRequest()->getParam('store')!=''){
				$ml_store_id = (int) Mage::app()->getRequest()->getParam('store');
				$cache->save((string)$ml_store_id, "ml_store_id", array("store_id_onchange"), 60*60);
			}
			
			if($cache->load('ml_store_id')!=''){
				$defStoreViewId = (int) $cache->load('ml_store_id');
			} else if(Mage::getStoreConfig("mlitems/defaultstoresettings/mldefaultstoreview",Mage::app()->getStore())){
				$defStoreViewId = Mage::getStoreConfig("mlitems/defaultstoresettings/mldefaultstoreview",Mage::app()->getStore());
			}
			return $defStoreViewId;
	} 

	
	public function getCurrentDateTime($format="")
	{
		if (empty($format)) {
			$format = $this->datetimeformat;
		}
		$store_id = Mage::app()->getStore()->getId(); 
		$storeTimestamp = Mage::app()->getLocale()->storeTimeStamp($store_id);
		$dt = date($format,$storeTimestamp);
		//print $dt = Mage::getModel('core/date')->date($format);die;
		return $dt;
	}
  
  	
	public function getMlAccessToken($storeId = 0){
			if(!$storeId){
				$storeId = (int) $this->_getStore()->getId();
			}
			$token_expiresVal  = 0;
			// Code to check the Refresh Token
			$commonModel = Mage::getModel('items/common');
			$write = Mage::getSingleton('core/resource')->getConnection('core_write');

			$token_expires = $write->fetchCol("SELECT token_expires from  mercadolibre_token_details WHERE store_id = '".$storeId."'");
			
			$changeaccesstoken = 0;
			if(Mage::getStoreConfig("mlitems/mltokenaccess/changeaccesstoken",Mage::app()->getStore($storeId))){
				$changeaccesstoken = Mage::getStoreConfig("mlitems/mltokenaccess/changeaccesstoken",Mage::app()->getStore($storeId));
			}
			
			if(isset($token_expires['0']) && $token_expires['0']){
				$token_expiresVal = $token_expires['0'];
			}

			if(trim($token_expiresVal) <= time() || $changeaccesstoken == 1){
				$commonModel -> getMLRefreshToken($storeId);
				if($changeaccesstoken == 1){
					$write->query("UPDATE core_config_data  set value = '0' where path='mlitems/mltokenaccess/changeaccesstoken'");
				}
			} 

			$token_expires = $write->fetchCol("SELECT access_token from  mercadolibre_token_details WHERE store_id = '".$storeId."'");
			return $token_expires['0'];
	}
	
	public function getMlSellerId(){
			// Get Seller Id
			$storeId = (int) $this->_getStore()->getId();	
			$commonModel = Mage::getModel('items/common');
			$write = Mage::getSingleton('core/resource')->getConnection('core_write');
			$token_expires = $write->fetchCol("SELECT seller_id from  mercadolibre_token_details WHERE store_id = '".$storeId."'");
			return $token_expires['0'];
	}
	
	
	public function getMlSiteId(){
			// Get ML country code 
			$mlSiteId = '';
			if(Mage::getStoreConfig("mlitems/mlmarketplaces/mlsiteid",Mage::app()->getStore())){
				$mlSiteId = Mage::getStoreConfig("mlitems/mlmarketplaces/mlsiteid",Mage::app()->getStore());
			}else{
				$mlSiteId = 'MLA';
			}
			return $mlSiteId;
	}
	
  /* Get category root_id */		
   public function getMLRootId($path_from_root){
   		if(count($path_from_root) > 1){
   			return $path_from_root[count($path_from_root)-2]['id'];
		} else {
			return false;
		}
   }
   	
   public function getMLebayDateToDateTime($ebay_date)
   {
		$old_date_format = strtotime($ebay_date);
		$new_date_format = date("Y-m-d H:i:s", $old_date_format);  
		return $new_date_format;
		//return trim(str_replace(array("T", "Z"), array(" ", ""), $ebay_date));

   }
	
    public function getMlXmlToArray($fileName = 'test')
    {
        $dir = Mage::getBaseDir('var').DS.'xml';	
        $xmlFile = $dir . DS . $fileName . '.xml';	
        if (file_exists($xmlFile) && is_readable($xmlFile)) {
            $xml  = simplexml_load_file($xmlFile);
            $data = $this->xmlToAssoc($xml);
            if (!empty($data)) {
                return $data;
            }
        }

    }
	
    public function getMlArrayToXml($array,$fileName = 'test', $rootName='config')
    {
        $xml = $this->assocToXml($array,$rootName);
        $dir = Mage::getBaseDir('var').DS.'xml';

        // prepare dir to save
        $parts = explode(DS, $fileName);
        array_pop($parts);
        $newDir = implode(DS, $parts);
        if ((!empty($newDir)) && (!is_dir($dir . DS . $newDir))) {
            if (!@mkdir($dir . DS . $newDir, 0777, true)) {
                return false;
            }
        }
        if (!@file_put_contents($dir . DS . $fileName . '.xml', $xml->asXML())) {
            return false;
        }
        return true;
    }
	
	/* creates a compressed zip file */
	function create_zip($files = array(),$destination = '',$overwrite = false) {
		//if the zip file already exists and overwrite is false, return false
		if(file_exists($destination) && !$overwrite) { return false; }
		//vars
		$valid_files = array();
		//if files were passed in...
		if(is_array($files)) {
			//cycle through each file
			foreach($files as $file) {
				//make sure the file exists
				if(file_exists($file)) {
					$valid_files[] = $file;
				}
			}
		}
		//if we have good files...
		if(count($valid_files)) {
			//create the archive
			$zip = new ZipArchive();
			if($zip->open($destination,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
				return false;
			}
			//add the files
			foreach($valid_files as $file) {
				$zip->addFile($file,$file);
			}
			//debug
			//echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;
			//close the zip -- done!
			$zip->close();
			
			//check to make sure the file exists
			return file_exists($destination);
		}
		else
		{
			return false;
		}
	}

     /**
     * Transform an assoc array to SimpleXMLElement object
     * Array has some limitations. Appropriate exceptions will be thrown
     * @param array $array
     * @param string $rootName
     * @return SimpleXMLElement
     * @throws Exception
     */
    public function assocToXml(array $array, $rootName = '_')
    {
        if (empty($rootName) || is_numeric($rootName)) {
            throw new Exception('Root element must not be empty or numeric');
        }

$xmlstr = <<<XML
<?xml version='1.0' encoding='UTF-8' standalone='yes'?>
<$rootName><$rootName>
XML;
        $xml = new SimpleXMLElement($xmlstr);
        foreach ($array as $key => $value) {
            if (is_numeric($key)) {
                throw new Exception('Array root keys must not be numeric.');
            }
        }
        return self::_assocToXml($array, $rootName, $xml);
    }

    /**
     * Function, that actually recursively transforms array to xml
     *
     * @param array $array
     * @param string $rootName
     * @param SimpleXMLElement $xml
     * @return SimpleXMLElement
     * @throws Exception
     */
    private function _assocToXml(array $array, $rootName, SimpleXMLElement &$xml)
    {
        $hasNumericKey = false;
        $hasStringKey  = false;
        foreach ($array as $key => $value) {
            if (!is_array($value)) {
                if (is_string($key)) {
                    if ($key === $rootName) {
                        throw new Exception('Associative key must not be the same as its parent associative key.');
                    }
                    $hasStringKey = true;
                    $xml->$key = $value;
                }
                elseif (is_int($key)) {
                    $hasNumericKey = true;
                    $xml->{$rootName}[$key] = $value;
                }
            }
            else {
                self::_assocToXml($value, $key, $xml->$key);
            }
        }
        if ($hasNumericKey && $hasStringKey) {
            throw new Exception('Associative and numeric keys must not be mixed at one level.');
        }
        return $xml;
    }
	
    public function xmlToAssoc(SimpleXMLElement $xml)
    {
        $array = array();
        foreach ($xml as $key => $value) {
            if (isset($value->$key)) {
                $i = 0;
                foreach ($value->$key as $v) {
                    $array[$key][$i++] = (string)$v;
                }
            }
            else {
                // try to transform it into string value, trimming spaces between elements
                $array[$key] = trim((string)$value);
                if (empty($array[$key]) && !empty($value)) {
                    $array[$key] = self::xmlToAssoc($value);
                }
                // untrim strings values
                else {
                    $array[$key] = (string)$value;
                }
            }
        }
        return $array;
    }
	
	
	
	
	

}