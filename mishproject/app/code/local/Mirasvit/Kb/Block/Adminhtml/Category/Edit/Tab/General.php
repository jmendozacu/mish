<?php
class Mirasvit_Kb_Block_Adminhtml_Category_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
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

        $fieldset = $form->addFieldset('edit_fieldset', array('legend'=> Mage::helper('kb')->__('General Information')));
        if ($model->getId()) {
            $fieldset->addField('category_id', 'hidden', array(
                'name'      => 'category_id',
                'value'     => $model->getId(),
            ));
        }
        $fieldset->addField('name', 'text', array(
            'label'     => Mage::helper('kb')->__('Title'),
            'required'  => true,
            'name'      => 'name',
            'value'     => $model->getName(),
        ));

        $fieldset->addField('url_key', 'text', array(
            'label'     => Mage::helper('kb')->__('URL Key'),
            'name'      => 'url_key',
            'value'     => $model->getUrlKey(),

        ));

        $fieldset->addField('is_active', 'select', array(
            'label'     => Mage::helper('kb')->__('Is Active'),
            'name'      => 'is_active',
            'value'     => $model->getIsActive(),
            'values'    => Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray()

        ));

        $fieldset->addField('sort_order', 'text', array(
            'label'     => Mage::helper('kb')->__('Sort Order'),
            'name'      => 'sort_order',
            'value'     => $model->getSortOrder(),

        ));

        if (!$model->getId()) {
            $parentId = $this->getRequest()->getParam('parent_id');
            $fieldset->addField('path', 'hidden', array(
                'name'  => 'path',
                'value' => $parentId
            ));
        } else {
            $fieldset->addField('path', 'hidden', array(
                'name'      => 'path',
                'value'     => $model->getPath(),
            ));
        }


        $this->setForm($form);
    }
}