<?php

class Mish_Personallogistic_Model_Mysql4_Personallogistic extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the personallogistic_id refers to the key field in your database table.
        $this->_init('personallogistic/personallogistic', 'personallogistic_id');
    }
}