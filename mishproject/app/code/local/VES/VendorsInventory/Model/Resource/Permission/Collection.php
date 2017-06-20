<?php

class VES_VendorsInventory_Model_Resource_Permission_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {

    protected function _construct() {
        $this->_init('vendorsinventory/permission');
    }

    public function getAllCanEditAdmins(){
        $idsSelect = clone $this->getSelect();
        $idsSelect->reset(Zend_Db_Select::ORDER);
        $idsSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $idsSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $idsSelect->reset(Zend_Db_Select::COLUMNS);
        $idsSelect->where('main_table.can_edit_warehouse=?',1);
        $idsSelect->columns('main_table.vendor_id');
        $idsSelect->resetJoinLeft();
        return $this->getConnection()->fetchCol($idsSelect);
    }
    
    /**
     * get admin id that can adjust stock of warehouse
     * @return array of admin id
     */
    public function getAllCanAdjustAdmins(){
        $idsSelect = clone $this->getSelect();
        $idsSelect->reset(Zend_Db_Select::ORDER);
        $idsSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $idsSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $idsSelect->reset(Zend_Db_Select::COLUMNS);
        $idsSelect->where('main_table.can_adjust=?',1);
        $idsSelect->columns('main_table.vendor_id');
        $idsSelect->resetJoinLeft();
        return $this->getConnection()->fetchCol($idsSelect);
    }   
    
    /**
     * get admin id that can send/request stock of warehouse
     * @return array of admin id
     */
    public function getAllCanSendRequestAdmins(){
        $idsSelect = clone $this->getSelect();
        $idsSelect->reset(Zend_Db_Select::ORDER);
        $idsSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $idsSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $idsSelect->reset(Zend_Db_Select::COLUMNS);
        $idsSelect->where('main_table.can_send_request_stock=?',1);
        $idsSelect->columns('main_table.vendor_id');
        $idsSelect->resetJoinLeft();
        return $this->getConnection()->fetchCol($idsSelect);
    }
}
