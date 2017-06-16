<?php

    class Evince_Trackyourorder_Helper_Data extends Mage_Core_Helper_Abstract
    {
        public function getTrackyourorderUrl()
        {
            return $this->_getUrl('trackyourorder/index');
        }
}