<?php
class Mirasvit_Rma_Block_Adminhtml_Condition extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct ()
    {
        parent::__construct();
        $this->_controller = 'adminhtml_condition';
        $this->_blockGroup = 'rma';
        $this->_headerText = Mage::helper('rma')->__('Condition');
        $this->_addButtonLabel = Mage::helper('rma')->__('Add New Condition');
    }

    public function getCreateUrl ()
    {
        return $this->getUrl('*/*/add');
    }

    /************************/

}