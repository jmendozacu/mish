<?php
class Mirasvit_Kb_Model_Resource_Category extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('kb/category', 'category_id');
    }

    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getIsMassDelete()) {
        }
        return parent::_afterLoad($object);
    }

    // protected function _beforeSave(Mage_Core_Model_Abstract $object)
    // {
    //     if (!$object->getId()) {
    //         $object->setCreatedAt(Mage::getSingleton('core/date')->gmtDate());
    //     }
    //     $object->setUpdatedAt(Mage::getSingleton('core/date')->gmtDate());
    //     if (!$urlKey = $object->getUrlKey()) {
    //         $urlKey = $object->getName();
    //     }
    //     $object->setUrlKey(Mage::helper('mstcore/urlrewrite')->normalize($urlKey));


    //     if ($object->getId() == 1) {
    //         $object->setParentId(null);
    //     } elseif ($object->getParentId() == $object->getId()) {
    //             throw new Mage_Exception("Can't move category to itself");
    //     } elseif ($object->getParentId() != $object->getOrigData('parent_id')) {
    //         $parentCategory = $object->getParentCategory();
    //         if (strpos($object->getPath(), $parentCategory->getPath()) == false) {
    //             $path = $parentCategory->getPath().'/'.$parentCategory->getId();
    //             $object->setPath($path);
    //             $object->setLevel(count(explode('/', $path))-1);

    //             $resource = Mage::getSingleton('core/resource');
    //             $writeConnection = $resource->getConnection('core_write');
    //             $query = "UPDATE {$resource->getTableName('kb/category')} SET path = REPLACE(path, '{$object->getOrigData('path')}', '$path')";
    //             $writeConnection->query($query);
    //         } else {
    //             throw new Mage_Exception("Can't move category to it's child");
    //         }
    //     }
    //     return parent::_beforeSave($object);
    // }

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

        if (!$object->getChildrenCount()) {
            $object->setChildrenCount(0);
        }

        if ($object->getLevel() === null) {
            $object->setLevel(1);
        }

        if (!$object->getId()) {
            $parentId = $object->getData('parent_id');
            if (!$parentId) {
                $parentId = 1; #Mage_Catalog_Model_Category::TREE_ROOT_ID;
            }
            $parentCategory = Mage::getModel('kb/category')->load($parentId);
            $object->setPath($parentCategory->getPath());

        }

        if (!$object->getId()) {
            $object->setPosition($this->_getMaxPosition($object->getPath()) + 1);
            $path  = explode('/', $object->getPath());
            $level = count($path);
            $object->setLevel($level + 1);

            if ($object->getParentId() == null) {
                $object->setLevel(1);
            }

            $object->setPath($object->getPath() . '/');

            $toUpdateChild = explode('/', $object->getPath());

            $this->_getWriteAdapter()->update(
                $this->getTable('kb/category'),
                array('children_count'  => new Zend_Db_Expr('children_count + 1')),
                array('category_id IN(?)' => $toUpdateChild)
            );
        }

        if ($object->getParentId() == 0) {
            $object->setParentId(null);
        }

        return parent::_beforeSave($object);
    }
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getIsMassStatus()) {
            Mage::helper('mstcore/urlrewrite')->updateUrlRewrite('KB', 'CATEGORY', $object, array('category_key'=>$object->getUrlKey()));

            if ($object->getPath() ==  '/') {
                $object->setPath($object->getId());
                $this->_savePath($object);
            } elseif (substr($object->getPath(), -1) == '/') {
                $object->setPath($object->getPath() . $object->getId());
                $this->_savePath($object);
            }
        }

        return parent::_afterSave($object);
    }

    protected function _beforeDelete(Mage_Core_Model_Abstract $object)
    {
        foreach ($object->getChildren() as $children) {
            $children->delete();
        }

        return parent::_beforeDelete($object);
    }

    public function getChildrenCount($categoryId)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('category'), 'children_count')
            ->where('category_id = :category_id');
        $bind = array('category_id' => $categoryId);

        return $this->_getReadAdapter()->fetchOne($select, $bind);
    }

    public function changeParent($category, $newParent, $afterItemId = null)
    {
        $childrenCount = $this->getChildrenCount($category->getId()) + 1;
        $table         = $this->getTable('category');
        $adapter       = $this->_getWriteAdapter();
        $levelFiled    = $adapter->quoteIdentifier('level');
        $pathField     = $adapter->quoteIdentifier('path');

        /**
         * Decrease children count for all old Item parent categories
         */
        $adapter->update(
            $table,
            array('children_count' => new Zend_Db_Expr('children_count - '.$childrenCount)),
            array('category_id IN(?)' => $category->getParentIds())
        );

        /**
         * Increase children count for new Item parents
         */
        $adapter->update(
            $table,
            array('children_count' => new Zend_Db_Expr('children_count + '.$childrenCount)),
            array('category_id IN(?)' => $newParent->getPathIds())
        );

        $position = $this->_processPositions($category, $newParent, $afterItemId);

        if ($newParent->getPath()) {
            $newPath = sprintf('%s/%s', $newParent->getPath(), $category->getId());
        } else {
            $newPath = $category->getId();
        }
        $newLevel         = $newParent->getLevel() + 1;
        $levelDisposition = $newLevel - $category->getLevel();

        /**
         * Update children nodes path
         */
        $adapter->update(
            $table,
            array(
                'path' => new Zend_Db_Expr('REPLACE('.$pathField.','.$adapter->quote($category->getPath().'/').', '.$adapter->quote($newPath.'/').')'),
                'level' => new Zend_Db_Expr($levelFiled.' + '.$levelDisposition)
            ),
            array($pathField.' LIKE ?' => $category->getPath().'/%')
        );

        /**
         * Update moved category data
         */
        $data = array(
            'path'      => $newPath,
            'level'     => $newLevel,
            'position'  => $position,
            'parent_id' => $newParent->getId()
        );
        $adapter->update($table, $data, array('category_id = ?' => $category->getId()));

        $category->addData($data);

        return $this;
    }

    protected function _savePath($object)
    {
        if ($object->getId()) {
            $this->_getWriteAdapter()->update(
                $this->getTable('category'),
                array('path' => $object->getPath()),
                array('category_id = ?' => $object->getId())
            );
        }
        return $this;
    }

    protected function _processPositions($category, $newParent, $afterItemId)
    {
        $table         = $this->getTable('category');
        $adapter       = $this->_getWriteAdapter();
        $positionField = $adapter->quoteIdentifier('position');

        $bind = array(
            'position' => new Zend_Db_Expr($positionField . ' - 1')
        );
        $where = array(
            'parent_id = ?'       => $category->getParentId(),
            $positionField.' > ?' => $category->getPosition()
        );
        $adapter->update($table, $bind, $where);

        /**
         * Prepare position value
         */

        if ($afterItemId) {
            $select = $adapter->select()
                ->from($table,'position')
                ->where('category_id = :category_id');
            $position = $adapter->fetchOne($select, array('category_id' => $afterItemId));

            $bind = array(
                'position' => new Zend_Db_Expr($positionField . ' + 1')
            );

            if (intval($newParent->getId()) == 0) {
                $where = array(
                    'parent_id IS NULL',
                    $positionField.' > ?' => $position
                );
            } else {
                $where = array(
                    'parent_id = ?' => $newParent->getId(),
                    $positionField.' > ?' => $position
                );
            }

            $adapter->update($table, $bind, $where);
        } elseif ($afterItemId !== null) {
            $position = 0;
            $bind = array(
                'position' => new Zend_Db_Expr($positionField . ' + 1')
            );

            if (intval($newParent->getId()) == 0) {
                $where = array(
                    'parent_id IS NULL',
                    $positionField.' > ?' => $position
                );
            } else {
                $where = array(
                    'parent_id = ?' => $newParent->getId(),
                    $positionField.' > ?' => $position
                );
            }

            $adapter->update($table,$bind,$where);
        } else {
            $select = $adapter->select()
                ->from($table,array('position' => new Zend_Db_Expr('MIN(' . $positionField. ')')))
                ->where('parent_id = :parent_id');
            $position = $adapter->fetchOne($select, array('parent_id' => $newParent->getId()));
        }
        $position += 1;

        return $position;
    }

    protected function _getMaxPosition($path)
    {
        $adapter       = $this->getReadConnection();
        $positionField = $adapter->quoteIdentifier('position');
        $level         = count(explode('/', $path));
        if ($path == '') {
            $level = 1;
            $path = '%';
        } else {
            $level++;
            $path .= '/%';
        }
        $bind = array(
            'c_level' => $level,
            'c_path'  => $path
        );
        $select  = $adapter->select()
            ->from($this->getTable('category'), 'MAX(' . $positionField . ')')
            ->where($adapter->quoteIdentifier('path') . ' LIKE :c_path')
            ->where($adapter->quoteIdentifier('level') . ' = :c_level');
        $position = $adapter->fetchOne($select, $bind);

        if (!$position) {
            $position = 0;
        }
        return $position;
    }

    protected function _afterDelete(Mage_Core_Model_Abstract $object)
    {
        Mage::helper('mstcore/urlrewrite')->removeUrlRewrite('KB', 'CATEGORY', $object);
        return parent::_afterDelete($object);
    }

    public function getArticlesNumber(Mage_Core_Model_Abstract $object, $storeId = 0)
    {
        $resource = Mage::getSingleton('core/resource');
        $storeId = (int)$storeId;

        $readConnection = $resource->getConnection('core_read');
        $query = "
        select count(distinct ac_article_id) from {$resource->getTableName('kb/category')}
        c left join {$resource->getTableName('kb/article_category')} ac
        ON c.`category_id` = ac.`ac_category_id`
        left join {$resource->getTableName('kb/article_store')} ast
        ON ac_article_id = ast.`as_article_id`
        left join {$resource->getTableName('kb/article')} art
        ON ac_article_id = art.article_id
        where path like '{$object->getPath()}%' and art.is_active=1"; ///and ast.`as_store_id` IN(0, $storeId)


        $num = (int)$readConnection->fetchOne($query);
        return $num;
    }
}