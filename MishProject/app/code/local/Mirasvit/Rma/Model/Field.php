<?php
class Mirasvit_Rma_Model_Field extends Mage_Core_Model_Abstract
{

    protected function _construct()
    {
        $this->_init('rma/field');
    }

    public function toOptionArray($emptyOption = false)
    {
    	return $this->getCollection()->toOptionArray($emptyOption);
    }


	/************************/

	public function getValues($emptyOption = false)
    {
    	$arr =  explode("\n", $this->getData('values'));
        $values = array();
        foreach ($arr as $value) {
            $value = explode('|', $value);
            if (count($value) >= 2) {
                $values[trim($value[0])] = trim($value[1]);
            }
        }
    	if ($emptyOption) {
            $res = array();
            $res[] = array('value'=>'', 'label'=> Mage::helper('rma')->__('-- Please Select --'));
            foreach ($values as $index => $value) {
                $res[] = array(
                   'value' => $index,
                   'label' => $value
                );
            }
            $values = $res;
        }
        return $values;
    }

    public function getVisibleCustomerStatus()
    {
        if (is_string($this->getData('visible_customer_status'))) {
            $this->setData('visible_customer_status', explode(",", $this->getData('visible_customer_status')));
        }

        return $this->getData('visible_customer_status');
    }

    protected function _beforeSave()
    {
        if (!$this->getId()) {
            $this->setIsNew(true);
        }
        return parent::_beforeSave();
    }

    protected function _afterSaveCommit()
    {
        parent::_afterSaveCommit();

        if ($this->getIsNew()) {
            $resource = Mage::getSingleton('core/resource');
            $writeConnection = $resource->getConnection('core_write');
            $tableName = $resource->getTableName('rma/rma');
            $query = "ALTER TABLE `{$tableName}` ADD `{$this->getCode()}` TEXT";
            $writeConnection->query($query);
            $writeConnection->resetDdlCache();
        }
    }

    protected function _beforeDelete()
    {
        $field = Mage::getModel('rma/field')->load($this->getId());
        $this->setDbCode($field->getCode());
        return parent::_beforeDelete();
    }

    protected function _afterDeleteCommit()
    {
        parent::_afterDeleteCommit();

        $resource = Mage::getSingleton('core/resource');
        $writeConnection = $resource->getConnection('core_write');
        $tableName = $resource->getTableName('rma/rma');
        $query = "ALTER TABLE `{$tableName}` DROP `{$this->getDbCode()}`";
        $writeConnection->query($query);
        $writeConnection->resetDdlCache();
    }
}