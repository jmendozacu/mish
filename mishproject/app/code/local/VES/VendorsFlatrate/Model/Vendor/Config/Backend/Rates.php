<?php
/**
 * Backend for serialized array data
 *
 */
class VES_VendorsFlatrate_Model_Vendor_Config_Backend_Rates extends Mage_Core_Model_Config_Data
{
    /**
     * Process data after load
     */
    protected function _afterLoad()
    {
        $value = $this->getValue();
        $arr   = @unserialize($value);

        if(!is_array($arr)) return '';

        // some cleanup
        foreach ($arr as $k => $val) {
            if(!is_array($val)) {
                unset($arr[$k]);
                continue;
            }
        }
        $this->setValue($arr);
    }
    
	public function cmp($a, $b) {
	   return $a['sort_order'] - $b['sort_order'];
	}


    /**
     * Prepare data before save
     */
    public function beforeSave()
    {
        $value = $this->getValue();
        unset($value['__empty']);
        usort($value, array($this,'cmp'));
        $value = serialize($value);
        $this->setValue($value);
    }
}
