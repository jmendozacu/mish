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
 * Activity admin block
 *
 * @category	Evirtual
 * @package		Evirtual_Autoimport
 * @author Ultimate Module Creator
 */
class Evirtual_Autoimport_Block_Adminhtml_Activiy extends Mage_Adminhtml_Block_Widget_Grid_Container{
	/**
	 * constructor
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function __construct(){
		$this->_controller 		= 'adminhtml_activiy';
		$this->_blockGroup 		= 'autoimport';
		$this->_headerText 		= Mage::helper('autoimport')->__('Activity');
		$this->_addButtonLabel 	= Mage::helper('autoimport')->__('Add Activity');
		
		parent::__construct();
		$this->removeButton('add'); 
	}
}