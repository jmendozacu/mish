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


class Mirasvit_AsyncCache_Model_Mysql4_Asynccache extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('asynccache/asynccache', 'cache_id');
    }

    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if ($object->isObjectNew() && !$object->hasCreatedAt()) {
            $object->setCreatedAt(Mage::getSingleton('core/date')->gmtDate());
        }
        $object->setUpdatedAt(Mage::getSingleton('core/date')->gmtDate());

        if (!$object->hasData('status')) {
            $object->setStatus('pending');
        }

        return parent::_beforeSave($object);
    }

    public function cleanOld()
    {
        $adapter = $this->_getWriteAdapter();
        $adapter->delete($this->getMainTable(), array(
            'status = ?' => Mirasvit_AsyncCache_Model_Asynccache::STATUS_SUCCESS
        ));

        return $this;
    }
}
