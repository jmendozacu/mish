<?php

class Mercadolibre_Items_Block_Adminhtml_Questions_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'items';
        $this->_controller = 'adminhtml_questions';
        
        
        $this->_updateButton('save', 'label', Mage::helper('items')->__('Post Answer'));
       // $this->_updateButton('delete', 'label', Mage::helper('items')->__('Delete Question'));
		//$this->_updateButton('save', 'label', Mage::helper('items')->__('Save Answer AS Template'));
		$this->removeButton('delete');
        
        $this->_addButton('saveastemplate', array(
            'label'     => Mage::helper('adminhtml')->__('Post Answer And Save AS Template'),
            'onclick'   => 'saveAsTemplate()',
            'class'     => 'save',
        ), -100);
        
        /*$this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);*/
        
        $this->_formScripts[] = "
			document.getElementById('question').disabled=true;
			
            function toggleEditor() {
                if (tinyMCE.getInstanceById('items_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'items_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'items_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
			
			function saveAsTemplate(){
				 editForm.submit($('edit_form').action+'saveas/template/');
			}
			
			function getAnswerTemplate(val){
				if(val == 'new_template'){
					document.getElementById('title').value = 'Enter Answer Template Title';
					document.getElementById('answer').disabled=false;
					document.getElementById('title').disabled=false;
					document.getElementById('answer').value = '';
				} else {
							new Ajax.Request('".$this->getUrl('*/*/getAnswerTemplateAjax')."id/'+val, {
							method:     'get',
							data:     'test',
							onSuccess: function(transport){
								var strRes = transport.responseText;
								var resArr = strRes.split('##Answer1Template2Id##');
								document.getElementById('title').value = resArr['0'];
								document.getElementById('answer').value = resArr['1'];
								document.getElementById('answer').disabled=true;
								document.getElementById('title').disabled=true;
							}
						});
				}
				
			}			
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('items_data') && Mage::registry('items_data')->getId() ) {
            return Mage::helper('items')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('items_data')->getTitle()));
        } else {
            return Mage::helper('items')->__('Answer The Question');
        }
    }
}