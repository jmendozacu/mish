<?php

class VES_VendorsVacation_Block_Adminhtml_Vacation_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'vendorsvacation';
        $this->_controller = 'adminhtml_vacation';
        $this->_removeButton('delete')->_removeButton('reset')->_removeButton('update')->_removeButton('save');
    }

    public function getHeaderText()
    {
        if( Mage::registry('vacation_data') ) {
            return Mage::helper('vendorsvacation')->__("View vacation of vendor '%s'", $this->htmlEscape(Mage::getModel('vendors/vendor')->load(Mage::registry('vacation_data')->getVendorId())->getVendorId()));
        }
    }
}