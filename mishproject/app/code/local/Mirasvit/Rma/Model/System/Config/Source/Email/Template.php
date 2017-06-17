<?php
class Mirasvit_Rma_Model_System_Config_Source_Email_Template extends Mage_Adminhtml_Model_System_Config_Source_Email_Template {

    public function toOptionArray()
    {
        $options = parent::toOptionArray();
        array_unshift( $options,
            array(
                 'value' => 'none',
                 'label' => Mage::helper('rma')->__("- Disable these emails -")
            ));
        return $options;
    }

	/************************/

}