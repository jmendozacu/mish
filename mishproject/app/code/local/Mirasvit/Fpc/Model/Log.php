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


class Mirasvit_Fpc_Model_Log extends Mage_Core_Model_Abstract
{
    const LOG_FILE = 'fpc.log';

    protected function _construct()
    {
        $this->_init('fpc/log');
    }

    public function log($cacheId, $isHit = 1)
    {
        if (Mage::helper('core/http')->getHttpUserAgent() == Mirasvit_Fpc_Model_Crawler_Crawl::USER_AGENT) {
            return true;
        }

        $logPath = Mage::getBaseDir('var').DS.'log'.DS.'fpc.log';
        $time    = microtime(true) - $_SERVER['FPC_TIME'];

        $data = array(
            $isHit,
            round($time, 5),
            self::_getFullUrl(),
            $cacheId
        );

        Mage::log(implode('|', $data), null, self::LOG_FILE, true);

        return true;
    }

    public function importFileLog()
    {
        $logPath  = Mage::getBaseDir('var').DS.'log';
        $filePath = $logPath.DS.self::LOG_FILE;

        if (!file_exists($filePath)) {
            return true;
        }

        @rename($filePath, $logPath.DS.'fpc_.log');

        $filePath = $logPath.DS.'fpc_.log';

        if (!file_exists($filePath)) {
            return true;
        }

        $handle   = fopen($filePath, 'r');
        if ($handle) {
            $resource   = Mage::getSingleton('core/resource');
            $connection = $resource->getConnection('core_write');
            $tableName  = Mage::getSingleton('core/resource')->getTableName('fpc/log');
            $rows       = array();
            while (($line = fgets($handle)) !== false) {
                $line = explode('):', $line);
                $line = explode('|', $line[1]);

                $rows[] = array(
                    'response_time' => $line[1],
                    'from_cache'    => $line[0],
                    'created_at'    => date('Y-m-d H:i:s'),
                );

                if (count($rows) > 100) {
                    $connection->insertArray($tableName, array('response_time', 'from_cache', 'created_at'), $rows);
                    $rows = array();
                }

                Mage::getSingleton('fpc/crawler_url')->saveUrl($line);
            }

            if (count($rows) > 0) {
                $connection->insertArray($tableName, array('response_time', 'from_cache', 'created_at'), $rows);
            }

            unlink($filePath);
        }

        return true;
    }

    private static function _getFullUrl()
    {
        $ssl      = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? true : false;
        $sp       = strtolower($_SERVER['SERVER_PROTOCOL']);
        $protocol = substr($sp, 0, strpos($sp, '/')) . (($ssl) ? 's' : '');
        $port     = $_SERVER['SERVER_PORT'];
        $port     = ((!$ssl && $port=='80') || ($ssl && $port=='443')) ? '' : ':'.$port;
        $host     = isset($_SERVER['HTTP_X_FORWARDED_HOST'])
            ? $_SERVER['HTTP_X_FORWARDED_HOST']
            : isset($_SERVER['HTTP_HOST'])? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];

        return $protocol.'://'.$host.$port.$_SERVER['REQUEST_URI'];
    }
}