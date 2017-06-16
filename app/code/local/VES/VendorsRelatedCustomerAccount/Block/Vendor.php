<?php
class VES_VendorsRelatedCustomerAccount_Block_Vendor extends Mage_Customer_Block_Form_Register{
	protected $_customer;
	protected $_vendor;
	/**
	 * Get customer session
	 * @return Mage_Customer_Model_Session
	 */
	protected function _getSession(){
		return Mage::getSingleton('customer/session');
	}
	
	/**
	 * Get current logged in customer.
	 * @return Mage_Customer_Model_Customer
	 */
	public function getCustomer(){
		if(!$this->_customer){
			$this->_customer = $this->_getSession()->getCustomer();
		}
		return $this->_customer;
	}
	
	/**
	 * Get current vendor account associates to current logged in customer.
	 * @return VES_Vendors_Model_Vendor
	 */
	public function getVendor(){
		if(!$this->_vendor){
			$this->_vendor = Mage::getModel('vendors/vendor')->loadByAttribute('customer_id',$this->getCustomer()->getId());
		}
		return $this->_vendor;
	}
	
	/**
	 * Check if current customer has vendor account or not
	 * @return boolean
	 */
	public function hasVendorAccount(){
		return $this->getVendor()->getId();
	}
	
	public function getStoreManagerUrl(){
		return $this->getUrl('vendors');
	}
	
	public function getPostActionUrl(){
		return $this->getUrl('customer/vendor/registerPost');
	}
	
	public function getFormData(){
		return new Varien_Object(Mage::getSingleton('customer/session')->getFormData());
	}
	/**
	 * vendor can register
	 * @return boolean
	 */
	public function vendorCanRegister(){
		return Mage::getStoreConfig('vendors/create_account/register');
	}
	/**
	 * Get vendor status label
	 * @param int $status
	 * @return string
	 */
	public function getStatusLabel($status){
		$labels = Mage::getModel('vendors/source_status')->getOptionArray();
		return isset($labels[$status])?$labels[$status]:status;
	}
	
	/**
	 * Can view store url and manage url
	 * @return boolean
	 */
	public function canViewStoreUrl(){
		return $this->getVendor()->getStatus() == VES_Vendors_Model_Vendor::STATUS_ACTIVATED;
	}
}
