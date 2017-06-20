<?php


class Eadesigndev_Romcity_Block_Adminhtml_Romcity extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_romcity";
	$this->_blockGroup = "romcity";
	$this->_headerText = Mage::helper("romcity")->__("Romcity Manager");
	$this->_addButtonLabel = Mage::helper("romcity")->__("Add New Item");
	parent::__construct();
	
	}

}
