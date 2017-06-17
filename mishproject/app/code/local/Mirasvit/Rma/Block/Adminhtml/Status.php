<?php
class Mirasvit_Rma_Block_Adminhtml_Status extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct ()
    {
        parent::__construct();
        $this->_controller = 'adminhtml_status';
        $this->_blockGroup = 'rma';
        $this->_headerText = Mage::helper('rma')->__('Statuses');
        $this->_addButtonLabel = Mage::helper('rma')->__('Add New Status');
    }

    public function getCreateUrl ()
    {
        return $this->getUrl('*/*/add');
    }

    /************************/

}