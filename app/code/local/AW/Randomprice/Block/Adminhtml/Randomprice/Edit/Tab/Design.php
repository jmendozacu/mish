<?php
/**
* aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Randomprice
 * @version    1.0
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */


class AW_Randomprice_Block_Adminhtml_Randomprice_Edit_Tab_Design extends Mage_Adminhtml_Block_Widget_Form {

    /**
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm() {
        $model = Mage::registry('randomprice_data');
        $form = new Varien_Data_Form();
        $helper = Mage::helper('awrandomprice');

        $design_fieldset = $form->addFieldset('design_design', array(
            'legend' => $this->__('Design')
                ));

        $design_fieldset->addField('block_title', 'text', array(
            'name' => 'block_title',
            'label' => $this->__('Title'),
            'title' => $this->__('Title'),
            'required' => true,
        ));

        $template_fieldset = $form->addFieldset('template_fieldset', array(
            'legend' => $this->__('Template'),
                ));

        $template_fieldset->addField('addLink', 'button', array(
            'title' => $this->__('Insert link'),
            'class' => 'button add form-button',
            'name' => 'addTimer',
            'value' => $this->__('Insert link'),
            'onclick' => 'wysiwygtemplate.turnOff(); insertText($(\'template\'),\'{{price_link}}\')',
            'style' => 'min-width: 150px;',
            'read-only' => true
        ));

        $template_fieldset->addField('addTitle', 'button', array(
            'title' => $this->__('Insert link title'),
            'class' => 'button add form-button',
            'name' => 'addTitle',
            'value' => $this->__('Insert link title'),
            'onclick' => 'wysiwygtemplate.turnOff(); insertText($(\'template\'),\'{{link_title}}\')',
            'style' => 'min-width: 150px;',
            'read-only' => true
        ));


        $widgetFilters = array('is_email_compatible' => 1);
        $wysiwygConfig = Mage::getSingleton('cms/wysiwyg_config')->getConfig(
                array('widget_filters' => $widgetFilters));


        $wysiwygConfig->setData(
                Mage::helper('awrandomprice')->recursiveReplace(
                        '/admin_awrandomprice/', '/'
                        . (string) Mage::app()->getConfig()->getNode('admin/routers/adminhtml/args/frontName')
                        . '/', $wysiwygConfig->getData()
                )
        );



        $template_fieldset->addField('template', 'editor', array(
            'name' => 'template',
            'label' => $this->__('Template'),
            'note' => $this->__('HTML code is accepted.'),
            'style' => 'width:600px; height:300px;',
            'config' => $wysiwygConfig,
        ));

        $model->setData('addTitle', $this->__('Insert title'));
        $model->setData('addLink', $this->__('Insert link'));
        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

}