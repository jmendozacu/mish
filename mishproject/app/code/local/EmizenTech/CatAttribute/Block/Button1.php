<?php
class EmizenTech_CatAttribute_Block_Button1 extends Mage_Adminhtml_Block_System_Config_Form_Field
  {
    // protected function _construct()
    // {
    //     parent::_construct();
    //     $this->setTemplate('catattribute/button.phtml');
    // }

   protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        // $url = $this->getUrl('adminhtml/catattribute/sync'); 
        $url = $this->getUrl('adminhtml/catattribute/cat'); 

        $html = $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setType('button')
                ->setClass('scalable')
                ->setLabel('Sync')
                ->setOnClick("setLocation('$url')")
                ->toHtml();

      return $html;
    }
  }
?>