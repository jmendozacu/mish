<?php
class Mirasvit_Kb_Model_Category extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('kb/category');
    }

    public function getUrl() {
    	return Mage::helper('mstcore/urlrewrite')->getUrl('KB', 'CATEGORY', $this);
    }

    // public function getParentIds()
    // {
    //     return array_diff($this->getPathIds(), array($this->getId()));
    // }

    // public function getPathIds()
    // {
    //     $ids = $this->getData('path_ids');
    //     if (is_null($ids)) {
    //         $ids = explode('/', $this->getPath());
    //         $this->setData('path_ids', $ids);
    //     }
    //     return $ids;
    // }

    // public function getParentCategory()
    // {
    // 	return Mage::getModel('kb/category')->load($this->getParentId());
    // }

    // public function hasChildren()
    // {
    //     return true;
    // }

    // public function getChildren()
    // {
    //     return $this->getCollection()
    //         ->addFieldToFilter('parent_id', $this->getId())
    //         ->addFieldToFilter('level', $this->getLevel() + 1);
    // }

    /**
     * Get parent category identifier
     *
     * @return int
     */
    public function getParentId()
    {
        // if ($this->getData('parent_id')) {
        //     return $this->getData('parent_id');
        // }
        $parentIds = $this->getParentIds();
        return intval(array_pop($parentIds));
    }

    /**
     * Get all parent categories ids
     *
     * @return array
     */
    public function getParentIds()
    {
        return array_diff($this->getPathIds(), array($this->getId()));
    }

    public function getPathIds()
    {
        $ids = $this->getData('path_ids');
        if (is_null($ids)) {
            $ids = explode('/', $this->getPath());
            $this->setData('path_ids', $ids);
        }
        return $ids;
    }

    public function hasChildren()
    {
        return $this->getChildrenCount() > 0 ? 1 : 0;
    }

    public function getChildren($store = null)
    {
        $collection = Mage::getModel('kb/category')->getCollection()
            ->addFieldToFilter('parent_id', $this->getId())
            ->addFieldToFilter('level', $this->getLevel() + 1)
            ->setOrder('position', 'asc');
        if ($store) {
            $collection->addStoreFilter($store);
        }
        return $collection;
    }

    public function getChildrenByType($type)
    {
        $collection = $this->getChildren()
            ->addFieldToFilter('type', $type);
        return $collection;
    }

    public function loadPathArray($path)
    {
        $result = array();
        $ids = explode('/', $path);
        foreach ($ids as $categoryId) {
            $result[] = Mage::getModel('kb/category')->load($categoryId);
        }
        return $result;
    }

    public function move($parentId, $afterItemId)
    {
        if ($parentId != null) {
            $parent = Mage::getModel('kb/category')
                ->load($parentId);

            if (!$parent->getId()) {
                Mage::throwException(
                    Mage::helper('kb')->__('Item move operation is not possible: the new parent category was not found.')
                );
            }
        } else {
            $parent = Mage::getModel('kb/category');
        }
        if (!$this->getId()) {
            Mage::throwException(
                Mage::helper('kb')->__('Item move operation is not possible: the current category was not found.')
            );
        } elseif ($parent && $parent->getId() == $this->getId()) {
            Mage::throwException(
                Mage::helper('kb')->__('Item move operation is not possible: parent category is equal to child category.')
            );
        }
        $this->setMovedItemId($this->getId());
        $this->getResource()->changeParent($this, $parent, $afterItemId);
        return $this;
    }

    public function getArticlesNumber($storeId)
    {
        return $this->getResource()->getArticlesNumber($this, $storeId);
    }
}