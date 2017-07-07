<?php

class Bluethink_Quesanswer_Model_Mysql4_Quesanswer extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the quesanswer_id refers to the key field in your database table.
        $this->_init('quesanswer/quesanswer', 'quesanswer_id');
    }
}