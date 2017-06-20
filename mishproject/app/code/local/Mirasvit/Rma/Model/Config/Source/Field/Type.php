<?php
class Mirasvit_Rma_Model_Config_Source_Field_Type
{

    public function toArray()
    {
        return array(
            Mirasvit_Rma_Model_Config::FIELD_TYPE_TEXT => Mage::helper('rma')->__('Text'),
            Mirasvit_Rma_Model_Config::FIELD_TYPE_TEXTAREA => Mage::helper('rma')->__('Multi-line text'),
            Mirasvit_Rma_Model_Config::FIELD_TYPE_DATE => Mage::helper('rma')->__('Date'),
            Mirasvit_Rma_Model_Config::FIELD_TYPE_CHECKBOX => Mage::helper('rma')->__('Checkbox'),
            Mirasvit_Rma_Model_Config::FIELD_TYPE_SELECT => Mage::helper('rma')->__('Drop-down list'),
        );
    }
    public function toOptionArray()
    {
        $result = array();
        foreach($this->toArray() as $k=>$v) {
            $result[] = array('value'=>$k, 'label'=>$v);
        }
        return $result;
    }

    /************************/
}