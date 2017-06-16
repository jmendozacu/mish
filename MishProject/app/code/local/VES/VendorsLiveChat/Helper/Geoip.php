<?php
require Mage::getBaseDir('code') .'/local/VES/VendorsLiveChat/lib/geoipcity.php' ;
require Mage::getBaseDir('code') .'/local/VES/VendorsLiveChat/lib/Geoipregionvars.php';
class VES_VendorsLiveChat_Helper_Geoip {
    private $gi = false;
	private $record ;
	//private $ip ;
    /**
     * An array of valid database
     *
     * @var array
     **/
    protected $_databases = array(
        'GeoIP',
		'GeoLiteCity'
    );
	
    function __construct() {
    }
	   /**
     * set Record
     * return record
     * @var database and ip
     **/
	public function setRecord($database,$ip){
		if(!in_array($database, $this->_databases))
            throw new Exception('GeoIP does not exist: '. $database);

        $filename = Mage::getBaseDir('code') .'/local/VES/VendorsLiveChat/lib/'. $database .'.dat';
		
        // Load database
        $this->gi = geoip_open($filename, GEOIP_STANDARD);
		$this->record = geoip_record_by_addr($this->gi ,$ip);

	}
    function __destruct() {
        $this->gi = geoip_close($this->gi);
    }
	 /**
     *get Record
     **/
	public function getRecord(){
		return $this->record ;
	}
	 /**
     *return country_code
     **/
    function getCountryCode() {
	   return $this->getRecord()->country_code;
    }
	 /**
     *return country_name
     **/
    function getCountryName() {
	   return $this->getRecord()->country_name;
    }
	/**
     *return region
    **/
	function getRegionCode() {
	   return $this->getRecord()->region;
    }
	/**
     *return region name
    **/
	function getRegionName() {
	   return $GEOIP_REGION_NAME[$this->getRecord()->country_code][$this->getRecord()->region];
    }
	/**
     *return city
    **/
	function getCity() {
	   return $this->getRecord()->city;
    }
	/**
     *return city
    **/
	function getFlags(){
		$filename= Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'ves_vendors/livechat/geoip';
        if($this->getCountryCode()){
            $link = $filename."/".$this->getCountryCode().'.png';
        }
        else{
            $link = $filename.'/unknow.png';
        }
		return $link;
	}
	/**
     *return postal_code
    **/
	function getPostalCode() {
	   return $this->getRecord()->postal_code;
    }
	/**
     *return latitude
    **/
	function getLatitude() {
	   return $this->getRecord()->latitude;
    }
	/**
     *return longitude
    **/
	function getLongitude() {
	   return $this->getRecord()->longitude;
    }
	/**
     *return metro_code
    **/
	function getMetroCode() {
	   return $this->getRecord()->metro_code;
    }
	/**
     *return area_code
    **/
	function getAreaCode() {
	   return $this->getRecord()->area_code;
    }
	/**
     *return continent_code
    **/
	function getContinentCode() {
	   return $this->getRecord()->continent_code;
    }
}
