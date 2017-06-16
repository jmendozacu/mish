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
 * Activity admin edit tabs
 *
 * @category	Evirtual
 * @package		Evirtual_Autoimport
 * @author Ultimate Module Creator
 */
class Evirtual_Autoimport_Block_Adminhtml_Activiy_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs{
	/**
	 * constructor
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function __construct(){
		parent::__construct();
		$this->setId('activiy_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('autoimport')->__('Activity'));
	}
	/**
	 * before render html
	 * @access protected
	 * @return Evirtual_Autoimport_Block_Adminhtml_Activiy_Edit_Tabs
	 * @author Ultimate Module Creator
	 */
	protected function _beforeToHtml(){
		$this->addTab('form_activiy', array(
			'label'		=> Mage::helper('autoimport')->__('Activity'),
			'title'		=> Mage::helper('autoimport')->__('Activity'),
			'content' 	=> $this->getLayout()->createBlock('autoimport/adminhtml_activiy_edit_tab_form')->toHtml(),
		));
		if (!Mage::app()->isSingleStoreMode()){
			$this->addTab('form_store_activiy', array(
				'label'		=> Mage::helper('autoimport')->__('Store views'),
				'title'		=> Mage::helper('autoimport')->__('Store views'),
				'content' 	=> $this->getLayout()->createBlock('autoimport/adminhtml_activiy_edit_tab_stores')->toHtml(),
			));
		}
		return parent::_beforeToHtml();
	}
}