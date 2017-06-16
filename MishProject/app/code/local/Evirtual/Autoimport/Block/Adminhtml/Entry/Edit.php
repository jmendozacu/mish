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
 * Entry admin edit block
 *
 * @category	Evirtual
 * @package		Evirtual_Autoimport
 * @author Ultimate Module Creator
 */
class Evirtual_Autoimport_Block_Adminhtml_Entry_Edit extends Mage_Adminhtml_Block_Widget_Form_Container{
	/**
	 * constuctor
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function __construct(){
		parent::__construct();
		$this->_blockGroup = 'autoimport';
		$this->_controller = 'adminhtml_entry';
		$this->_updateButton('save', 'label', Mage::helper('autoimport')->__('Save Entry'));
		$this->_updateButton('delete', 'label', Mage::helper('autoimport')->__('Delete Entry'));
		$this->_addButton('saveandcontinue', array(
			'label'		=> Mage::helper('autoimport')->__('Save And Continue Edit'),
			'onclick'	=> 'saveAndContinueEdit()',
			'class'		=> 'save',
		), -100);
		$this->_formScripts[] = "
			function saveAndContinueEdit(){
				editForm.submit($('edit_form').action+'back/edit/');
			}
		";
	}
	/**
	 * get the edit form header
	 * @access public
	 * @return string
	 * @author Ultimate Module Creator
	 */
	public function getHeaderText(){
		if( Mage::registry('entry_data') && Mage::registry('entry_data')->getId() ) {
			return Mage::helper('autoimport')->__("Edit Entry '%s'", $this->htmlEscape(Mage::registry('entry_data')->getTitle()));
		} 
		else {
			return Mage::helper('autoimport')->__('Add Entry');
		}
	}
}