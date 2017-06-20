<?php
class Mirasvit_Rma_Model_Resource_Item extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('rma/item', 'item_id');
    }

    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getIsMassDelete()) {
        }
        if ($options = $object->getProductOptions()) {
            $object->setProductOptions(@unserialize($options));
        }
        return parent::_afterLoad($object);
    }

    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getId()) {
            $object->setCreatedAt(Mage::getSingleton('core/date')->gmtDate());
        }
        $object->setUpdatedAt(Mage::getSingleton('core/date')->gmtDate());
        if ($options = $object->getProductOptions()) {
            $object->setProductOptions(@serialize($options));
        }
        return parent::_beforeSave($object);
    }

    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getIsMassStatus()) {
        }
        return parent::_afterSave($object);
    }

    /************************/

}