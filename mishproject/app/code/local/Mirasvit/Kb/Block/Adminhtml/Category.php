<?php
class Mirasvit_Kb_Block_Adminhtml_Category extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct ()
    {
        $this->_controller = 'adminhtml_category';
        $this->_blockGroup = 'kb';
        $this->_headerText = Mage::helper('kb')->__('Categories');
        $this->_addButtonLabel = Mage::helper('kb')->__('Add New Category');
        parent::__construct();
    }

    public function getCreateUrl ()
    {
        return $this->getUrl('*/*/add');
    }

    /************************/

}