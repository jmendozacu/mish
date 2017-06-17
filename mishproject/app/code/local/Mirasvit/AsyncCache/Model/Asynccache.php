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


class Mirasvit_AsyncCache_Model_Asynccache extends Mage_Core_Model_Abstract
{
    const STATUS_PENDING = 'pending';
    const STATUS_SUCCESS = 'success';

    protected function _construct()
    {
        $this->_init('asynccache/asynccache');
    }

    public function getTagArray()
    {
        $tags = array();
        foreach (explode(',', $this->getTags()) as $tag) {
            $tag = trim($tag);
            if (!empty($tag)) {
                $tags[$tag] = $tag;
            }
        }
        sort($tags);
        return array_values($tags);
    }
}
