<?php
class Mirasvit_Rma_Model_Resource_Condition_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{

    protected function _construct()
    {
        $this->_init('rma/condition');
    }

    public function toOptionArray($emptyOption = false)
    {
        $arr = $this->_toOptionArray('condition_id', 'name');
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
        // $select->columns(array('is_replied' => new Zend_Db_Expr("answer <> ''")));
    }

    protected function _initSelect()
    {
        parent::_initSelect();
        $this->initFields();
    }

     /************************/

}