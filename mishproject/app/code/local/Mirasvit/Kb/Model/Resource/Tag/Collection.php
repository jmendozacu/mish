<?php
class Mirasvit_Kb_Model_Resource_Tag_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{

    protected function _construct()
    {
        $this->_init('kb/tag', 'tag_id');
    }

    public function toOptionArray()
    {
        return $this->_toOptionArray('tag_id', 'name');
    }

    public function getOptionArray()
    {
        $arr = array();
        foreach ($this as $item) {
            $arr[$item->getId()] = $item->getName();
        }
        return $arr;
    }

    public function joinNotEmptyFields()
    {
        $select = $this->getSelect();
        $select->joinRight(array('article_tag' => $this->getTable('kb/article_tag')), 'main_table.tag_id = at_tag_id', array('ratio' => 'count(main_table.tag_id)'))
            ->group('tag_id');
        return $this;
    }

    protected function _initSelect()
    {
        parent::_initSelect();
    }
}