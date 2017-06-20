<?php
class Mirasvit_Kb_Block_Adminhtml_Article_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form  = new Varien_Data_Form();
        $this->setForm($form);
        $article = Mage::registry('current_article');

        $fieldset = $form->addFieldset('edit_fieldset', array('class' => 'fieldset-wide', 'legend'=> Mage::helper('kb')->__('General Information')));
        if ($article->getId()) {
            $fieldset->addField('article_id', 'hidden', array(
                'name'      => 'article_id',
                'value'     => $article->getId(),
            ));
        }
        $fieldset->addField('name', 'text', array(
            'label'     => Mage::helper('kb')->__('Title'),
            'required'  => true,
            'name'      => 'name',
            'value'     => $article->getName(),

        ));
        $fieldset->addField('text', 'editor', array(
            'label'     => Mage::helper('kb')->__('Text'),
            'required'  => false,
            'name'      => 'text',
            'value'     => $article->getText(),
            'config'    => Mage::getSingleton('mstcore/wysiwyg_config')->getConfig(),
            'wysiwyg'   => true,
            'style'     => 'height:35em',
        ));
        $fieldset->addField('url_key', 'text', array(
            'label'     => Mage::helper('kb')->__('URL Key'),
            'name'      => 'url_key',
            'value'     => $article->getUrlKey(),

        ));
        $fieldset->addField('is_active', 'select', array(
            'label'     => Mage::helper('kb')->__('Is Active'),
            'name'      => 'is_active',
            'value'     => $article->getIsActive(),
            'values'    => Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray()

        ));
        if (!Mage::app()->isSingleStoreMode()) {
            $field = $fieldset->addField('store_id', 'multiselect', array(
                'name'      => 'store_ids[]',
                'label'     => Mage::helper('kb')->__('Store View'),
                'title'     => Mage::helper('kb')->__('Store View'),
                'required'  => true,
                'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
                'value'     => $article->getStoreIds()
            ));
        } else {
            $fieldset->addField('store_id', 'hidden', array(
                'name'      => 'store_id',
                'value'     => Mage::app()->getStore(true)->getId()
            ));
            $article->setStoreId(Mage::app()->getStore(true)->getId());
        }

        $fieldset->addField('user_id', 'select', array(
            'label'     => Mage::helper('kb')->__('Author'),
            'name'      => 'user_id',
            'value'     => $article->getUserId(),
            'values'    => Mage::helper('kb')->toAdminUserOptionArray()

        ));
        $tags = array();
        foreach ($article->getTags() as $tag) {
            $tags[] = $tag->getName();
        }
        $fieldset->addField('tags', 'text', array(
            'label'     => Mage::helper('kb')->__('Tags'),
            'name'      => 'tags',
            'value'     => implode(', ', $tags),

        ));
        return parent::_prepareForm();
    }
}