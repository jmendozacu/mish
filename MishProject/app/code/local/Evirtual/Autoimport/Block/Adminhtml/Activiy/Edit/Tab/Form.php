<?php
/**
 * Evirtual_Autoimport extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category   	Evirtual
 * @package		Evirtual_Autoimport
 * @copyright  	Copyright (c) 2013
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Activity edit form tab
 *
 * @category	Evirtual
 * @package		Evirtual_Autoimport
 * @author Ultimate Module Creator
 */
class Evirtual_Autoimport_Block_Adminhtml_Activiy_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form{	
	/**
	 * prepare the form
	 * @access protected
	 * @return Autoimport_Activiy_Block_Adminhtml_Activiy_Edit_Tab_Form
	 * @author Ultimate Module Creator
	 */
	protected function _prepareForm(){
		$form = new Varien_Data_Form();
		$form->setHtmlIdPrefix('activiy_');
		$form->setFieldNameSuffix('activiy');
		$this->setForm($form);
		$fieldset = $form->addFieldset('activiy_form', array('legend'=>Mage::helper('autoimport')->__('Activity')));

		$fieldset->addField('title', 'text', array(
			'label' => Mage::helper('autoimport')->__('Title'),
			'name'  => 'title',

		));

		$fieldset->addField('summary', 'textarea', array(
			'label' => Mage::helper('autoimport')->__('Summary'),
			'name'  => 'summary',

		));
		$fieldset->addField('status', 'select', array(
			'label' => Mage::helper('autoimport')->__('Status'),
			'name'  => 'status',
			'values'=> array(
				array(
					'value' => 1,
					'label' => Mage::helper('autoimport')->__('Enabled'),
				),
				array(
					'value' => 0,
					'label' => Mage::helper('autoimport')->__('Disabled'),
				),
			),
		));
		if (Mage::app()->isSingleStoreMode()){
			$fieldset->addField('store_id', 'hidden', array(
                'name'      => 'stores[]',
                'value'     => Mage::app()->getStore(true)->getId()
            ));
            Mage::registry('current_activiy')->setStoreId(Mage::app()->getStore(true)->getId());
		}
		if (Mage::getSingleton('adminhtml/session')->getActiviyData()){
			$form->setValues(Mage::getSingleton('adminhtml/session')->getActiviyData());
			Mage::getSingleton('adminhtml/session')->setActiviyData(null);
		}
		elseif (Mage::registry('current_activiy')){
			$form->setValues(Mage::registry('current_activiy')->getData());
		}
		return parent::_prepareForm();
	}
}