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


class Mirasvit_AsyncCache_Model_Handler
{
    protected $_timer    = 0;

    public function processQueue()
    {
        if (Mage::helper('asynccache/lock')->isLocked()) {
            return $this;
        }

        Mage::helper('asynccache/lock')->lock();
        $this->_startTime();

        $collection = Mage::getModel('asynccache/asynccache')->getCollection()
            ->addFieldToFilter('status', 'pending');

        $mergeCollection = $collection->merge();

        foreach ($collection as $asynccache) {
            $asynccache->setStatus(Mirasvit_AsyncCache_Model_Asynccache::STATUS_SUCCESS)
                ->save();
        }

        foreach ($mergeCollection as $item) {
            Mage::app()->getCache()->clean($item['mode'], $item['tags'], true);
        }


        $this->_stopTimer();
        Mage::helper('asynccache/lock')->unlock();

        return $this;
    }

    /**
     * Close file resource if it was opened
     */
    public function __destruct()
    {
        Mage::helper('asynccache/lock')->unlock();
    }

    protected function _startTime()
    {
        $this->_timer = microtime(true);
        $lastExecution = date("Y-m-d H:i:s", Mage::getModel('core/date')->timestamp(time()));
        Mage::helper('asynccache')->setVariableValue('last_execution', $lastExecution);
    }

    protected function _stopTimer()
    {
        $time = microtime(true) - $this->_timer;

        $crntValue = Mage::helper('asynccache')->getVariableValue('time');
        Mage::helper('asynccache')->setVariableValue('time', floatval($crntValue) + $time);

        return $this;
    }
}