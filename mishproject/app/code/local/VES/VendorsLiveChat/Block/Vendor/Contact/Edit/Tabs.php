<?php

class VES_VendorsLiveChat_Block_Vendor_Contact_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('contact_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('vendorslivechat')->__('Contact Information'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('form_section', array(
            'label'     => Mage::helper('vendorslivechat')->__('Contact Information'),
            'title'     => Mage::helper('vendorslivechat')->__('Contact Information'),
            'content'   => $this->getLayout()->createBlock('vendorslivechat/vendor_contact_edit_tab_form')->toHtml(),
        ));

        return parent::_beforeToHtml();
    }
}