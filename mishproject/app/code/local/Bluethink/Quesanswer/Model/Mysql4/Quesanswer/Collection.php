<?php

class Bluethink_Quesanswer_Model_Mysql4_Quesanswer_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('quesanswer/quesanswer');
    }
}