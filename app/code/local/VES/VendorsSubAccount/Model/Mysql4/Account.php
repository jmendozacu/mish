<?php

class VES_VendorsSubAccount_Model_Mysql4_Account extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the vendorssubaccount_id refers to the key field in your database table.
        $this->_init('vendorssubaccount/account', 'account_id');
    }
    
	/**
     * Check vendor scope, email and confirmation key before saving
     *
     * @param VES_Vendors_Model_Vendor $customer
     * @throws Mage_Customer_Exception
     * @return VES_Vendors_Model_Resource_Vendor
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $account)
    {
        parent::_beforeSave($account);

        if (!$account->getEmail()) {
            throw Mage::exception('VES_Vendors', Mage::helper('vendorssubaccount')->__('Account email is required'));
        }
		
    	if (!$account->getVendorId()) {
            throw Mage::exception('VES_Vendors', Mage::helper('vendorssubaccount')->__('Account vendor id is required'));
        }

        $adapter = $this->_getWriteAdapter();
        $bind    	= array('email' => $account->getEmail());
		$bind1		= array('username' => $account->getUsername());
        $select 	= $adapter->select()
		            ->from($this->getTable('vendorssubaccount/account'), array('account_id'))
		            ->where('email = :email');
       	$select1 = $adapter->select()
            ->from($this->getTable('vendorssubaccount/account'), array('account_id'))
            ->where('username = :username');

        if ($account->getId()) {
            $bind['account_id'] = (int)$account->getId();
            $bind1['account_id'] = (int)$account->getId();
            $select->where('account_id != :account_id');
            $select1->where('account_id != :account_id');
        }

    	$result1 = $adapter->fetchOne($select1, $bind1);
        if ($result1) {
            throw Mage::exception(
                'VES_Vendors', Mage::helper('vendorssubaccount')->__('This Subvendor username already exists'),
                VES_Vendors_Model_Vendor::EXCEPTION_VENDOR_ID_EXISTS
            );
        }
        
        $result = $adapter->fetchOne($select, $bind);
        if ($result) {
            throw Mage::exception(
                'VES_Vendors', Mage::helper('vendorssubaccount')->__('This Subvendor email already exists'),
                VES_Vendors_Model_Vendor::EXCEPTION_EMAIL_EXISTS
            );
        }        
        
    	
        return $this;
    }
    
	/**
     * Load sub account by username
     *
     * @throws Mage_Core_Exception
     *
     * @param VES_Vendors_Model_Vendor $vendor
     * @param string $vendorId
     * @param bool $testOnly
     * @return VES_Vendors_Model_Resource_Vendor
     */
    public function loadByUsername(VES_VendorsSubAccount_Model_Account $account, $username, $testOnly = false)
    {
        $adapter = $this->_getReadAdapter();
        $bind    = array('username' => $username);
        $select  = $adapter->select()
            ->from($this->getTable('vendorssubaccount/account'), array('account_id'))
            ->where('username = :username');

        $id = $adapter->fetchOne($select, $bind);
        if ($id) {
            $this->load($account, $id);
        } else {
            $account->setData(array());
        }

        return $this;
    }
}