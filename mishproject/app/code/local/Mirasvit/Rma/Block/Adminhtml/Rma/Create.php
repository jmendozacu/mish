<?php

class Mirasvit_Rma_Block_Adminhtml_Rma_Create extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_rma';
        $this->_mode = 'create';
        $this->_blockGroup = 'rma';

        parent::__construct();

        $this->setId('rma_rma_create');
        $this->removeButton('save');
        $this->removeButton('reset');
    }

    public function getHeaderText ()
    {
        return Mage::helper('rma')->__('Create New RMA');
    }
}