<?php
class Mirasvit_Kb_Helper_Mail
{
	public $emails = array();
	protected function getConfig() {
		return Mage::getSingleton('kb/config');
	}

	protected function getSender() {
		return 'general';
	}

    protected function send($templateName, $senderName, $senderEmail, $recipientEmail, $recipientName, $variables) {
		if (!$senderEmail) {
			return false;
		}
        $template = Mage::getModel('core/email_template');
        $template->setDesignConfig(array('area' => 'backend'))
                 ->sendTransactional($templateName,
                 array(
                     'name' => $senderName,
                     'email' => $senderEmail
                 ),
                 $recipientEmail, $recipientName, $variables);
		$text = $template->getProcessedTemplate($variables, true);
		$this->emails[]= array('text'=>$text, 'recipient_email'=>$recipientEmail, 'recipient_name'=>$recipientName);
		return true;
    }


    /************************/

}