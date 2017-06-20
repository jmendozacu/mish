<?php
class Mirasvit_Kb_Block_Adminhtml_Category_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('category_info_tabs');
        $this->setDestElementId('category_tab_content');
        $this->setTitle(Mage::helper('kb')->__('Category Data'));
        $this->setTemplate('widget/tabshoriz.phtml');
    }

    protected function _prepareLayout()
    {
        $this->addTab('general', array(
            'label'   => Mage::helper('kb')->__('General'),
            'content' => $this->getLayout()->createBlock(
                'kb/adminhtml_category_edit_tab_general',
                'edit.general'
            )->toHtml()
        ))->addTab('seo', array(
            'label'   => Mage::helper('kb')->__('Meta Information'),
            'content' => $this->getLayout()->createBlock(
                'kb/adminhtml_category_edit_tab_seo',
                'edit.seo'
            )->toHtml()
        ));

        return parent::_prepareLayout();
    }
}
