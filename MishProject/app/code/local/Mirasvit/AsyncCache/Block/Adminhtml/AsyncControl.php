<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Asynchronous Cache
 * @version   1.0.0
 * @revision  125
 * @copyright Copyright (C) 2014 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_AsyncCache_Block_Adminhtml_AsyncControl extends Mage_Adminhtml_Block_Template
{
    public function getAsyncCollection()
    {
        $collection = Mage::getModel('asynccache/asynccache')->getCollection()
            ->addFieldToFilter('status', Mirasvit_AsyncCache_Model_Asynccache::STATUS_PENDING)
            ->setOrder('created_at', 'desc')
            ->setPageSize(10);

        return $collection;
    }

    public function getSavedTime()
    {
        $seconds = intval(Mage::helper('asynccache')->getVariableValue('time'));

        return Mage::helper('asynccache')->formatElapsedTime($seconds);
    }

    public function getLastExecutionTime()
    {
        $val = Mage::helper('asynccache')->getVariableValue('last_execution');

        if ($val) {
            return $val;
        } else {
            return '-';
        }
    }

    public function getStatus()
    {
        if (Mage::helper('asynccache/lock')->isLocked()) {
            return 'Processing';
        } else {
            return 'Waiting';
        }
    }
}