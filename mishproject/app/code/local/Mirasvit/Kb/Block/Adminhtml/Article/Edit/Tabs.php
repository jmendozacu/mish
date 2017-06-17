<?php
class Mirasvit_Kb_Block_Adminhtml_Article_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('article_tabs');
        $this->setDestElementId('edit_form');
    }

    protected function _beforeToHtml()
    {
        $this->addTab('general_section', array(
            'label'     => Mage::helper('kb')->__('General Information'),
            'title'     => Mage::helper('kb')->__('General Information'),
            'content'   => $this->getLayout()->createBlock('kb/adminhtml_article_edit_tab_general')->toHtml(),
        ));

        $this->addTab('categories_section', array(
            'label'     => Mage::helper('kb')->__('Categories'),
            'title'     => Mage::helper('kb')->__('Categories'),
            'content'   => $this->getLayout()->createBlock('kb/adminhtml_article_edit_tab_categories')->toHtml(),
        ));

        $this->addTab('seo_section', array(
            'label'     => Mage::helper('kb')->__('Meta Information'),
            'title'     => Mage::helper('kb')->__('Meta Information'),
            'content'   => $this->getLayout()->createBlock('kb/adminhtml_article_edit_tab_seo')->toHtml(),
        ));

        $this->addTab('rating_section', array(
            'label'     => Mage::helper('kb')->__('Rating'),
            'title'     => Mage::helper('kb')->__('Rating'),
            'content'   => $this->getLayout()->createBlock('kb/adminhtml_article_edit_tab_rating')->toHtml(),
        ));

        return parent::_beforeToHtml();
    }

    /************************/

}