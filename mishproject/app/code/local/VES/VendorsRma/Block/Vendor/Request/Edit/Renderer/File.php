<?php
class VES_VendorsRma_Block_Vendor_Request_Edit_Renderer_File extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface {
    public function __construct()
    {
        $this->setTemplate('ves_vendorsrma/request/file.phtml');
    }
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        return $this->toHtml();
    }
    protected function _prepareLayout()
    {
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'label' => Mage::helper('vendorsrma')->__('Add File'),
                'onclick' => "return requestOption.addNew('0')",
                'class' => 'add'
            ));
        $button->setName('add_file_item_button');

        $this->setChild('add_button', $button);
        return parent::_prepareLayout();
    }
    public function getAddButtonHtml()
    {
        return $this->getChildHtml('add_button');
    }
}


