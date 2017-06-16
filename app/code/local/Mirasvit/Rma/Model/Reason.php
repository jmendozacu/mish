<?php
class Mirasvit_Rma_Model_Reason extends Mage_Core_Model_Abstract
{

    protected function _construct()
    {
        $this->_init('rma/reason');
    }

    public function toOptionArray($emptyOption = false)
    {
    	return $this->getCollection()->toOptionArray($emptyOption);
    }
 

	/************************/

}