<?php
class Mirasvit_Kb_Model_Resource_Article extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('kb/article', 'article_id');
    }

    protected function loadStoreIds(Mage_Core_Model_Abstract $object)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('kb/article_store'))
            ->where('as_article_id = ?', $object->getId());
        if ($data = $this->_getReadAdapter()->fetchAll($select)) {
            $array = array();
            foreach ($data as $row) {
                $array[] = $row['as_store_id'];
            }
            $object->setData('store_ids', $array);
        }
        return $object;
    }

    protected function saveStoreIds($object)
    {
        $condition = $this->_getWriteAdapter()->quoteInto('as_article_id = ?', $object->getId());
        $this->_getWriteAdapter()->delete($this->getTable('kb/article_store'), $condition);
        foreach ((array)$object->getData('store_ids') as $id) {
            $objArray = array(
                'as_article_id'  => $object->getId(),
                'as_store_id' => $id
            );
            $this->_getWriteAdapter()->insert(
                $this->getTable('kb/article_store'), $objArray);
        }
    }

    protected function loadCategoryIds(Mage_Core_Model_Abstract $object)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('kb/article_category'))
            ->where('ac_article_id = ?', $object->getId());
        if ($data = $this->_getReadAdapter()->fetchAll($select)) {
            $array = array();
            foreach ($data as $row) {
                $array[] = $row['ac_category_id'];
            }
            $object->setData('category_ids', $array);
        } else {
            $object->setData('category_ids', array());
        }
        return $object;
    }

    protected function saveCategoryIds($object)
    {
        $condition = $this->_getWriteAdapter()->quoteInto('ac_article_id = ?', $object->getId());
        $this->_getWriteAdapter()->delete($this->getTable('kb/article_category'), $condition);
        foreach ((array)$object->getData('category_ids') as $id) {
            if ($id) {
                $objArray = array(
                    'ac_article_id'  => $object->getId(),
                    'ac_category_id' => $id
                );
                $this->_getWriteAdapter()->insert(
                    $this->getTable('kb/article_category'), $objArray);
            }
        }
    }

    protected function loadTagIds(Mage_Core_Model_Abstract $object)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('kb/article_tag'))
            ->where('at_article_id = ?', $object->getId());
        if ($data = $this->_getReadAdapter()->fetchAll($select)) {
            $array = array();
            foreach ($data as $row) {
                $array[] = $row['at_tag_id'];
            }
            $object->setData('tag_ids', $array);
        }
        return $object;
    }

    protected function saveTagIds($object)
    {
        $condition = $this->_getWriteAdapter()->quoteInto('at_article_id = ?', $object->getId());
        $this->_getWriteAdapter()->delete($this->getTable('kb/article_tag'), $condition);
        foreach ((array)$object->getData('tag_ids') as $id) {
            $objArray = array(
                'at_article_id'  => $object->getId(),
                'at_tag_id' => $id
            );
            $this->_getWriteAdapter()->insert(
                $this->getTable('kb/article_tag'), $objArray);
        }
    }

    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getIsMassDelete()) {
            $this->loadStoreIds($object);
            $this->loadCategoryIds($object);
            $this->loadTagIds($object);
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
            $this->saveStoreIds($object);
            $this->saveCategoryIds($object);
            $this->saveTagIds($object);
            Mage::helper('mstcore/urlrewrite')->updateUrlRewrite('KB', 'ARTICLE', $object, array('article_key'=>$object->getUrlKey(), 'category_key'=>$object->getCategory()->getUrlKey()));
        }
        return parent::_afterSave($object);
    }

    protected function _afterDelete(Mage_Core_Model_Abstract $object)
    {
        Mage::helper('mstcore/urlrewrite')->removeUrlRewrite('KB', 'ARTICLE', $object);
        return parent::_afterDelete($object);
    }
}