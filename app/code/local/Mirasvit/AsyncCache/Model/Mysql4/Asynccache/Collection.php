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


class Mirasvit_AsyncCache_Model_Mysql4_Asynccache_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('asynccache/asynccache');
    }

    public function merge()
    {
        $result         = array();
        $matchingAnyTag = array();
        foreach ($this as $item) {
            $mode = $item->getMode();
            $tags = $item->getTagArray();

            if ($mode == 'all') {
                return array(array('mode' => 'all', 'tags' => array()));
            } elseif ($mode == 'matchingAnyTag') {
                $matchingAnyTag = array_merge($matchingAnyTag, $tags);
            } elseif (($mode == 'matchingTag') && (count($tags) <= 1)) {
                $matchingAnyTag = array_merge($matchingAnyTag, $tags);
            } else {
                $result = $this->addCustomJob($result, $mode, $tags);
            }
        }
        $matchingAnyTag = array_unique($matchingAnyTag);
        if (count($matchingAnyTag) > 0) {
            $result[] = array(
                'mode' => 'matchingAnyTag',
                'tags' => $matchingAnyTag
            );
        }
        return $result;
    }

    protected function addCustomJob(array $jobs, $mode, $tags)
    {
        foreach ($jobs as $job) {
            if ($job['mode'] == $mode && $job['tags'] == $tags) {
                return $jobs;
            }
        }
        $jobs[] = array('mode' => $mode, 'tags' => $tags);

        return $jobs;
    }

}
