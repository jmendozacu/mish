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
 * Entry admin edit tabs
 *
 * @category	Evirtual
 * @package		Evirtual_Autoimport
 * @author Ultimate Module Creator
 */
class Evirtual_Autoimport_Block_Adminhtml_Entry_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs{
	/**
	 * constructor
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function __construct(){
		parent::__construct();
		$this->setId('entry_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('autoimport')->__('Entry'));
	}
	/**
	 * before render html
	 * @access protected
	 * @return Evirtual_Autoimport_Block_Adminhtml_Entry_Edit_Tabs
	 * @author Ultimate Module Creator
	 */
	protected function _beforeToHtml(){
		$this->addTab('form_entry', array(
			'label'		=> Mage::helper('autoimport')->__('Entry'),
			'title'		=> Mage::helper('autoimport')->__('Entry'),
			'content' 	=> $this->getLayout()->createBlock('autoimport/adminhtml_entry_edit_tab_form')->toHtml(),
		));
		$this->addTab('form_mapping', array(
			'label'		=> Mage::helper('autoimport')->__('Entry Mapping'),
			'title'		=> Mage::helper('autoimport')->__('Entry Mapping'),
			'class' =>   'ajax',
    		'url'   =>   $this->getUrl('*/*/mapping',array('_current'=>true)),
			/*'content' 	=> $this->getLayout()->createBlock('autoimport/adminhtml_entry_edit_tab_mapping')->toHtml(),*/
		));
		if (!Mage::app()->isSingleStoreMode()){
			$this->addTab('form_store_entry', array(
				'label'		=> Mage::helper('autoimport')->__('Store views'),
				'title'		=> Mage::helper('autoimport')->__('Store views'),
				'content' 	=> $this->getLayout()->createBlock('autoimport/adminhtml_entry_edit_tab_stores')->toHtml(),
			));
		}
		return parent::_beforeToHtml();
	}
}