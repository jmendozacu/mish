<?php

class NBSSystem_Nitrogento_Model_Cache_Blockhtml_Observer extends Varien_Object
{
    protected function _construct()
    {
        $this->setLearningModeEnabled(Mage::getStoreConfig('nitrogento/cache_blockhtml/learning_mode'));
        $this->setCacheBlockhtmlEnabled(Mage::helper("nitrogento")->isCacheBlockhtmlEnabled());
    }
    
    // Retro compat with 1.2.6 without config clean cache, will be removed in 1.2.8
    public function decidePutBlockInCache($observer)
    {
        $this->handleBlockhtmlCache($observer);
    }
    
    public function handleBlockhtmlCache($observer)
    {
        // If block cache is disabled -> exit
        if (!$this->getData('cache_blockhtml_enabled'))
        {
            return;
        }
        
        $block = $observer->getEvent()->getBlock();
        $cacheBlockhtmlConfig = Mage::getSingleton('nitrogento/cache_blockhtml_config');
        
        if ($cacheBlockhtmlConfig->tryBlockMatchWithCacheBlockhtmlConfig($block))
        {
            if ($cacheHelper = Mage::helper($cacheBlockhtmlConfig->getHelperClass()))
            {
                $cacheHelper->handleBlockhtmlCache($block, $cacheBlockhtmlConfig->getCacheLifetime());
            }
        }
    }
    
    public function addBlockInLearningMode($observer)
    {
        if (!$this->getData('learning_mode_enabled'))
        {
            return;
        }
        
        // Retrieve current block
        $block = $observer->getEvent()->getBlock();
        Varien_Profiler::stop(Mage::helper('nitrogento')->getBlockTimerKey($block));
        
        if (!($block instanceof Mage_Core_Block_Template))
        {
            return;
        }
        
        $cacheBlockhtmlLearning = Mage::getModel('nitrogento/cache_blockhtml_learning');
        $cacheBlockhtmlLearning->addData(array(
            'store_id' => Mage::app()->getStore()->getId(),
            'block_class' => get_class($block),
            'block_template' => $block->getTemplate()
        ));
        
        $collection = $cacheBlockhtmlLearning->getCollection()
            ->addLearningFieldsToFilter($cacheBlockhtmlLearning)
            ->load();
        
        $timers = Varien_Profiler::getTimers();
        $blockTimer = $timers[Mage::helper('nitrogento')->getBlockTimerKey($block)];
        
        if (count($collection) == 0)
        {
            $cacheBlockhtmlLearning->setCount(1)->setTotalTime($blockTimer['sum'])->save();
        }
        else
        {
            $cacheBlockhtmlLearning->addData($collection->getFirstItem()->getData());
            $cacheBlockhtmlLearning->setCount($collection->getFirstItem()->getCount() + 1)
                ->setTotalTime($blockTimer['sum'] + $collection->getFirstItem()->getTotalTime())
                ->save();
        }
    }
    
    public function startTimer($observer)
    {
        if ($this->getData('learning_mode_enabled'))
        {
            Varien_Profiler::enable();
            Varien_Profiler::start(Mage::helper('nitrogento')->getBlockTimerKey($observer->getEvent()->getBlock()));
        }
    }
}