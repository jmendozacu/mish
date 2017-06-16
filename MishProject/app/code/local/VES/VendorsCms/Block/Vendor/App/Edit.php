<?php

class VES_VendorsCms_Block_Vendor_App_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	/**
     * Initialize cms page edit block
     *
     * @return void
     */
    public function __construct()
    {
        $this->_objectId   = 'app_id';
        $this->_blockGroup = 'vendorscms';
        $this->_controller = 'vendor_app';

        parent::__construct();

        if ($this->_isAllowedAction('save')) {
            $this->_updateButton('save', 'label', Mage::helper('cms')->__('Save App'));
            $this->_addButton('saveandcontinue', array(
                'label'     => Mage::helper('adminhtml')->__('Save and Continue Edit'),
                'onclick'   => 'saveAndContinueEdit(\''.$this->_getSaveAndContinueUrl().'\')',
                'class'     => 'save-and-continue',
            ), -100);
        } else {
            $this->_removeButton('save');
        }

        if ($this->_isAllowedAction('delete')) {
            $this->_updateButton('delete', 'label', Mage::helper('cms')->__('Delete App'));
        } else {
            $this->_removeButton('delete');
        }
        if(!Mage::registry('cms_app')->getId() && !$this->getRequest()->getParam('type')){
        	$this->_removeButton('save');
        	$this->_removeButton('saveandcontinue');
        }
    }

    /**
     * Retrieve text for header element depending on loaded page
     *
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('cms_app')->getId()) {
            return Mage::helper('vendorscms')->__("Edit App '%s'", $this->escapeHtml(Mage::registry('cms_app')->getTitle()));
        }
        else {
            return Mage::helper('vendorscms')->__('New App');
        }
    }
    
	/**
     * Getter of url for "Save and Continue" button
     * tab_id will be replaced by desired by JS later
     *
     * @return string
     */
    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl('*/*/save', array(
            '_current'   => true,
            'back'       => 'edit',
            'active_tab' => '{{tab_id}}'
        ));
    }
    
    /**
     * Check permission for passed action
     *
     * @param string $action
     * @return bool
     */
    protected function _isAllowedAction($action)
    {
        return true;
    }
    
	/**
     * Prepare layout
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        $tabsBlock = $this->getLayout()->getBlock('cms_app_edit_tabs');
        if ($tabsBlock) {
            $tabsBlockJsObject = $tabsBlock->getJsObjectName();
            $tabsBlockPrefix   = $tabsBlock->getId() . '_';
        } else {
            $tabsBlockJsObject = 'app_tabsJsTabs';
            $tabsBlockPrefix   = 'app_tabs_';
        }

        $this->_formScripts[] = "
       	 	var appTemplateSyntax = /(^|.|\\r|\\n)({{(\w+)}})/;
            function saveAndContinueEdit(urlTemplate) {
                var tabsIdValue = " . $tabsBlockJsObject . ".activeTab.id;
                var tabsBlockPrefix = '" . $tabsBlockPrefix . "';
                if (tabsIdValue.startsWith(tabsBlockPrefix)) {
                    tabsIdValue = tabsIdValue.substr(tabsBlockPrefix.length)
                }
                var template = new Template(urlTemplate, appTemplateSyntax);
                var url = template.evaluate({tab_id:tabsIdValue});
                editForm.submit(url);
            }
            function setSettings(urlTemplate,typeElement) {
            	if(!editForm.validate()) return;
				var template = new Template(urlTemplate,appTemplateSyntax);
				setLocation(template.evaluate({type:$(typeElement).value}));
			} 
        ";
        $app = Mage::registry('cms_app');
        if($app->getId()){
        	$options = $app->getOptions();
        	if(sizeof($options)){
        		$frontendInstancesData = array();
        		foreach($options as $option){
        			$data = json_decode($option->getValue(),true);
        			$newData = array();
        			foreach($data as $key=>$value){
        				$newData[] = array('field'=>$key, 'value'=>$value);
        			}
        			/*Add option  id as an item in option array*/
        			$newData[] = array('field'=>'option_id', 'value'=>$option->getId());
        			
        			$frontendInstancesData[] = array('type'=>$option->getCode(), 'options'=>$newData);
        		}
        		$this->_formScripts[] = '
        			frontendInstance.init('.json_encode($frontendInstancesData).');
        		';
        	}
        }
        return parent::_prepareLayout();
    }
}