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
class Ves_FAQ_Block_Adminhtml_Question_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
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

        if (Mage::getSingleton('adminhtml/session')->getFAQData()) {
            $data = Mage::getSingleton('adminhtml/session')->getFAQData();
            Mage::getSingleton('adminhtml/session')->setFAQData(null);
        } elseif (Mage::registry('question_data')) {
            $data = Mage::registry('question_data')->getData();
        }

        $model = Mage::registry('question_data');

        if(isset($data['product_id'])){
            $data['product_id'] = 'product/'.$data['product_id'];
            $model->setData('product_id',$data['product_id']);
        }

        $fieldset = $form->addFieldset('question_form', array(
            'legend'=>Mage::helper('ves_faq')->__('General information')
            ));

        $fieldset->addField('title', 'text', array(
            'label'        => Mage::helper('ves_faq')->__('Title'),
            'class'        => 'required-entry',
            'required'    => true,
            'name'        => 'title',
            ));

        $fieldset->addField('status', 'select', array(
            'label'    => Mage::helper('ves_faq')->__('Status'),
            'name'     => 'status',
            'values'   => $model->getListStatus()
            ));

        $fieldset->addField('visibility', 'select', array(
            'label'     => Mage::helper('ves_faq')->__('Visibility'),
            'class'     => 'required-entry',
            'name'      => 'visibility',
            'values'   => Mage::getSingleton('ves_faq/visibility')->getOptionHash()
            ));

        $dateFormatIso = Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
        $fieldset->addField('created_at', 'date', array(
          'name'   => 'created_at',
          'label'  => Mage::helper('ves_faq')->__('Create At'),
          'title'  => Mage::helper('ves_faq')->__('Create At'),
          'image'  => $this->getSkinUrl('images/grid-cal.gif'),
          'input_format' => $dateFormatIso,
          'format'       => $dateFormatIso,
          'time' => true
          ));

        if(isset($data['author_name']) && $data['author_name'] !=''){
            $fieldset->addField('author_name', 'hidden', array(
                'name' => 'author_name',
                ));
            $data['author_name_label'] = $data['author_name'];
            $fieldset->addField('author_name_label', 'note', array(
             'label' => Mage::helper('ves_faq')->__('Author Name'),
             'name' => 'author_name_label',
             'text' => $data['author_name_label']
             ));
        }

        if(isset($data['author_email']) && $data['author_email'] !=''){
            $fieldset->addField('author_email', 'hidden', array(
                'name' => 'author_email',
                ));

            $data['author_email_label'] = $data['author_email'];
            $fieldset->addField('author_email_label', 'note', array(
             'label' => Mage::helper('ves_faq')->__('Author Email'),
             'name' => 'author_email_label',
             'text' => $data['author_email_label']
             ));
        }

        $helper = Mage::helper('ves_faq/chooser');

        $categoryConfig = array(
            'input_name'  => 'product_id',
            'input_label' => Mage::helper('ves_faq')->__('Product'),
            'button_text' => Mage::helper('ves_faq')->__('Select Product...'),
            'required'    => false
            );

        $helper->createProductChooser($model, $fieldset, $categoryConfig);

        $fieldset->addField('category_id', 'select', array(
            'label'    => Mage::helper('ves_faq')->__('Category'),
            'name'     => 'category_id',
            'values'   => Mage::helper('ves_faq')->getListCategory()
            ));

        $fieldset->addField('description', 'editor', array(
            'label'     => Mage::helper('ves_faq')->__('Description'),
            'required'  => false,
            'name'      => 'description',
            'style'     => 'width:600px;height:300px;',
            'wysiwyg'   => true,
            'config'   => $config
            ));

        $fieldset->addField('default_answer', 'editor', array(
            'label'     => Mage::helper('ves_faq')->__('Default Answer'),
            'note'    => Mage::helper('ves_faq')->__('Default Answer for the question'),
            'required'  => false,
            'name'      => 'default_answer',
            'style'     => 'width:600px;height:300px;',
            'wysiwyg'   => true,
            'config'   => $config
            ));

        $fieldset->addField('position', 'text', array(
            'label'     => Mage::helper('ves_faq')->__('Position'),
            'name'      => 'position',
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