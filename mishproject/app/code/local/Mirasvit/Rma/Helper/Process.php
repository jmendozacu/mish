<?php
class Mirasvit_Rma_Helper_Process extends Mage_Core_Helper_Abstract
{

    public function getConfig()
    {
        return Mage::getSingleton('rma/config');
    }

    /**
    * save function for backend
    */
    public function createOrUpdateRmaFromPost($data, $items)
    {
        $rma = Mage::getModel('rma/rma');
        if (isset($data['rma_id']) && $data['rma_id']) {
            $rma->load((int)$data['rma_id']);
            $rmaIsNew = false;
        } else {
            unset($data['rma_id']);
            $rmaIsNew = true;
        }
        if ($data['street2'] != '') {
            $data['street'] .= "\n". $data['street2'];
            unset($data['street2']);
        }

        $order = Mage::getModel('sales/order')->load((int)$data['order_id']);
        $rma->addData($data);
        $rma->setCustomerId($order->getCustomerId());
        $rma->setStoreId($order->getStoreId());
        if (!$rma->getUserId()) {
            if ($user = Mage::getSingleton('admin/session')->getUser()) {
                $rma->setUserId($user->getId());
            }
        }
        $rma->save();
        Mage::helper('mstcore/attachment')->saveAttachment('rma_return_label', $rma->getId(), 'return_label');

        foreach ($items as $item) {
            // if ((int)$item['qty_requested'] == 0) {
            //     continue;
            // }
            $rmaItem = Mage::getModel('rma/item');
            if (isset($item['item_id']) && $item['item_id']) {
                $rmaItem->load((int)$item['item_id']);
            } else {
                unset($item['item_id']);
            }
            if (!(int)$item['reason_id']) {
                unset($item['reason_id']);
            }
            if (!(int)$item['resolution_id']) {
                unset($item['resolution_id']);
            }
            if (!(int)$item['condition_id']) {
                unset($item['condition_id']);
            }
            $rmaItem->addData($item)
                    ->setRmaId($rma->getId());
            $orderItem = Mage::getModel('sales/order_item')->load((int)$item['order_item_id']);
            $rmaItem->initFromOrderItem($orderItem);
            $rmaItem->save();
        }

        if ($rmaIsNew && $rma->getTicketId()) {
            $this->closeTicketByRma($rma);
        }

        Mage::helper('rma/process')->notifyRmaChange($rma);
        return $rma;
    }

    /**
    * save function for frontend
    */
    public function createRmaFromPost($data, $items, $customer = false)
    {
        $order = Mage::getModel('sales/order')->load((int)$data['order_id']);
        if ($customer && $order->getCustomerId() != $customer->getId()) {
            throw new Exception("Error Processing Request 1");
        }

        $address = $order->getShippingAddress();

        $rma = Mage::getModel('rma/rma');
        $rma->addData($data)
            ->setStoreId($order->getStoreId())
            ->setEmail($order->getCustomerEmail())
            ->setFirstname($address->getFirstname())
            ->setLastname($address->getLastname())
            ->setCompany($address->getCompany())
            ->setTelephone($address->getTelephone())

            ->setStreet(implode("\n", $address->getStreet()))
            ->setCity($address->getCity())
            ->setCountryId($address->getCountryId())
            ->setRegionId($address->getRegionId())
            ->setRegion($address->getRegion())

            ;
        if (isset($data['gift'])) {
            $rma->addData($data['gift']);
            $rma->setIsGift(true);
        }
        if ($customer) {
            $rma->setCustomerId($order->getCustomerId());
        }
// pr($rma->getData());die;
        $rma->save();
        $collection = $order->getItemsCollection();
        foreach ($collection as $orderItem) {
            $rmaItem = Mage::getModel('rma/item');
            $rmaItem->setRmaId($rma->getId());
            foreach ($items as $item) {
                if ((int)$item['order_item_id'] == $orderItem->getId()) {
                    $rmaItem->addData($item);
                    break;
                }
            }
            $rmaItem->initFromOrderItem($orderItem);
            $rmaItem->save();
        }

        Mage::helper('rma/process')->notifyRmaChange($rma);
        if ($data['comment'] != '') {
            $rma->addComment($data['comment'], false, $rma->getCustomer(), false, false, true, true);
        }
        return $rma;
    }

