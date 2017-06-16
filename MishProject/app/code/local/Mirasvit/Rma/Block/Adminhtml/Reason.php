<?php
class Mirasvit_Rma_Block_Adminhtml_Reason extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct ()
    {
        parent::__construct();
        $this->_controller = 'adminhtml_reason';
        $this->_blockGroup = 'rma';
        $this->_headerText = Mage::helper('rma')->__('Reason');
        $this->_addButtonLabel = Mage::helper('rma')->__('Add New Reason');
    }

    public function getCreateUrl ()
    {
        return $this->getUrl('*/*/add');
    }

    /************************/

}