<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_File
 */
class Amasty_File_Model_Store extends Mage_Core_Model_Abstract
{
    public function _construct()
    {    
        $this->_init('amfile/store', 'id');
    }
}
