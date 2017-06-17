<?php
class NextBits_HelpDesk_Helper_Data extends Mage_Core_Helper_Abstract
{
	const XML_PATH_HELPDESK_EMAIL_TEMPLATE  = 'help_desk/email/email_template';
	const XML_PATH_HELPDESK_SENDER_EMAIL    = 'help_desk/email/help_desk_email';
	const XML_PATH_HELPDESK_SENDER_NAME     = 'help_desk/email/help_desk_name';
	const XML_PATH_ENABLED   = 'help_desk/email/enabled';
	 
	static public function isEnabled()
    {
        return Mage::getStoreConfig( self::XML_PATH_ENABLED );
    }
	static public function getAllStatus($mode='form')
		{
			if($mode=='form'){
			return array('Please Select Status'=>'Please Select Status','New'=>'New','Open'=>'Open','Closed'=>'Closed','Waiting For Customer'=>'Waiting For Customer','New And Open'=>'New And Open');
		}else{
			return array('New'=>'New','Open'=>'Open','Closed'=>'Closed','Waiting For Customer'=>'Waiting For Customer','New And Open'=>'New And Open');
		}
		}
	static public function getAllPriority($mode='form')
		{	
			if($mode=='form'){
				return array('Please Select Priority'=>'Please Select Priority','High'=>'High','Normal'=>'Normal','Low'=>'Low');
			}else{
				return array('High'=>'High','Normal'=>'Normal','Closed'=>'Closed','Low'=>'Low');
				}
            
		}
	 static public function sendMail($ticketId) 
		{  
		$ticket = Mage::getModel('helpdesk/ticket')->load($ticketId);
		$ticketId = $ticket->getData('ticket_id');
		$ticketSub = $ticket->getData('subject');
		$ticketCustomerId = $ticket->getData('customer_id');
		$ticketMessage = $ticket->getData('message');
		$comments = Mage::getModel('helpdesk/comment')->getCollection()->addFieldToFilter('ticket_id', $ticketId);
		$customerData = Mage::getModel('customer/customer')->load($ticketCustomerId);
		$customerEmail = $customerData->getData('email');
        try 
		{
			$commentHtml = "";
			foreach($comments as $comment){
				$commentData = $comment->getData('comment');
				$commentHtml .= $commentData;	
				$commentHtml .= "<br />";						
			}			
			$postObject = new Varien_Object();
			$post['ticket_id'] = $ticketId;
			$post['subject'] = $ticketSub;
			$post['message'] = $ticketMessage;
			$post['comment'] = $commentHtml;
			$postObject->setData($post);
			$mailTemplate = Mage::getModel('core/email_template');
			
            $mailTemplate->setDesignConfig(array('area' => 'frontend'))
						 ->setReplyTo($customerEmail)
						 ->sendTransactional(Mage::getStoreConfig(self::XML_PATH_HELPDESK_EMAIL_TEMPLATE),
						   array('name'=> Mage::getStoreConfig(self::XML_PATH_HELPDESK_SENDER_NAME),'email'=>Mage::getStoreConfig(self::XML_PATH_HELPDESK_SENDER_EMAIL)),
						   Mage::getStoreConfig(self::XML_PATH_HELPDESK_SENDER_EMAIL),
                           null,
                           array('data' => $postObject)
			 );
				if(!$mailTemplate->getSentSuccess()) {
					throw new Exception();
                }  
        } catch (Exception $e) {
			//print_R($e);
            $errorMessage = $e->getMessage();
            return $errorMessage;
        }
		} 
}
	 