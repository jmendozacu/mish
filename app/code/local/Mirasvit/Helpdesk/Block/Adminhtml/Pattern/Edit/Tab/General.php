<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Help Desk MX
 * @version   1.1.0
 * @build     1285
 * @copyright Copyright (C) 2015 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_Helpdesk_Block_Adminhtml_Pattern_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form  = new Varien_Data_Form();
        $this->setForm($form);
        $pattern = Mage::registry('current_pattern');

        $fieldset = $form->addFieldset('edit_fieldset', array('legend'=> Mage::helper('helpdesk')->__('General Information')));
        if ($pattern->getId()) {
            $fieldset->addField('pattern_id', 'hidden', array(
                'name'      => 'pattern_id',
                'value'     => $pattern->getId(),
            ));
        }
        $fieldset->addField('name', 'text', array(
            'label'     => Mage::helper('helpdesk')->__('Title'),
            'name'      => 'name',
            'value'     => $pattern->getName(),
        ));
        $fieldset->addField('is_active', 'select', array(
            'label'     => Mage::helper('helpdesk')->__('Is Active'),
            'name'      => 'is_active',
            'value'     => $pattern->getIsActive(),
            'values'    => Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray()
        ));
        $fieldset->addField('scope', 'select', array(
            'label'     => Mage::helper('helpdesk')->__('Scope'),
            'name'      => 'scope',
            'value'     => $pattern->getScope(),
            'values'    => Mage::getSingleton('helpdesk/config_source_scope')->toOptionArray()
        ));
        $fieldset->addField('pattern', 'textarea', array(
            'label'     => Mage::helper('helpdesk')->__('Pattern'),
            'name'      => 'pattern',
            'value'     => $pattern->getPattern(),
        ));
        return parent::_prepareForm();
    }
}
