<?php
class Mercadolibre_Items_Block_Adminhtml_Feedbacks_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'items';
        $this->_controller = 'adminhtml_feedbacks';
         if( Mage::registry('items_data') && Mage::registry('items_data')->getId() ) {        
        	$this->_updateButton('save', 'label', Mage::helper('items')->__('Reply'));
		}else{
			$this->_updateButton('save', 'label', Mage::helper('items')->__('Post Now'));
		}
        $this->removeButton('delete');        
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);               
    }

    public function getHeaderText()
    {
        if( Mage::registry('items_data') && Mage::registry('items_data')->getId() ) {
            return Mage::helper('items')->__("Reply on '%s'", $this->htmlEscape(Mage::registry('items_data')->getTitle()));
        } else {
            return Mage::helper('items')->__('Feedback Form');
        }
    }
}