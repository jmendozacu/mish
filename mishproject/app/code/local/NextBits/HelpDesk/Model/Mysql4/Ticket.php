<?php
class NextBits_HelpDesk_Model_Mysql4_Ticket extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("helpdesk/ticket", "ticket_id");
    }
}