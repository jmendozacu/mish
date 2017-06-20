<?php
class Mirasvit_Kb_Model_Resource_Attachment_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{

    protected function _construct()
    {
        $this->_init('kb/attachment', 'attachment_id');
    }

    public function toOptionArray()
    {
        return $this->_toOptionArray('attachment_id', 'name');
    }

    public function getOptionArray()
    {
        $arr = array();
        foreach ($this as $item) {
            $arr[$item->getId()] = $item->getName();
        }
        return $arr;
    }


    // protected function initFields()
    // {
    //     $select = $this->getSelect();
    //     $select->joinLeft(array('user' => $this->getTable('admin/user')), 'main_table.user_id = user.user_id', array('user_name' => 'CONCAT(firstname, " ", lastname)'));
    //     $select->columns(array('is_replied' => new Zend_Db_Expr("answer <> ''")));
    // }

    // protected function _initSelect()
    // {
    //     parent::_initSelect();
    //     $this->initFields();
    // }
}