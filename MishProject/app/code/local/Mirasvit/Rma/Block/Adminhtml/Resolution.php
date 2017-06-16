<?php
class Mirasvit_Rma_Block_Adminhtml_Resolution extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct ()
    {
        parent::__construct();
        $this->_controller = 'adminhtml_resolution';
        $this->_blockGroup = 'rma';
        $this->_headerText = Mage::helper('rma')->__('Resolution');
        $this->_addButtonLabel = Mage::helper('rma')->__('Add New Resolution');
    }

    public function getCreateUrl ()
    {
        return $this->getUrl('*/*/add');
    }

    /************************/

}