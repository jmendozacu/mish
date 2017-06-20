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



class Mirasvit_Helpdesk_Helper_Process extends Varien_Object
{
    public function getConfig()
    {
        return Mage::getSingleton('helpdesk/config');
    }

    /**
     * creates ticket from frontend
     * @param  array $post
     * @param  Varien_Object or Customer model $customer
     * @return Mirasvit_Helpdesk_Model_Ticket
     */
    public function createFromPost($post, $channel)
    {
         $orderid  = $post['order_id'];

        $ordercollection = Mage::getModel('sales/order')->load($orderid );
         //   $vendorid=$ordercollection['vendor_id'];
         // echo  $vendorid;
         // exit();
       
        
        $ticket = Mage::getModel('helpdesk/ticket');
        // если кастомер не был авторизирован, то ищем его
        $customer = Mage::helper('helpdesk/customer')->getCustomerByPost($post);
        // echo "asdohuosd<pre>";
        // print_r($customer->getData() );
        // exit();

        $ticket->setCustomerId($customer->getId())
            ->setCustomerEmail($customer->getEmail())
            ->setCustomerName($customer->getName())
            ->setQuoteAddressId($customer->getQuoteAddressId())
            ->setCode(Mage::helper('helpdesk/string')->generateTicketCode())
            ->setName($post['name'])
            ->setSelecttype($post['tickettype_id'])
            ->setDescription($this->getEnviromentDescription())
            ->setVendorid($ordercollection['vendor_id']);

        // if (isset($post['tickettype_id'])) {
        //     $avc=$ticket->setSelecttype($post['tickettype_id']);
        //     echo $avc;
        // }
        // exit();
         if (isset($post['priority_id'])) {
            $ticket->setPriorityId((int)$post['priority_id']);
        }
        if (isset($post['department_id'])) {
            $ticket->setDepartmentId((int)$post['department_id']);
        } else {
            $ticket->setDepartmentId($this->getConfig()->getContactFormDefaultDepartment());
        }
        if (isset($post['order_id'])) {
            $ticket->setOrderId((int)$post['order_id']);
        }
        $ticket->setStoreId(Mage::app()->getStore()->getStoreId());
        $ticket->setChannel($channel);
        if ($channel == Mirasvit_Helpdesk_Model_Config::CHANNEL_FEEDBACK_TAB) {
            $url = Mage::getSingleton('customer/session')->getFeedbackUrl();
            $ticket->setChannelData(array('url' => $url));
        }

        Mage::helper('helpdesk/field')->processPost($post, $ticket);
        $ticket->save();
        $body = $post['message'];
        // $body = Mage::helper('helpdesk/string')->parseBody($post['message'], Mirasvit_Helpdesk_Model_Config::FORMAT_PLAIN);
        $ticket->addMessage($body, $customer, false, Mirasvit_Helpdesk_Model_Config::CUSTOMER, Mirasvit_Helpdesk_Model_Config::MESSAGE_PUBLIC, false, Mirasvit_Helpdesk_Model_Config::FORMAT_PLAIN);
        Mage::helper('helpdesk/history')->changeTicket($ticket, Mirasvit_Helpdesk_Model_Config::CUSTOMER, array('customer' => $customer));

        return $ticket;
    }

    public function getEnviromentDescription()
    {
        return print_r($_SERVER, true);
    }

