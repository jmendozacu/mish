<?php

class Bluethink_Recommendation_Model_Mysql4_Recommendation extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the recommendation_id refers to the key field in your database table.
        $this->_init('recommendation/recommendation', 'recommendation_id');
    }
}