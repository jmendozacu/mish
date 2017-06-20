<?php

class VES_VendorsSubAccount_Model_Account extends Mage_Core_Model_Abstract
{
	const STATUS_ENABLED 	= 1;
	const STATUS_DISABLED	= 0;
	const SPECIAL_CHAR		= '_';
	
	
	/**
     * Codes of exceptions related to customer model
     */
    const EXCEPTION_EMAIL_NOT_CONFIRMED       	= 1;
    const EXCEPTION_INVALID_EMAIL_OR_PASSWORD 	= 2;
    const EXCEPTION_EMAIL_EXISTS              	= 3;
    const EXCEPTION_VENDOR_ID_EXISTS          	= 4;
    const EXCEPTION_ACCOUNT_SUPPENDED			= 5;
    const EXCEPTION_ACCOUNT_PENDING				= 6;
    const EXCEPTION_VENDOR_ID_NOT_ACCEPTED		= 7;
    
    
	protected $_eventPrefix 	= 'vendor_subaccount';
    protected $_eventObject		= 'account';
	
    protected $_vendor;
    protected $_role;
    
    protected $_not_allowed_urls;
    protected $_menu_urls;
    
    public function _construct()
    {
        parent::_construct();
        $this->_init('vendorssubaccount/account');
    }

    /**
     * Get related vendor account
     * 
     * @return VES_Vendors_Model_Vendor
     */
	public function getVendor(){
		if(!$this->_vendor){
			$this->_vendor = Mage::getModel('vendors/vendor')->load($this->getVendorId());
		}
		return $this->_vendor;
	}
	
	/**
	 * Get sub account role
	 * @return VES_VendorsSubAccount_Model_Role
	 */
	public function getRole(){
		return Mage::getModel('vendorssubaccount/role')->load($this->getRoleId());
	}
	protected function _getMenuUrls($config){
		$urls = array();
		foreach($config as $key=>$data){
			if(isset($data['action'])) $urls[] = trim($data['action'],"/");
			if(isset($data['children'])) $urls = array_merge($urls,$this->_getMenuUrls($data['children']));
		}
		
		return $urls;
	}
	/**
	 * Get not allowed URLS
	 * @return array
	 */
	public function getNotAllowedUrls(){

		$notAllowedUrls = array();
		$allowedResources = $this->getRole()->getRoleResources();
		if($allowedResources[0]=='__root__') unset($allowedResources[0]);
		
		$config = Mage::getConfig()->getNode('vendors/menu')->asArray();
		if(!$this->_menu_urls) {
			$this->_menu_urls 	= $this->_getMenuUrls($config);
			$this->_menu_urls[] = 'vendors/config';
			$this->_menu_urls[] = 'vendors/message/inbox';
			$this->_menu_urls[] = 'vendors/message/outbox';
			$this->_menu_urls[] = 'vendors/message/trash';
		}
		
		$allowedUrls = array();
		foreach($allowedResources as $resource){
			$resource 	= explode("/", $resource);
			$key		= 'vendors/menu/'.implode("/children/", $resource).'/action';
			$url = trim(Mage::getConfig()->getNode($key),'/');
			$allowedUrls[] = $url;
		}
		if(isset($allowedResources['configuration'])) $allowedUrls[] = 'vendors/config';
		if(isset($allowedResources['message'])) {
			$allowedUrls[] = 'vendors/message/inbox';
			$allowedUrls[] = 'vendors/message/outbox';
			$allowedUrls[] = 'vendors/message/trash';
		}
		
		foreach($this->_menu_urls as $url){
			if(!in_array($url, $allowedUrls)){
				$notAllowedUrls[] = $url;
			}
		}

		return $notAllowedUrls;
	}
	
    /**
     * Hash account password
     *
     * @param   string $password
     * @param   int    $salt
     * @return  string
     */
    public function hashPassword($password, $salt = null)
    {
        return Mage::helper('core')->getHash($password, !is_null($salt) ? $salt : 2);
    }
    
    
    protected function _beforeSave(){
    	/*Hash subaccount password*/
    	if($this->getPassword()){
    		$this->setPasswordHash($this->hashPassword($this->getPassword()));
    	}
    }
    
	/**
     * Authenticate sub account
     *
     * @param  string $login (email, vendor_id)
     * @param  string $password
     * @throws Mage_Core_Exception
     * @return true
     *
     */
    public function authenticate($login, $password)
    {
    	/*Check if login is email*/
    	$validator = new Zend_Validate_EmailAddress();
    	$this->loadByUsername($login);
        
        if (!$this->validatePassword($password)) {
            throw Mage::exception('Mage_Core', Mage::helper('vendors')->__('Invalid login or password.'),
                self::EXCEPTION_INVALID_EMAIL_OR_PASSWORD
            );
        }

        
    	if ($this->getStatus() == self::STATUS_DISABLED) {
            throw Mage::exception('Mage_Core', Mage::helper('vendors')->__('Your vendor account has been suppended.'),
                self::EXCEPTION_ACCOUNT_SUPPENDED
            );
        }

        return true;
    }
	/**
     * Validate password with salted hash
     *
     * @param string $password
     * @return boolean
     */
    public function validatePassword($password)
    {
        $hash = $this->getPasswordHash();
        if (!$hash) {
            return false;
        }
        return Mage::helper('core')->validateHash($password, $hash);
    }
	/**
     * Load vendor by vendor id
     *
     * @param   string $vendorId
     * @return  VES_Vendor_Model_Vendor
     */
    public function loadByUsername($username)
    {
        $this->_getResource()->loadByUsername($this, $username);
        return $this;
    }
}