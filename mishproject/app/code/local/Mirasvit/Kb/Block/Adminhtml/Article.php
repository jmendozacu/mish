<?php
class Mirasvit_Kb_Block_Adminhtml_Article extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct ()
    {
        $this->_controller = 'adminhtml_article';
        $this->_blockGroup = 'kb';
        $this->_headerText = Mage::helper('kb')->__('Articles');
        $this->_addButtonLabel = Mage::helper('kb')->__('Add New Article');
        parent::__construct();
    }

    public function getCreateUrl ()
    {
        return $this->getUrl('*/*/add');
    }

    /************************/

}