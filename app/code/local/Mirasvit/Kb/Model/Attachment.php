<?php
class Mirasvit_Kb_Model_Attachment extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('kb/attachment');
    }

    public function toOptionArray($emptyOption = false)
    {
    	return $this->getCollection()->toOptionArray($emptyOption);
    }
 

	/************************/

}