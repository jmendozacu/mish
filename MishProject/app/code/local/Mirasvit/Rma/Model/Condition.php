<?php
class Mirasvit_Rma_Model_Condition extends Mage_Core_Model_Abstract
{

    protected function _construct()
    {
        $this->_init('rma/condition');
    }

    public function toOptionArray($emptyOption = false)
    {
    	return $this->getCollection()->toOptionArray($emptyOption);
    }
 

	/************************/

}