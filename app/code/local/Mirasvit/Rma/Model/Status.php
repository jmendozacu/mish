<?php
class Mirasvit_Rma_Model_Status extends Mage_Core_Model_Abstract
{
    const PACKAGE_SENT = 'package_sent';

    protected function _construct()
    {
        $this->_init('rma/status');
    }

    public function toOptionArray($emptyOption = false)
    {
    	return $this->getCollection()->setOrder('sort_order', 'asc')->toOptionArray($emptyOption);
    }


	/************************/

    public function __toString()
    {
        return $this->getName();
    }
}