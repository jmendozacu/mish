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


class Mirasvit_Fpc_Model_Crawler_Crawl extends Varien_Object
{
    const USER_AGENT = 'FpcCrawler';

    protected $_status   = array();
    protected $_lockFile = null;

    public function run($verbose = false)
    {
        if (Mage::getSingleton('fpc/config')->getCrawlerEnabled() && !$this->isLocked()) {
            $this->lock();

            $this->addStatusMessage('start', sprintf(__('Started at: %s'), Mage::getSingleton('core/date')->date()));

            $config     = Mage::getSingleton('fpc/config');
            $collection = $this->getUrlCollection();
            $threads    = $config->getCrawlerMaxThreads();
            if (!$threads) {
                $threads = 1;
            }

            $i = 0;
            $urls = array();
            $totalCount = 0;
            while ($row = $collection->fetch()) {
                $cacheId = $row['cache_id'];
                $url     = $row['url'];

                if (!$this->checkCache($cacheId)) {
                    $urls[$row['url_id']] = $url;
                    $totalCount++;

                    if (count($urls) == $threads) {
                        $this->requestUrls($urls, $verbose);

                        $this->addStatusMessage('process', sprintf(__('Crawled %d urls', $totalCount)));
                        $urls = array();
                        usleep($config->getCrawlerThreadDelay());
                    }

                    if ($totalCount >= $config->getCrawlerMaxUrlsPerRun()) {
                        break;
                    }
                } else {
                    if ($verbose) {
                        echo 'IN CACHE '.$url.PHP_EOL;
                    }
                }
            }

            if (count($urls)) {
                $this->requestUrls($urls, $verbose);
                $this->addStatusMessage('process', sprintf(__('Crawled %d urls', $totalCount)));
            }

            $this->addStatusMessage('process', sprintf(__('Crawled %d urls', $totalCount)))
                ->addStatusMessage('finish', sprintf(__('Finished at: %s'), Mage::getSingleton('core/date')->date()));

            $this->unlock();
        }
    }

    public function getUrlCollection()
    {
        $resource = Mage::getSingleton('core/resource');
        $read     = $resource->getConnection('core_read');
        $select = $read->select()
            ->from($resource->getTableName('fpc/crawler_url', array('*')))
            ->order('rate desc');

        return $read->query($select);
    }

    public function checkCache($id)
    {
        $cache = Mirasvit_Fpc_Model_Cache::getCacheInstance();

        if ($cache->load($id)) {
            return true;
        }

        return false;
    }

    public function requestUrls($urls, $verbose = true)
    {
        $adapter    = new Varien_Http_Adapter_Curl();
        $options    = array(
            CURLOPT_USERAGENT => self::USER_AGENT,
            CURLOPT_HEADER    => true
        );

        $multiResult = $adapter->multiRequest($urls, $options);

        foreach ($multiResult as $urlId => $content) {
            $urlModel = Mage::getModel('fpc/crawler_url')->load($urlId);
            $this->_removeDublicates($urlModel);
            $matches = array();
            preg_match('/Fpc-Cache-Id: ('.Mirasvit_Fpc_Model_Processor::REQUEST_ID_PREFIX.'[a-z0-9]{32})/', $content, $matches);
            if (count($matches) == 2) {
                $cacheId = $matches[1];
                if ($urlModel->getCacheId() != $cacheId) {
                    $urlModel->setCacheId($cacheId)
                        ->save();
                }
                if ($verbose) {
                    echo 'CACHED '.$urls[$urlId].PHP_EOL;
                }
            } else {
                if ($verbose) {
                    echo 'REMOVED '.$urls[$urlId].PHP_EOL;
                }
                $urlModel->delete();
            }
        }

        return $this;
    }

    public function addStatusMessage($key, $message)
    {
        $this->_status[$key] = $message;

        Mage::helper('fpc')->setVariable('status', implode(PHP_EOL, $this->_status));

        return $this;
    }

    protected function _removeDublicates($urlModel)
    {
        $collection = Mage::getModel('fpc/crawler_url')->getCollection()
            ->addFieldToFilter('url_id', array('neq' => $urlModel->getId()))
            ->addFieldToFilter('url', $urlModel->getUrl());

        foreach ($collection as $url) {
            $url->delete();
        }

        $collection = Mage::getModel('fpc/crawler_url')->getCollection()
            ->addFieldToFilter('url_id', array('neq' => $urlModel->getId()))
            ->addFieldToFilter('cache_id', $urlModel->getCacheId());

        foreach ($collection as $url) {
            $url->delete();
        }

        return $this;
    }

    protected function _getLockFile()
    {
        if ($this->_lockFile === null) {
            $varDir = Mage::getConfig()->getVarDir('locks');
            $file   = $varDir.DS.'fpc_crawler.lock';

            if (is_file($file)) {
                $this->_lockFile = fopen($file, 'w');
            } else {
                $this->_lockFile = fopen($file, 'x');
            }
            fwrite($this->_lockFile, date('r'));
        }

        return $this->_lockFile;
    }

    public function lock()
    {
        flock($this->_getLockFile(), LOCK_EX | LOCK_NB);

        return $this;
    }

    public function lockAndBlock()
    {
        flock($this->_getLockFile(), LOCK_EX);

        return $this;
    }

    public function unlock()
    {
        flock($this->_getLockFile(), LOCK_UN);

        return $this;
    }

    public function isLocked()
    {
        $fp = $this->_getLockFile();
        if (flock($fp, LOCK_EX | LOCK_NB)) {
            flock($fp, LOCK_UN);

            return false;
        }

        return true;
    }

    public function __destruct()
    {
        if ($this->_lockFile) {
            fclose($this->_lockFile);
        }
    }
}