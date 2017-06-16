<?php
class Mirasvit_Rma_Block_Adminhtml_Field_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct ()
    {
        parent::__construct();
        $this->_objectId = 'field_id';
        $this->_controller = 'adminhtml_field';
        $this->_blockGroup = 'rma';


        $this->_updateButton('save', 'label', Mage::helper('rma')->__('Save'));
        $this->_updateButton('delete', 'label', Mage::helper('rma')->__('Delete'));


        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('rma')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action + 'back/edit/');
            }
        ";

        return $this;
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
    }

    public function getField()
    {
        if (Mage::registry('current_field') && Mage::registry('current_field')->getId()) {
            return Mage::registry('current_field');
        }
    }

    public function getHeaderText ()
    {
        if ($field = $this->getField()) {
            return Mage::helper('rma')->__("Edit Field '%s'", $this->htmlEscape($field->getName()));
        } else {
            return Mage::helper('rma')->__('Create New Field');
        }
    }

    /************************/

}