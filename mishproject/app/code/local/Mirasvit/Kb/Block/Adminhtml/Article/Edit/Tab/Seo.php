<?php
class Mirasvit_Kb_Block_Adminhtml_Article_Edit_Tab_Seo extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form  = new Varien_Data_Form();
        $this->setForm($form);
        $article = Mage::registry('current_article');

        $fieldset = $form->addFieldset('seo_fieldset', array('legend'=> Mage::helper('kb')->__('Meta Information')));
        if ($article->getId()) {
            $fieldset->addField('article_id', 'hidden', array(
                'name'      => 'article_id',
                'value'     => $article->getId(),
            ));
        }
        $fieldset->addField('meta_title', 'text', array(
            'label'     => Mage::helper('kb')->__('Meta Title'),
            'name'      => 'meta_title',
            'value'     => $article->getMetaTitle(),
        ));
        $fieldset->addField('meta_keywords', 'textarea', array(
            'label'     => Mage::helper('kb')->__('Meta Keywords'),
            'name'      => 'meta_keywords',
            'value'     => $article->getMetaKeywords(),
        ));
        $fieldset->addField('meta_description', 'textarea', array(
            'label'     => Mage::helper('kb')->__('Meta Description'),
            'name'      => 'meta_description',
            'value'     => $article->getMetaDescription(),
        ));
        return parent::_prepareForm();
    }

    /************************/

}