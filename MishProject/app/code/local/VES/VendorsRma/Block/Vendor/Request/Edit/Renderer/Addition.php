<?php
class VES_VendorsRma_Block_Vendor_Request_Edit_Renderer_Addition extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface {
    public function __construct()
    {
        $this->setTemplate('ves_vendorsrma/request/edit/addition.phtml');
    }
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        return $this->toHtml();
    }
    public function getValue($value = null){
        if($value) return Mage::registry('request_data')->getData($value);
        return Mage::registry('request_data')->getData();
    }

}