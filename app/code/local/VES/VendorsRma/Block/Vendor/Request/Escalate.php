<?php

class VES_VendorsRma_Block_Vendor_Request_Escalate extends Mage_Adminhtml_Block_Widget_Form_Container
{
    protected $_mode = 'escalate';
    public function __construct()
    {
        parent::__construct();
                 
        
        
        $this->_objectId = 'escalate_form';
        $this->_blockGroup = 'vendorsrma';
        $this->_controller = 'vendor_request';
        
        $this->removeButton("delete");
        $this->_updateButton('save', 'label', Mage::helper('vendorsrma')->__('Save'));
        
       
    }

    public function getLoadUrl(){
        return $this->getUrl('*/*/loadTemplate');
    }
    
    public function getHeaderText()
    {
        return Mage::helper('vendorsrma')->__('Escalate to a claim');
    }
    
    public function getBackUrl()
    {
        return $this->getUrl('*/*/edit',array("id"=>$this->getRequest()->getParam("id")));
    }
    
    protected function _prepareLayout() {
        $return = parent::_prepareLayout();
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
        return $return;
    }
}