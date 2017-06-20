<?php

class VES_VendorsCms_Model_Resource_Appoption extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct()
    {    
        $this->_init('vendorscms/appoption', 'option_id');
    }
    /**
     * 
     * Check and delete frontend instance options
     * @param int $appId
     * @param array $optionIds
     */
    public function checkAndDeleteFrontendInstanceOptions($appId,$optionIds = array()){
    	$where = "app_id = ".$appId." AND code='frontend_instance'";
    	/*Delete only options which is not in $optionIds*/
    	if(sizeof($optionIds)) $where.=' AND option_id NOT IN ('.implode(",", $optionIds).')';
    	$this->_getWriteAdapter()->delete($this->getMainTable(),$where);
    }
}