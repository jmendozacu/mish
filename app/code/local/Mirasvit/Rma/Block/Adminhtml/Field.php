<?php
class Mirasvit_Rma_Block_Adminhtml_Field extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct ()
    {
        parent::__construct();
        $this->_controller = 'adminhtml_field';
        $this->_blockGroup = 'rma';
        $this->_headerText = Mage::helper('rma')->__('Custom Fields');
        $this->_addButtonLabel = Mage::helper('rma')->__('Add New Field');
    }

    public function getCreateUrl ()
    {
        return $this->getUrl('*/*/add');
    }

    /************************/

}