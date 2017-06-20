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
 * Entry edit form tab
 *
 * @category	Evirtual
 * @package		Evirtual_Autoimport
 * @author Ultimate Module Creator
 */
class Evirtual_Autoimport_Block_Adminhtml_Entry_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form{	
	/**
	 * prepare the form
	 * @access protected
	 * @return Autoimport_Entry_Block_Adminhtml_Entry_Edit_Tab_Form
	 * @author Ultimate Module Creator
	 */
	protected function _prepareForm(){
		$form = new Varien_Data_Form();
		$form->setHtmlIdPrefix('entry_');
		$form->setFieldNameSuffix('entry');
		$this->setForm($form);
		$fieldset = $form->addFieldset('entry_form', array('legend'=>Mage::helper('autoimport')->__('Entry')));

		$fieldset->addField('title', 'text', array(
			'label' => Mage::helper('autoimport')->__('Title'),
			'name'  => 'title',
			'required'  => true,
			'class' => 'required-entry',

		));

		$fieldset->addField('type', 'select', array(
			'label' => Mage::helper('autoimport')->__('Type'),
			'name'  => 'type',
			'id'  => 'entrytype',
			'required'  => true,
			'class' => 'required-entry',
			'onchange'   => 'addUrlToCookie(this)',
			'values'=> array(
				array(
					'value' => Mage::helper('autoimport')->__(''),
					'label' => Mage::helper('autoimport')->__('Please select'),
				),
				array(
					'value' => Mage::helper('autoimport')->__('xml'),
					'label' => Mage::helper('autoimport')->__('XML'),
				),
				/*array(
					'value' => Mage::helper('autoimport')->__('csv'),
					'label' => Mage::helper('autoimport')->__('CSV'),
				),
				array(
					'value' => Mage::helper('autoimport')->__('xlsx'),
					'label' => Mage::helper('autoimport')->__('XLSX'),
				),*/
			),

		));

		/*$fieldset->addField('attributemapping', 'textarea', array(
			'label' => Mage::helper('autoimport')->__('Attribute Mapping'),
			'name'  => 'attributemapping',
			'required'  => true,
			'class' => 'required-entry',

		));*/

		$fieldset->addField('url', 'text', array(
			'label' => Mage::helper('autoimport')->__('Url'),
			'name'  => 'url',
			'id'  => 'entryUrl',
			'required'  => true,
			'class' => 'required-entry',
			'onchange'   => 'addUrlToCookie(this)',
			'after_element_html' => '<p class="note">' . $this->__('Url Like: http://www.google.com') . '</p>'

		));

		$fieldset->addField('catalogtype', 'select', array(
			'label' => Mage::helper('autoimport')->__('Catalog Type'),
			'name'  => 'catalogtype',
			'id'  => 'catalogtype',
			'required'  => true,
			'class' => 'required-entry',
			'onchange'   => 'addUrlToCookie(this)',
			'values'=> array(
				array(
					'value' => Mage::helper('autoimport')->__(''),
					'label' => Mage::helper('autoimport')->__('Please select'),
				),
				/*array(
					'value' => Mage::helper('autoimport')->__('category'),
					'label' => Mage::helper('autoimport')->__('Category'),
				),*/
				array(
					'value' => Mage::helper('autoimport')->__('product'),
					'label' => Mage::helper('autoimport')->__('Product'),
				),
				/*array(
					'value' => Mage::helper('autoimport')->__('stockupdate'),
					'label' => Mage::helper('autoimport')->__('Stock Update'),
				),*/
			),

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
            Mage::registry('current_entry')->setStoreId(Mage::app()->getStore(true)->getId());
		}
		if (Mage::getSingleton('adminhtml/session')->getEntryData()){
			$form->setValues(Mage::getSingleton('adminhtml/session')->getEntryData());
			Mage::getSingleton('adminhtml/session')->setEntryData(null);
		}
		elseif (Mage::registry('current_entry')){
			$form->setValues(Mage::registry('current_entry')->getData());
		}
		return parent::_prepareForm();
	}
}