    public function createOrUpdateFromBackendPost($data, $user)
    {
        $ticket = Mage::getModel('helpdesk/ticket');
        if (isset($data['ticket_id']) && (int)$data['ticket_id'] > 0) {
            $ticket->load((int)$data['ticket_id']);
        }
        if (!Zend_Validate::is($data['customer_email'] , 'EmailAddress')) {
             throw new Mage_Core_Exception("Invalid Customer Email");
        }
        if (!isset($data['customer_id']) || !$data['customer_id']) {
            if (!$ticket->getCustomerName()) {
                $data['customer_name'] = $data['customer_email'];
            }
        }
        if (isset($data['customer_id']) && strpos($data['customer_id'], 'address_') !== false) {
            $data['quote_address_id'] = (int)str_replace('address_', '', $data['customer_id']);
            $data['customer_id'] = null;
            if ($data['quote_address_id']) {
                $quoteAddress = Mage::getModel('sales/order_address')->load($data['quote_address_id']);
                $data['customer_name'] = $quoteAddress->getName();
            }
        } else {
            $data['quote_address_id'] = null;
        }

        $ticket->addData($data);

        if($data['allowCC'] == 'false') {
            $ticket->setCc('');
        }

        if($data['allowBCC'] == 'false') {
            $ticket->setBcc('');
        }

        Mage::helper('helpdesk/tag')->setTags($ticket, $data['tags']);
        //set custom fields
        Mage::helper('helpdesk/field')->processPost($data, $ticket);
        //set ticket user and department
        if (isset($data['owner'])) {
            $ticket->initOwner($data['owner']);
        }
        if (isset($data['fp_owner'])) {
            $ticket->initOwner($data['fp_owner'], 'fp');
        }
        if ($data['fp_period_unit'] == 'custom') {
            $format = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
            Mage::helper('mstcore/date')->formatDateForSave($ticket, 'fp_execute_at', $format);
        } elseif($data['fp_period_value'])  {
            $ticket->setData('fp_execute_at', $this->createFpDate($data['fp_period_unit'], $data['fp_period_value']));
        }
        if (!$ticket->getId()) {
            $ticket->setChannel(Mirasvit_Helpdesk_Model_Config::CHANNEL_BACKEND);
        }

        $ticket->save();

        $bodyFormat = Mirasvit_Helpdesk_Model_Config::FORMAT_PLAIN;
        if ($this->getConfig()->getGeneralIsWysiwyg()) {
            $bodyFormat = Mirasvit_Helpdesk_Model_Config::FORMAT_HTML;
        }
        if (trim($data['reply']) || $_FILES['attachment']['name'][0] != '') {
            $message = $ticket->addMessage($data['reply'], false, $user, Mirasvit_Helpdesk_Model_Config::USER, $data['reply_type'], false, $bodyFormat);
        }
        Mage::helper('helpdesk/history')->changeTicket($ticket, Mirasvit_Helpdesk_Model_Config::USER, array('user' => $user));
        Mage::helper('helpdesk/draft')->clearDraft($ticket);
        return $ticket;
    }

    public function createFpDate($unit, $value)
    {
        switch ($unit) {
            case 'minutes':
                $timeshift = $value;
                break;
            case 'hours':
                $timeshift = $value * 60;
                break;
            case 'days':
                $timeshift = $value * 60 * 24;
                break;
            case 'weeks':
                $timeshift = $value * 60 * 24 * 7;
                break;
            case 'weeks':
                $timeshift = $value * 60 * 24 * 31;
                break;
        }
        $timeshift *= 60; //in seconds
        $time = strtotime(Mage::getSingleton('core/date')->gmtDate()) + $timeshift;
        $time = date('Y-m-d H:i:s', $time);

        return $time;
    }

    public function isDev()
    {
        return Mage::getSingleton('helpdesk/config')->getDeveloperIsActive();
    }

