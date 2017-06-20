<?php

class Cartin24_Cmsimport_Block_Adminhtml_Cms_Import_Page_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
        $this->_blockGroup = 'cmsimport';
        $this->_controller = 'adminhtml_cms_import_page';        
        $this->_updateButton('save', 'label', Mage::helper('cmsimport')->__('Import'));	
        
    }

    public function getHeaderText()
    {
       
            return Mage::helper('cmsimport')->__('Import CMS Pages');
    }
}
