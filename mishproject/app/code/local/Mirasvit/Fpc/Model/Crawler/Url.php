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
 * @package   Full Page Cache
 * @version   1.0.1
 * @revision  181
 * @copyright Copyright (C) 2014 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_Fpc_Model_Crawler_Url extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('fpc/crawler_url');
    }

    public function saveUrl($line)
    {
        if (count($line) != 4) {
            return $this;
        }

        $url     = $line[2];
        $cacheId = preg_replace('/\s+/', ' ', trim($line[3]));

        $collection = $this->getCollection();
        $collection->getSelect()->where('url = ?', $url);
        $model = $collection->getFirstItem();

        try {
            if (trim($cacheId) != '') {
                $model->setCacheId($cacheId)
                    ->setUrl($url)
                    ->setRate(intval($model->getRate()) + 1)
                    ->save();
            } elseif ($model->getId()) {
                $model->setRate(intval($model->getRate()) + 1)
                    ->save();
            }
        } catch (Exception $e) {}

        return $this;
    }
}