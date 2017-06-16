<?php
class Mirasvit_Kb_Model_Tag extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('kb/tag');
    }
    public function getUrl() {
    	return Mage::helper('mstcore/urlrewrite')->getUrl('KB', 'TAG', $this);
    }
}