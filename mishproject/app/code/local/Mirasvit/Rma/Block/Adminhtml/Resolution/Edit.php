<?php
class Mirasvit_Rma_Block_Adminhtml_Resolution_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct ()
    {
        parent::__construct();
        $this->_objectId = 'resolution_id';
        $this->_controller = 'adminhtml_resolution';
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

    public function getResolution()
    {
        if (Mage::registry('current_resolution') && Mage::registry('current_resolution')->getId()) {
            return Mage::registry('current_resolution');
        }
    }

    public function getHeaderText ()
    {
        if ($resolution = $this->getResolution()) {
            return Mage::helper('rma')->__("Edit Resolution '%s'", $this->htmlEscape($resolution->getName()));
        } else {
            return Mage::helper('rma')->__('Create New Resolution');
        }
    }

    /************************/

}