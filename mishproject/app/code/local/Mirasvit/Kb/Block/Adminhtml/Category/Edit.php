<?php
class Mirasvit_Kb_Block_Adminhtml_Category_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId    = 'category_id';
        $this->_blockGroup  = 'kb';
        $this->_controller  = 'adminhtml_category';
        $this->_mode        = 'edit';

        parent::__construct();
    }
}
