<?php
class Mirasvit_Kb_Block_Adminhtml_Category_Edit_Tab_Seo extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        parent::_prepareLayout();

        $form  = new Varien_Data_Form();

        if (Mage::registry('current_model')) {
            $model = Mage::registry('current_model');
        } else {
            $model = Mage::getModel('kb/category');
        }

        $fieldset = $form->addFieldset('seo_fieldset', array('legend'=> Mage::helper('kb')->__('Meta Information')));
        if ($model->getId()) {
            $fieldset->addField('category_id', 'hidden', array(
                'name'      => 'category_id',
                'value'     => $model->getId(),
            ));
        }
        $fieldset->addField('meta_title', 'text', array(
            'label'     => Mage::helper('kb')->__('Meta Title'),
            'name'      => 'meta_title',
            'value'     => $model->getMetaTitle(),

        ));
        $fieldset->addField('meta_keywords', 'textarea', array(
            'label'     => Mage::helper('kb')->__('Meta Keywords'),
            'name'      => 'meta_keywords',
            'value'     => $model->getMetaKeywords(),

        ));
        $fieldset->addField('meta_description', 'textarea', array(
            'label'     => Mage::helper('kb')->__('Meta Description'),
            'name'      => 'meta_description',
            'value'     => $model->getMetaDescription(),

        ));

        $this->setForm($form);
    }
}