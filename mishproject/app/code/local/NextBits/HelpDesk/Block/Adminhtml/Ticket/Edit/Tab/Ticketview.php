<?php
class NextBits_HelpDesk_Block_Adminhtml_Ticket_Edit_Tab_Ticketview extends Mage_Adminhtml_Block_Template
{
	public function __construct() {
        parent::__construct();
        $this->setTemplate('helpdesk/ticketview.phtml');
    }
	 protected function _prepareLayout() {
        parent::_prepareLayout();
        $this->setChild('form_box', $this->getLayout()->createBlock('helpdesk/adminhtml_ticket_edit_tab_form'));
		$this->setChild('html_box', $this->getLayout()->createBlock('helpdesk/adminhtml_ticket_edit_tab_html'));
        return $this;
    }
}