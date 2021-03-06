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
class Ves_FAQ_Block_Adminhtml_Category_Edit_Tab_Meta extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		$_model = Mage::registry('category_data');
		$form = new Varien_Data_Form();
		$this->setForm($form);

		$fieldset = $form->addFieldset('category_meta', array('legend'=>Mage::helper('ves_faq')->__('Meta Information')));

		$fieldset->addField('title', 'text', array(
			'label'     => Mage::helper('ves_faq')->__('Page Title'),
			'class'     => '',
			'required'  => false,
			'name'      => 'title',
			'wysiwyg'   => false
			));
		$fieldset->addField('meta_keywords', 'editor', array(
			'label'     => Mage::helper('ves_faq')->__('Meta Keywords'),
			'class'     => '',
			'required'  => false,
			'name'      => 'meta_keywords',
			'style'     => 'width:600px;height:100px;',
			'wysiwyg'   => false
			));
		$fieldset->addField('meta_description', 'editor', array(
			'label'     => Mage::helper('ves_faq')->__('Meta Description'),
			'class'     => '',
			'required'  => false,
			'name'      => 'meta_description',
			'style'     => 'width:600px;height:100px;',
			'wysiwyg'   => false
			));

		if ( Mage::getSingleton('adminhtml/session')->getBrandData() )
		{
			$form->setValues(Mage::getSingleton('adminhtml/session')->getBrandData());
			Mage::getSingleton('adminhtml/session')->getBrandData(null);
		} elseif ( Mage::registry('category_data') ) {
			$form->setValues(Mage::registry('category_data')->getData());
		}

		return parent::_prepareForm();
	}

}