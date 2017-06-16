<?php

class VES_VendorsRma_Block_Adminhtml_Status_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'vendorsrma';
        $this->_controller = 'adminhtml_status';
        
        $this->_updateButton('save', 'label', Mage::helper('vendorsrma')->__('Save Status'));


        $objId = $this->getRequest()->getParam($this->_objectId);
        $status = Mage::getModel("vendorsrma/status")->load($objId);

        if($status->getId() && $status->getData("is_delete")){
            $this->_removeButton('delete');
        }
        $this->_updateButton('save', 'onclick', 'vesrma_save()');
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
        function toggleEditor() {
            if (tinyMCE.getInstanceById('vendorsrma_content') == null) {
                tinyMCE.execCommand('mceAddControl', false, 'vendorsrma_content');
            } else {
                tinyMCE.execCommand('mceRemoveControl', false, 'vendorsrma_content');
            }
        }

        function vesrma_save(){
            $$('.hidden #main_tpl .req').each(
                    function (item){
                        item.removeClassName('validation-passed');
                        item.removeClassName('required-entry');
                    }
              );
            editForm.submit();
            $$('.hidden #main_tpl .req').each( function(item){ item.addClassName('required-entry'); });
        }

        function saveAndContinueEdit(){
              if($('edit_form').action.indexOf('back/edit/')<0)
                $('edit_form').action += 'back/edit/';

            vesrma_save();
        }

        function getRandomInt(min, max)   { return Math.floor(Math.random() * (max - min + 1)) + min;    }
             document.observe('dom:loaded', function() {
                $('new_template_button').observe('click', function(event) {
                    event.stop();
                    $$('.hidden #main_tpl .req').each(function(item){ item.removeClassName('validation-advice');item.setAttribute('id', item.getAttribute('id')+getRandomInt(0,100));});
                    $('store_templates').insert($('main_tpl').cloneNode(true));
                    $$('.hidden #main_tpl .req').each(function(item){item.setAttribute('id', item.getAttribute('id')+getRandomInt(0,100));});
                });
            });
          function ddel(s){
                if(s.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.previous()){
                        var e = s.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.previous();
                        if(e.hasClassName('entry-edit-head'))
                        s.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.previous().remove();
                }
                s.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.remove();
          }
        ";
    }


    public function getStatus(){
        return Mage::registry('status_data');
    }
    public function getHeaderText()
    {
        if( Mage::registry('status_data') && Mage::registry('status_data')->getId() ) {
            return Mage::helper('vendorsrma')->__("Edit RMA Status");
        } else {
            return Mage::helper('vendorsrma')->__('Add RMA Status');
        }
    }
}