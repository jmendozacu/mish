<?php
class Etheme_Welldone_Block_Adminhtml_Fields_Cssupdate extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        $url = $this->getUrl('welldone/adminhtml_cssrefresh/',
            array(
                'website'=>Mage::app()->getRequest()->getParam('website'),
                'store'=>Mage::app()->getRequest()->getParam('store')
            ));
        $html = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setType('button')
            ->setClass('scalable')
            ->setLabel('Refresh CSS files')
            ->setOnClick("setLocation('$url')")
            ->toHtml();
        return $html;
    }
}
?>