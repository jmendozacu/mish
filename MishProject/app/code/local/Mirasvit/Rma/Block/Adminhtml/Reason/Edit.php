<?php
class Mirasvit_Rma_Block_Adminhtml_Reason_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct ()
    {
        parent::__construct();
        $this->_objectId = 'reason_id';
        $this->_controller = 'adminhtml_reason';
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

    public function getReason()
    {
        if (Mage::registry('current_reason') && Mage::registry('current_reason')->getId()) {
            return Mage::registry('current_reason');
        }
    }

    public function getHeaderText ()
    {
        if ($reason = $this->getReason()) {
            return Mage::helper('rma')->__("Edit Reason '%s'", $this->htmlEscape($reason->getName()));
        } else {
            return Mage::helper('rma')->__('Create New Reason');
        }
    }

    /************************/

}