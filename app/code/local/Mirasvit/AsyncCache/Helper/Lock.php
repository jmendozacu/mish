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


class Mirasvit_AsyncCache_Helper_Lock extends Mage_Core_Helper_Abstract
{
    protected static $_lockFile = null;

    /**
     * Get lock file resource
     *
     * @return resource
     */
    protected function _getLockFile()
    {
        if (self::$_lockFile === null) {
            $varDir = Mage::getConfig()->getVarDir('locks');
            $file = $varDir.DS.'asynccache.lock';

            if (is_file($file)) {
                self::$_lockFile = fopen($file, 'w');
            } else {
                self::$_lockFile = fopen($file, 'x');
            }
            fwrite(self::$_lockFile, date('r'));
        }
        return self::$_lockFile;
    }

    // public function _

    /**
     * Lock process without blocking.
     * This method allow protect multiple process runing and fast lock validation.
     *
     * @return Mirasvit_AsyncIndex_Model_Handler
     */
    public function lock()
    {
        flock($this->_getLockFile(), LOCK_EX | LOCK_NB);
        return $this;
    }

    /**
     * Lock and block process.
     * If new instance of the process will try validate locking state
     * script will wait until process will be unlocked
     *
     * @return Mirasvit_AsyncIndex_Model_Handler
     */
    public function lockAndBlock()
    {
        flock($this->_getLockFile(), LOCK_EX);
        return $this;
    }

    /**
     * Unlock process
     *
     * @return Mirasvit_AsyncIndex_Model_Handler
     */
    public function unlock()
    {
        flock($this->_getLockFile(), LOCK_UN);
        return $this;
    }

    /**
     * Check if process is locked
     *
     * @return bool
     */
    public function isLocked()
    {
        $fp = $this->_getLockFile();
        if (flock($fp, LOCK_EX | LOCK_NB)) {
            flock($fp, LOCK_UN);
            return false;
        }
        return true;
    }
}