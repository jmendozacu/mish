<?php

class VES_VendorsLiveChat_Block_Vendor_History_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('history_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('vendorslivechat')->__('View History'));
        $this->setTemplate('widget/tabshoriz.phtml');
    }

    protected function _beforeToHtml()
    {
        $this->addTab('form_section', array(
            'label'     => Mage::helper('vendorslivechat')->__('View History'),
            'title'     => Mage::helper('vendorslivechat')->__('View History'),
            'content'   => $this->getLayout()->createBlock('vendorslivechat/vendor_history_edit_tab_form')->toHtml(),
        ));

        return parent::_beforeToHtml();
    }
}