<?php
class Mirasvit_Rma_Model_Resolution extends Mage_Core_Model_Abstract
{

    protected function _construct()
    {
        $this->_init('rma/resolution');
    }

    public function toOptionArray($emptyOption = false)
    {
    	return $this->getCollection()->toOptionArray($emptyOption);
    }
 

	/************************/

}