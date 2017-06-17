<?php


class NextBits_HelpDesk_Block_Adminhtml_Ticket extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_ticket";
	$this->_blockGroup = "helpdesk";
	$this->_headerText = Mage::helper("helpdesk")->__("Help Desk - Tickets");
	$this->_addButtonLabel = Mage::helper("helpdesk")->__("Add Ticket");
	parent::__construct();
	
	}

}