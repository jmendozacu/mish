<?php
class Etheme_Welldone_Block_Adminhtml_Fields_Csscolors extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        $url = $this->getUrl('adminhtml/system_config/edit/section/coolbabycolors');
        $html = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setType('button')
            ->setClass('scalable')
            ->setLabel('Colors, bg, fonts')
            ->setOnClick("setLocation('$url')")
            ->toHtml();
        return $html;
    }
}
?>