    public function processEmail($email, $code)
    {
        $ticket      = false;
        $customer    = false;
        $user        = false;
        $triggeredBy = Mirasvit_Helpdesk_Model_Config::CUSTOMER;
        $messageType = Mirasvit_Helpdesk_Model_Config::MESSAGE_PUBLIC;

        if($code) {
            //try to find customer for this email
            $tickets = Mage::getModel('helpdesk/ticket')->getCollection()
                        ->addFieldToFilter('code', $code)
                        ->addFieldToFilter('customer_email', $email->getFromEmail())
                        ;
            if ($tickets->count()) {
                $ticket = $tickets->getFirstItem();
            } else {
                //try to find staff user for this email
                $users = Mage::getModel('admin/user')->getCollection()
                    ->addFieldToFilter('email', $email->getFromEmail())
                    ;

                if ($users->count()) {
                    $user = $users->getFirstItem();
                    $tickets = Mage::getModel('helpdesk/ticket')->getCollection()
                                ->addFieldToFilter('code', $code)
                                ;
                    if ($tickets->count()) {
                        $ticket = $tickets->getFirstItem();
                        $ticket->setUserId($user->getId());
                        $ticket->save();
                        $triggeredBy = Mirasvit_Helpdesk_Model_Config::USER;
                    } else {
                      $user = false; //@temp dva for testing
                    }
                } else { //third party
                    $tickets = Mage::getModel('helpdesk/ticket')->getCollection()
                                ->addFieldToFilter('code', $code)
                                ;
                    if ($tickets->count()) {
                        $ticket = $tickets->getFirstItem();
                        $triggeredBy = Mirasvit_Helpdesk_Model_Config::THIRD;
                        if ($ticket->isThirdPartyPublic()) {
                            $messageType = Mirasvit_Helpdesk_Model_Config::MESSAGE_PUBLIC_THIRD;
                        } else {
                            $messageType = Mirasvit_Helpdesk_Model_Config::MESSAGE_INTERNAL_THIRD;
                        }
                    }
                }
            }
        }

        if (!$user) {
            $customer = Mage::helper('helpdesk/customer')->getCustomerByEmail($email);
        }
        // create a new ticket
        if (!$ticket) {
            $ticket = Mage::getModel('helpdesk/ticket');
            if (!$code) {
              $ticket->setCode(Mage::helper('helpdesk/string')->generateTicketCode());
            } else {
              $ticket->setCode($code);//temporary for testing to fix @dva
            }
            $gateway = Mage::getModel('helpdesk/gateway')->load($email->getGatewayId());
            if ($gateway->getId()) {
                if ($gateway->getDepartmentId()) {
                    $ticket->setDepartmentId($gateway->getDepartmentId());
                } else { //if department was removed
                    $departments = Mage::getModel('helpdesk/department')->getCollection()
                                        ->addFieldToFilter('is_active', true);
                    if ($departments->count()) {
                        $department = $departments->getFirstItem();
                        $ticket->setDepartmentId($department->getId());
                    } else {
                        // throw new Exception("Helpdesk MX - Can't find any active department. Helpdesk can't fetch tickets correctly!");
                        Mage::log('Helpdesk MX - Can\'t find any active department. Helpdesk can\'t fetch tickets correctly!');
                    }
                }
                $ticket->setStoreId($gateway->getStoreId());
            }

            $ticket
                ->setName($email->getSubject())
                ->setCustomerName($customer->getName())
                ->setCustomerId($customer->getId())
                ->setQuoteAddressId($customer->getQuoteAddressId())
                ->setCustomerEmail($email->getFromEmail())
                ->setChannel(Mirasvit_Helpdesk_Model_Config::CHANNEL_EMAIL)
                ->setCc($email->getCc())
                ;
            $ticket->setEmailId($email->getId());
            $ticket->save();
            if ($pattern = $this->checkForSpamPattern($email)) {
                $ticket->markAsSpam($pattern);
                if ($email) {
                    $email->setPatternId($pattern->getId())->save();
                }
            }
        }

        //add message to ticket
        $text = $email->getBody();
        $encodingHelper = Mage::helper('helpdesk/encoding');
        $text = $encodingHelper->toUTF8($text);
        $body = Mage::helper('helpdesk/string')->parseBody($text, $email->getFormat());
        $message = $ticket->addMessage($body, $customer, $user, $triggeredBy, $messageType, $email);

        Mage::dispatchEvent('helpdesk_process_email', array('body'=>$body, 'customer' => $customer, 'user' => $user, 'ticket' => $ticket));

        Mage::helper('helpdesk/history')->changeTicket($ticket, $triggeredBy, array('user' => $user, 'customer' => $customer));

        return $ticket;
   }

    public function checkForSpamPattern($email) {
        $patterns = Mage::getModel('helpdesk/pattern')->getCollection()
            ->addFieldToFilter('is_active', true);
        foreach ($patterns as $pattern) {
            if ($pattern->checkEmail($email)) {
                return $pattern;
            }
        }

        return false;
    }
}