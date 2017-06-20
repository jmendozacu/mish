<?php
class Etheme_Welldone_Block_Adminhtml_Saveconfig_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
        $this->_controller = 'adminhtml_saveconfig';
        $this->_blockGroup = 'welldone';
        $this->_updateButton('save', 'label', Mage::helper('welldone')->__('Submit Action'));
        $this->_removeButton('back');
    }

    public function getHeaderText()
    {
        return Mage::helper('welldone')->__('Save current configuration');
    }
}

?>