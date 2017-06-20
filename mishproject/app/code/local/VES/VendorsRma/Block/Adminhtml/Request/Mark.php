<?php

class VES_VendorsRma_Block_Adminhtml_Request_Mark extends Mage_Adminhtml_Block_Widget_Form_Container
{
    protected $_mode = 'mark';
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'mark_form';
        $this->_blockGroup = 'vendorsrma';
        $this->_controller = 'adminhtml_request';
        
        $this->removeButton("delete");
        $this->_updateButton('save', 'label', Mage::helper('vendorsrma')->__('Send'));
       // $this->_updateButton('delete', 'label', Mage::helper('vendorsrma')->__('Delete Type'));
       
        $this->_formScripts[] = "
            var loader_id = 'loading-mask';
            
            function previewTemplateCustomer(){
    	       var id = $('template').value;            
               var win = window.open('".$this->getPreviewUrl()."?template='+id, '_blank');
               win.focus();
            }
                    
            function previewTemplateVendor(){
    	       var id = $('template1').value;            
               var win = window.open('".$this->getPreviewUrl()."?template='+id, '_blank');
               win.focus();
            }
                                               
                                               
            function findTemplateContent(template_id,object){
                if(template_id == ''){
                    alert('please select template');
                    if(object == 'content_template'){
                             $('ves-preview-customer-template').addClassName('disabled');
                             $('ves-preview-customer-template').writeAttribute('disabled','disabled');
                    } 
                    else{
                             $('ves-preview-vendor-template').addClassName('disabled');
                             $('ves-preview-vendor-template').writeAttribute('disabled','disabled');
                    }
                }
			    else{
    				new Ajax.Request('".$this->getLoadUrl()."', {
    					  method:'post',
    					  parameters:{id: template_id},
    					  onCreate: function(obj) {
    						  Element.show(loader_id);
    						  },
    				      onComplete: function (transport) {
    							  Element.hide(loader_id);
    							  },
    					  onSuccess: function(transport) {
    					    	var response = transport;
    					    	var message = response.responseText;
    					    	$(object).update(message);
    				    
                			     if(object == 'content_template'){
                                         $('ves-preview-customer-template').removeClassName('disabled');
                                         $('ves-preview-customer-template').removeAttribute('disabled','');
                                } 
                                else{
                                         $('ves-preview-vendor-template').removeClassName('disabled');
                                         $('ves-preview-vendor-template').removeAttribute('disabled','');
                                }
    					  },
    					});
			     }    
            }
        ";
    }

    

    public function getPreviewUrl(){
        return $this->getUrl("adminhtml/rma_mestemplate/preview/",array("request"=>$this->getRequest()->getParam("id")));
    }
    
    public function getLoadUrl(){
        return $this->getUrl('*/*/loadTemplate');
    }
    
    public function getHeaderText()
    {
        return Mage::helper('vendorsrma')->__('Mark as Resolved');
    }
    
    public function getBackUrl()
    {
        return $this->getUrl('*/*/edit',array("id"=>$this->getRequest()->getParam("id")));
    }
}