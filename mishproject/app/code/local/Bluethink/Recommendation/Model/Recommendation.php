<?php

class Bluethink_Recommendation_Model_Recommendation extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('recommendation/recommendation');
    }
}