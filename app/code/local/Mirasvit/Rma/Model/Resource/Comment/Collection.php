<?php
class Mirasvit_Rma_Model_Resource_Comment_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{

    protected function _construct()
    {
        $this->_init('rma/comment');
    }

    public function toOptionArray($emptyOption = false)
    {
        $arr = $this->_toOptionArray('comment_id', 'name');
        if ($emptyOption) {
            array_unshift($arr, array('value' => 0, 'label' => Mage::helper('rma')->__('-- Please Select --')));
        }
        return $arr;
    }

    public function getOptionArray($emptyOption = false)
    {
        $arr = array();
        if ($emptyOption) {
            $arr[0] = Mage::helper('rma')->__('-- Please Select --');
        }
        foreach ($this as $item) {
            $arr[$item->getId()] = $item->getName();
        }
        return $arr;
    }


    protected function initFields()
    {
        $select = $this->getSelect();
        $select->joinLeft(array('status' => $this->getTable('rma/status')), 'main_table.status_id = status.status_id', array('status_name' => 'status.name'));
        $select->joinLeft(array('user' => $this->getTable('admin/user')), 'main_table.user_id = user.user_id', array('user_name' => 'CONCAT(user.firstname, user.lastname)'));
        // $select->joinLeft(array('customer' => $this->getTable('customer/entity')), 'main_table.customer_id = customer.customer_id', array('customer_name' => 'customer.name'));
        // $select->columns(array('is_replied' => new Zend_Db_Expr("answer <> ''")));
        $this->setOrder('created_at');
    }

    protected function _initSelect()
    {
        parent::_initSelect();
        $this->initFields();
    }

     /************************/

}