    /**
    * save comment function for frontend
    */
    public function createCommentFromPost($rma, $post)
    {
        $comment = false;
        if (isset($post['comment'])) {
            $comment = $post['comment'];
        }
        unset($post['id']);
        unset($post['comment']);
        $fields = array();
        foreach ($post as $code => $value) {
            if (!$value) {
                continue;
            }
            $field = Mage::getModel('rma/field')->getCollection()
                        ->addFieldToFilter('code', $code)
                        ->getFirstItem();
            if ($field->getId()) {
                $fields[] = "{$field->getName()}: {$value}";
                $rma->setData($code, $value);
            }
        }
        if (count($fields)) {
            if ($comment) {
                $comment .= "\n";
            }
            $comment .= implode("\n", $fields);
        }
        if (trim($comment) == '' && !Mage::helper('mstcore/attachment')->hasAttachments()) {
            throw new Mage_Core_Exception(Mage::helper('rma')->__('Please, post not empty message'));
        }
        $rma->addComment($comment, false, $rma->getCustomer(), false, false, true);
    }

    public function notifyRmaChange($rma)
    {
        if ($rma->getStatusId() != $rma->getOrigData('status_id')) {
            $status = $rma->getStatus();
            if ($message = $status->getCustomerMessage()) {
                $message = Mage::helper('rma/mail')->parseVariables($message, $rma);
                Mage::helper('rma/mail')->sendNotificationCustomerEmail($rma, $message);
            }

            if ($message = $status->getAdminMessage()) {
                $message = Mage::helper('rma/mail')->parseVariables($message, $rma);
                Mage::helper('rma/mail')->sendNotificationAdminEmail($rma, $message);
            }

            if ($message = $status->getHistoryMessage()) {
                $message = Mage::helper('rma/mail')->parseVariables($message, $rma);
                $isNotified = $status->getCustomerMessage() != '';
                $rma->addComment($message, true, false, false, $isNotified, true);
            }
        } elseif ($rma->getUserId() != $rma->getOrigData('user_id')) {
            $status = $rma->getStatus();
            $message = $status->getAdminMessage();
            $message = Mage::helper('rma/mail')->parseVariables($message, $rma);
            Mage::helper('rma/mail')->sendNotificationAdminEmail($rma, $message);
        }
    }

    public function processEmail($email, $code)
    {
        $rma = false;
        $customer = false;
        $user = false;
        $triggeredByCustomer = true;

        // если у нас есть код, то ок
        // если кода нет, то такую ситуцию мы не обрабатываем

        $guestId = str_replace('RMA-', '', $code);
        //try to find RMA for this email
        $rmas = Mage::getModel('rma/rma')->getCollection()
                    ->addFieldToFilter('guest_id', $guestId)
                    ;
        if (!$rmas->count()) {
            echo 'Can\'t find a RMA by guest id '.$guestId;
           return false;
        }

        $rma = $rmas->getFirstItem();

        //try to find staff user for this email
        $users = Mage::getModel('admin/user')->getCollection()
            ->addFieldToFilter('email', $email->getFromEmail());
        if ($users->count()) {
            $user = $users->getFirstItem();
            $triggeredByCustomer = false;
            $rma->setUserId($user->getId());
            $rma->save();
        } else {
            $customers = Mage::getModel('customer/customer')->getCollection()
                ->addAttributeToSelect('*')
                ->addFieldToFilter('email', $email->getFromEmail());
            if ($customers->count()) {
                $customer = $customers->getLastItem(); //если мы можем найти кастомера по емейлу - ОК
            } else { //если кастомер ответил с другого емейла или это гость - создаем его временно
                $customer = new Varien_Object();
                $customer->setName($email->getSenderName());
                $customer->setEmail($email->getFromEmail());
            }
        }

        //add message to rma
        $body = Mage::helper('helpdesk/string')->parseBody($email->getBody(), $email->getFormat());
        $message = $rma->addComment($body, false, $customer, $user, true, true, true, $email);
        return $rma;
   }

   public function closeTicketByRma($rma)
   {
       $ticket = Mage::getModel('helpdesk/ticket')->load($rma->getTicketId());
       $ticket->addMessage($this->__("Ticket was converted to the RMA #%s", $rma->getIncrementId()), false, $rma->getUser(), true);
       $ticket->close();
   }
}