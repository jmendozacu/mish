<?php

class OTTO_Kbase_Model_Attribute_Source_Store extends Mage_Eav_Model_Entity_Attribute_Source_Table
{
    /**
     * Retrieve Full Option values array
     *
     * @return array
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {
            $store = Mage::getResourceModel('core/store_collection')->load()->toOptionArray();
            $data= array();
            foreach($store as $sto){
            	$data[] = $sto['value'];
            }
            $this->_options= $data;
        }
        return $this->_options;
    }
    public function getStoreName($id){
    	$store=Mage::getModel('core/store')->load($id);
    	return $store->getData('name');
    }
}