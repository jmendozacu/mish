<?php
/**
 * Venustheme
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Venustheme EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.venustheme.com/LICENSE-1.0.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.venustheme.com/ for more information
 *
 * @category   Ves
 * @package    Ves_FAQ
 * @copyright  Copyright (c) 2014 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */

/**
 * Ves FAQ Extension
 *
 * @category   Ves
 * @package    Ves_FAQ
 * @author     Venustheme Dev Team <venustheme@gmail.com>
 */
class Ves_FAQ_Block_Adminhtml_Answer_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * prepare tab form's information
     *
     * @return Ves_FAQ_Block_Adminhtml_Faq_Edit_Tab_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);

        $config = Mage::getSingleton('cms/wysiwyg_config')->getConfig(
            array(
                'add_widgets' => false,
                'add_variables' => false,
                'add_images' => true,
                'encode_directives'             => false,
                'directives_url'                => Mage::getSingleton('adminhtml/url')->getUrl('*/cms_wysiwyg/directive'),
                'files_browser_window_url' => Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/cms_wysiwyg_images/index'),
                'files_browser_window_width' => (int) Mage::getConfig()->getNode('adminhtml/cms/browser/window_width'),
                'files_browser_window_height'=> (int) Mage::getConfig()->getNode('adminhtml/cms/browser/window_height')
                )
            );
        
        if (Mage::getSingleton('adminhtml/session')->getAnswerData()) {
            $data = Mage::getSingleton('adminhtml/session')->getAnswerData();
            Mage::getSingleton('adminhtml/session')->setFAQData(null);
        } elseif (Mage::registry('answer_data')) {
            $data = Mage::registry('answer_data')->getData();
        }

        $model = Mage::registry('answer_data');

        $fieldset = $form->addFieldset('answer_form', array(
            'legend'=>Mage::helper('ves_faq')->__('General information')
            ));

        $fieldset->addField('question_id', 'select', array(
            'label'     => Mage::helper('ves_faq')->__('Question'),
            'class'     => 'required-entry',
            'name'      => 'question_id',
            'values'   => $model->getListQuestion()
            ));        

        $fieldset->addField('status', 'select', array(
            'label'     => Mage::helper('ves_faq')->__('Status'),
            'class'     => 'required-entry',
            'name'      => 'status',
            'values'   => $model->getListStatus()
            ));

        if($data['author_email']!=''){
            $fieldset->addField('author_name', 'note', array(
               'label' => Mage::helper('ves_faq')->__('Author Name'),
               'name' => 'author_name',
               'text' => $data['author_name']
               ));
        }

        if($data['author_email']!=''){
            $fieldset->addField('author_email', 'note', array(
               'label' => Mage::helper('ves_faq')->__('Author Email'),
               'name' => 'author_email',
               'text' => $data['author_email']
               ));
        }

        $fieldset->addField('answer_content', 'editor', array(
            'label'     => Mage::helper('ves_faq')->__('Content'),
            'required'  => false,
            'name'      => 'answer_content',
            'style'     => 'width:700px;height:400px;',
            'wysiwyg'   => true,
            'config'   => $config
            ));

        if (!Mage::app()->isSingleStoreMode()) {
            $fieldset->addField('store_id', 'multiselect', array(
                'name' => 'stores[]',
                'label' => Mage::helper('ves_faq')->__('Store View'),
                'title' => Mage::helper('ves_faq')->__('Store View'),
                'required' => true,
                'values' => Mage::getSingleton('adminhtml/system_store')
                ->getStoreValuesForForm(false, true),
                ));
        }
        else {
            $fieldset->addField('store_id', 'hidden', array(
                'name' => 'stores[]',
                'value' => Mage::app()->getStore(true)->getId()
                ));
        }

        $form->setValues($data);
        return parent::_prepareForm();
    }
}