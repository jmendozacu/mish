<?php

class Bluethink_Quesanswer_Model_Quesanswer extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('quesanswer/quesanswer');
    }
}