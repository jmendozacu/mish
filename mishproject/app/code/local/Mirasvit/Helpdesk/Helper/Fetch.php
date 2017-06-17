<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Help Desk MX
 * @version   1.1.0
 * @build     1285
 * @copyright Copyright (C) 2015 Mirasvit (http://mirasvit.com/)
 */



class Mirasvit_Helpdesk_Helper_Fetch extends Varien_Object
{
    protected $gateway;

    /** @var  Mirasvit_Ddeboer_Imap_Connection */
    protected $connection;

    /** @var  Mirasvit_Ddeboer_Imap_Mailbox */
    protected $mailbox;

    public function isDev()
    {
        return Mage::getSingleton('helpdesk/config')->getDeveloperIsActive();
    }

    /**
     * @param Mirasvit_Helpdesk_Model_Gateway
     * @return bool
     */
    public function connect($gateway)
    {
        $this->gateway = $gateway;
        $flags = sprintf('/%s', $gateway->getProtocol());
        if($gateway->getEncryption() == 'ssl') {
            $flags .= '/ssl';
        }
        $flags .= '/novalidate-cert';

        // echo $flags;die;
        $server = new Mirasvit_Ddeboer_Imap_Server($gateway->getHost(), $gateway->getPort(), $flags);
        if(function_exists('imap_timeout')) {
            imap_timeout(1,20);
        }
        if (!$this->connection = $server->authenticate($gateway->getLogin(), $gateway->getPassword())) {
            return false;
        }

        $mailboxes = $this->connection->getMailboxNames();
        if (in_array('INBOX',  $mailboxes)) {
            $mailboxName = 'INBOX';
        } elseif (in_array('Inbox',  $mailboxes)) {
            $mailboxName = 'Inbox';
        } else {
            $mailboxName = $mailboxes[0];
        }

        $this->mailbox = $this->connection->getMailbox($mailboxName);

        return true;
    }

    public function close()
    {
        $this->connection->close();
    }

    /**
     * @param $message
     * @return bool
     */
    public function getFromEmail($message)
    {
        // если есть reply to, то мы его устанавливаем как from, т.к. на него мы будем отвечать
        $fromEmail = false;
        if ($message->getReplyTo() && $message->getReplyTo()->getAddress()) {
            $fromEmail = $message->getReplyTo()->getAddress();
        } elseif ($message->getFrom()) {
            $fromEmail = $message->getFrom()->getAddress();
        }

        return $fromEmail;
    }

    /**
     * @param Mirasvit_Ddeboer_Imap_Message
     * @return bool
     */
    public function createEmail($message)
    {
        try {
            $emails = Mage::getModel('helpdesk/email')->getCollection()
                ->addFieldToFilter('message_id', $message->getId())
                ->addFieldToFilter('from_email', $this->getFromEmail($message));

            if($emails->count()) {
                return false;
            }

            $bodyHtml = $message->getBodyHtml();
            $bodyPlain = $message->getBodyText();
            if (!empty($bodyHtml)) {
                $format = Mirasvit_Helpdesk_Model_Config::FORMAT_HTML;
                $body = $bodyHtml;
            } else {
                $body = $bodyPlain;
                $format = Mirasvit_Helpdesk_Model_Config::FORMAT_PLAIN;
                $tags = array('<div', '<br', '<tr');
                foreach ($tags as $tag) {
                    if (stripos($body, $tag) !== false) {
                        $format = Mirasvit_Helpdesk_Model_Config::FORMAT_HTML;
                        break;
                    }
                }
            }
            $to = array();
            foreach($message->getTo() as $email) {
                $to[] = $email->getAddress();
            }

            $cc = array();
            foreach($message->getCc() as $copy) {
                $cc[] = $copy->mailbox . '@' . $copy->host;
            }

            $fromEmail = $this->getFromEmail($message);
            $email = Mage::getModel('helpdesk/email')
                ->setMessageId($message->getId())
                ->setFromEmail($fromEmail)
                ->setSenderName($message->getFrom()->getName())
                ->setToEmail(implode($to, ','))
                ->setCc(implode($cc, ', '))
                ->setSubject($message->getSubject())
                ->setBody($body)
                ->setFormat($format)
                ->setHeaders($message->getHeaders()->toString())
                ->setIsProcessed(false);
                if ($this->gateway) { //may be null during tests
                    $email->setGatewayId($this->gateway->getId());
                }

            // All Auto-Submitted emails are marked as processed to prevent infinity cycles
            if(strpos($message->getHeaders()->toString(), "Auto-Submitted") !== false) {
                $email->setIsProcessed(true);
            }

            $email->save();
            // if ($this->isDev()) {
            //     echo "Email '{$email->getSubject()}' was fetched\n";
            // }
            //Save attachments if any.
            $attachments = $message->getAttachments();

            if($attachments) {
                foreach($attachments as $a) {
                    $attachment = Mage::getModel('helpdesk/attachment');
                    $attachment->setName($a->getFilename())
                        ->setType($a->getType())
                        ->setSize($a->getSize())
                        ->setBody($a->getDecodedContent())
                        ->setEmailId($email->getId())
                        ->save();
                    // if ($this->isDev()) {
                    //     echo "Attached file '{$attachment->getName()}' was saved\n";
                    //     Mage::log("Attached file '{$attachment->getName()}'", null, 'helpdesk.log');
                    // }
                }
            }
            return $email;
        } catch (Exception $e) {
            echo $e->getMessage()."\n";
            Mage::log($e);

            return false;
        }
    }

    protected function fetchEmails()
    {

        $msgs = $errors = 0;
        $max = $this->gateway->getFetchMax();

        $messages = $this->mailbox->getMessages('UNSEEN');
        $emailsNumber = $this->mailbox->count();
        //echo "Total Emails Number: $emailsNumber \n";

        if ($limit = $this->gateway->getFetchLimit()) {
            $start = $emailsNumber - $limit + 1;
            if ($start < 1) {
                $start = 1;
            }
            for ($num = $start; $num <= $emailsNumber; $num++) {
                $message = $this->mailbox->getMessage($num);
                if($this->createEmail($message)) {
                    if ($this->gateway->getIsDeleteEmails()) {
                        $message->delete();
                        $this->mailbox->expunge();
                    }
                    $msgs++;
                }
                if($max && $msgs >= $max) {
                    break;
                }
            }
        } else {
            foreach ($messages as $message) {
                if($this->createEmail($message)) {
                    if ($this->gateway->getIsDeleteEmails()) {
                        $message->delete();
                        $this->mailbox->expunge();
                    }
                    $msgs++;
                }
                if($max && $msgs >= $max) {
                    break;
                }
            }
        }

        //echo "Fetch is finished \n";
    }

    /**
     * @param Mirasvit_Helpdesk_Model_Gateway
     * @return bool
     */
    public function fetch($gateway)
    {
        if(!$this->connect($gateway)) {
            return false;
        }
        $this->fetchEmails();
        $this->close();

        return true;
    }
}
