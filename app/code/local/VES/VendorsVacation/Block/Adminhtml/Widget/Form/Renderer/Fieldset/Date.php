<?php
class VES_VendorsVacation_Block_Adminhtml_Widget_Form_Renderer_Fieldset_Date extends Mage_Adminhtml_Block_Template
    implements Varien_Data_Form_Element_Renderer_Interface
{
    public function getElement()
    {
        return $this->_element;
    }

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        return 'test';
    }
}
