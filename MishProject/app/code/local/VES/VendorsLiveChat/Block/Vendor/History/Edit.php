<?php

class VES_VendorsLiveChat_Block_Vendor_History_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'vendorslivechat';
        $this->_controller = 'vendor_history';

        $this->_updateButton('save', 'label', Mage::helper('vendorslivechat')->__('Save Contact'));
        $this->_updateButton('delete', 'label', Mage::helper('vendorslivechat')->__('Delete Contact'));

        $this->_removeButton('save');
        $this->_removeButton('delete');
        $this->_formScripts[] = '
             Event.observe(window,"load",function(){
                $$(".box-content-message-history").each(function(div){
                    div.scrollTop = div.scrollHeight;
                });
              });
        ';
    }

    public function getHeaderText()
    {
        if( Mage::registry('box_data') && Mage::registry('box_data')->getId() ) {
            return Mage::helper('vendorslivechat')->__("View History Chat With '%s'", $this->htmlEscape(Mage::registry('box_data')->getName()));
        } else {
            return Mage::helper('vendorslivechat')->__('Add Contact');
        }
    }
}