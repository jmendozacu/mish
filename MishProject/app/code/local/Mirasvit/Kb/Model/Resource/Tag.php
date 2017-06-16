<?php
class Mirasvit_Kb_Model_Resource_Tag extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('kb/tag', 'tag_id');
    }

    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getIsMassDelete()) {
        }
        return parent::_afterLoad($object);
    }

    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getId()) {
            $object->setCreatedAt(Mage::getSingleton('core/date')->gmtDate());
        }
        $object->setUpdatedAt(Mage::getSingleton('core/date')->gmtDate());
        if (!$urlKey = $object->getUrlKey()) {
            $urlKey = $object->getName();
        }
        $object->setUrlKey(Mage::helper('mstcore/urlrewrite')->normalize($urlKey));
        return parent::_beforeSave($object);
    }

    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getIsMassStatus()) {
            Mage::helper('mstcore/urlrewrite')->updateUrlRewrite('KB', 'TAG', $object, array('tag_key'=>$object->getUrlKey()));
        }
        return parent::_afterSave($object);
    }

    protected function _afterDelete(Mage_Core_Model_Abstract $object)
    {
        Mage::helper('mstcore/urlrewrite')->removeUrlRewrite('KB', 'TAG', $object);
        return parent::_afterDelete($object);
    }
}