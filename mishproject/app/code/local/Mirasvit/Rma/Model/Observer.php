<?php
class Mirasvit_Rma_Model_Observer extends Mage_Core_Model_Abstract
{
	public function onHelpdeskProcessEmail($observer)
	{
		$event = $observer->getEvent();
		$ticket = $event->getTicket();
		$text = $event->getBody();
		if (!$rmaId = $ticket->getRmaId()) {
			return;
		}
		$rma = Mage::getModel('rma/rma')->load($rmaId);
		if (!$rma->getId()) {
			return;
		}
		$rma->addComment($text, false, $customer, $user, true, true, true, true);
	}
}
