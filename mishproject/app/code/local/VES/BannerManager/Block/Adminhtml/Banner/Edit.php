<?php

class VES_BannerManager_Block_Adminhtml_Banner_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'bannermanager';
        $this->_controller = 'adminhtml_banner';
     //   //Mage::helper('ves_core');
        $this->_updateButton('save', 'label', Mage::helper('bannermanager')->__('Save Banner'));
        $this->_updateButton('delete', 'label', Mage::helper('bannermanager')->__('Delete Banner'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);
        if(($banner = Mage::registry('bannermanager_data')) && Mage::registry('bannermanager_data')->getId()){
	        $this->_addButton('add_item', array(
	            'label'     => Mage::helper('adminhtml')->__('Add Banner Item'),
	            'onclick'   => 'setLocation(\''.$this->getAddBannerItemUrl($banner).'\')',
	            'class'     => 'add',
	        ), -200);
        }
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('bannermanager_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'bannermanager_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'bannermanager_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }
    
	public function getAddBannerItemUrl($banner){
		return $this->getUrl('adminhtml/banner_item/new',array('banner'=>$banner->getId()));
	}
	
    public function getHeaderText()
    {
    	////Mage::helper('ves_core');
        if( Mage::registry('bannermanager_data') && Mage::registry('bannermanager_data')->getId() ) {
            return Mage::helper('bannermanager')->__("Edit Banner '%s'", $this->htmlEscape(Mage::registry('bannermanager_data')->getTitle()));
        } else {
            return Mage::helper('bannermanager')->__('Add Banner');
        }
    }
}