<?php
class VES_VendorsVacation_Block_Adminhtml_System_Config_Editor
    extends Mage_Adminhtml_Block_System_Config_Form_Field
    implements Varien_Data_Form_Element_Renderer_Interface {

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) {
        $element->setWysiwyg(true);
        $config = Mage::getSingleton('cms/wysiwyg_config')->getConfig();
        unset($config['add_variables']);
        unset($config['add_widgets']);
        $element->setConfig($config);
        return parent::_getElementHtml($element);
    }
}