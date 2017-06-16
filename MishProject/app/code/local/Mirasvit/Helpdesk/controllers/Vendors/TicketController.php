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


class Mirasvit_Helpdesk_Vendors_TicketController extends VES_Vendors_Controller_Action
{
    protected function _initAction ()
    {
        $this->loadLayout()->_setActiveMenu('helpdesk');

        return $this;
    }

    public function indexAction ()
    {
        Mage::register('is_archive', (bool)$this->getRequest()->getParam('is_archive'));

        $this->_title($this->__('Tickets'));
        $this->_initAction();
        $this->_addContent($this->getLayout()
            ->createBlock('helpdesk/vendors_ticket'));
        $this->renderLayout();
    }

    public function addAction ()
    {
        $this->_title($this->__('New Ticket'));

        $ticket = $this-> _initTicket();

        $data = Mage::getSingleton('vendors/session')->getFormData(true);
        //echo  $data;
        //exit();
        if (!empty($data)) {
            $ticket->setData($data);
        }
        if ($customerId = $this->getRequest()->getParam('customer_id')) {
            $ticket->setCustomerId($customerId);
            $customer = Mage::getModel('customer/customer')->load($customerId);
            if ($customer->getId()) {
                $ticket->setCustomerEmail($customer->getEmail());
            }
        } elseif ($orderId = $this->getRequest()->getParam('order_id')) {
            $ticket->initFromOrder($orderId);
        }

        if (!$ticket->getStoreId()) {
            if ($storeId = Mage::getSingleton('helpdesk/config')->getGeneralDefaultStore()) {
                $ticket->setStoreId($storeId);
            }
        }

        $this->_initAction();
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Ticket  Manager'),
                Mage::helper('adminhtml')->__('Ticket Manager'), mage::getUrl('*/*/'));
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Add Ticket '), Mage::helper('adminhtml')->__('Add Ticket'));

        $this->getLayout()
            ->getBlock('head')
            ->setCanLoadExtJs(true);
        $this->_addContent($this->getLayout()->createBlock('helpdesk/vendors_ticket_edit'))
                ->_addLeft($this->getLayout()->createBlock('helpdesk/vendors_ticket_edit_tabs'));
        $this->renderLayout();
    }

    public function editAction ()
    {
          $user = Mage::getSingleton('vendors/session')->getUser();
        
        Mage::register('is_archive', (bool)$this->getRequest()->getParam('is_archive'));

        $ticket = $this->_initTicket();

        if ($ticket->getId()) {
            $this->_title(Mage::helper('helpdesk')->htmlEscape($ticket->getName()));
            $this->_initAction();
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Tickets'),
                    Mage::helper('adminhtml')->__('Tickets'), mage::getUrl('*/*/'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Edit Ticket '),
                    Mage::helper('adminhtml')->__('Edit Ticket '));

            $this->getLayout()
                ->getBlock('head')
                ->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('helpdesk/vendors_ticket_edit'))
                    ->_addLeft($this->getLayout()->createBlock('helpdesk/vendors_ticket_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('vendors/session')->addError(Mage::helper('adminhtml')->__('The ticket does not exist.'));
            $this->_redirect('*/*/');
        }
    }

    public function saveAction ()
    {
        if ($data = $this->getRequest()->getPost()) {
            try {
                $user = Mage::getSingleton('vendors/session')->getUser();
                $ticket = Mage::helper('helpdesk/process')->createOrUpdateFromBackendPost($data, $user);

                if ($data['reply'] != '' && $data['reply_type'] != Mirasvit_Helpdesk_Model_Config::MESSAGE_INTERNAL) {
                    Mage::getSingleton('vendors/session')->addSuccess(Mage::helper('adminhtml')->__('Message was successfully sent'));
                } else {
                    if ($ticket->getOrigData('is_archived') != $ticket->getData('is_archived')) {
                        Mage::getSingleton('vendors/session')->addSuccess(Mage::helper('adminhtml')->__('Ticket was moved to archive'));
                    } else {
                        Mage::getSingleton('vendors/session')->addSuccess(Mage::helper('adminhtml')->__('Ticket was successfully updated'));
                    }
                }
                Mage::getSingleton('vendors/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $ticket->getId()));
                    return;
                }

                $this->_redirect('*/*/', array('is_archive' => $ticket->getOrigData('is_archived')));
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('vendors/session')->addError($e->getMessage());
                Mage::getSingleton('vendors/session')->setFormData($data);
                if ($this->getRequest()->getParam('id')) {
                    $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                } else {
                    $this->_redirect('*/*/add');
                }
                return;
            }
        }
        Mage::getSingleton('vendors/session')->addError(Mage::helper('adminhtml')->__('Unable to find ticket to save'));
        $this->_redirect('*/*/');
    }

    public function archiveAction ()
    {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $ticket = Mage::getModel('helpdesk/ticket')->load($this->getRequest()->getParam('id'));
                // $status = Mage::getModel('helpdesk/status')->loadByCode(Mirasvit_Helpdesk_Model_Config::STATUS_CLOSED);

                $ticket = Mage::getModel('helpdesk/ticket')->load($this->getRequest()->getParam('id'));
                $ticket
                    // ->setStatusId($status->getId())
                    ->setIsArchived(true)
                    ->save();

                Mage::getSingleton('vendors/session')->addSuccess(
                        Mage::helper('adminhtml')->__('Ticket was moved to archive'));
                $this->_redirect('*/*/');
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('vendors/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()
                    ->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    public function restoreAction ()
    {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $ticket = Mage::getModel('helpdesk/ticket');
                $status = Mage::getModel('helpdesk/status')->loadByCode(Mirasvit_Helpdesk_Model_Config::STATUS_OPEN);

                $ticket = Mage::getModel('helpdesk/ticket')->load($this->getRequest()->getParam('id'));
                $ticket
                    ->setStatusId($status->getId())
                    ->setIsArchived(false)
                    ->save();


                Mage::getSingleton('vendors/session')->addSuccess(
                        Mage::helper('adminhtml')->__('Ticket was moved to the Tickets List'));
                $this->_redirect('*/*/');
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('vendors/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()
                    ->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    public function deleteAction ()
    {
        if ($this->getRequest()->getParam('id') > 0 && Mage::helper('helpdesk/permission')->isTicketRemoveAllowed()) {
            try {
                $ticket = Mage::getModel('helpdesk/ticket');

                $ticket->setId($this->getRequest()
                    ->getParam('id'))
                    ->delete();

                Mage::getSingleton('vendors/session')->addSuccess(
                        Mage::helper('adminhtml')->__('Ticket was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('vendors/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()
                    ->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }


    public function spamAction ()
    {
        $id = (int)$this->getRequest()->getParam('id');
        try {
            $ticket = Mage::getModel('helpdesk/ticket')->load($id);
            $ticket->markAsSpam();

            Mage::getSingleton('vendors/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Ticket was successfully moved to the spam folder'));
            $this->_redirect('*/*/');
        } catch (Exception $e) {
            Mage::getSingleton('vendors/session')->addError($e->getMessage());
            $this->_redirect('*/*/edit', array('id' => $this->getRequest()
                ->getParam('id')));
        }
        $this->_redirect('*/*/');
    }

    public function massChangeAction()
    {
        $idss = $this->getRequest()->getParam('ticket_id');
        $ids = explode(',', $idss);
        if (!is_array($ids)) {
            Mage::getSingleton('vendors/session')->addError(Mage::helper('adminhtml')->__('Please select ticket(s)'));
        } else {
            try {
                $statusId = $this->getRequest()->getParam('status');
                $owner = $this->getRequest()->getParam('owner');
                $spam = $this->getRequest()->getParam('spam');
                $archive = $this->getRequest()->getParam('archive');
                foreach ($ids as $id) {
                    $ticket = Mage::getModel('helpdesk/ticket')
                        ->setIsMassDelete(true)
                        ->load($id);
                    if ($spam) {
                        $ticket->markAsSpam();
                        continue;
                    }
                    if ($archive) {
                        $ticket->setIsArchived(true);
                    }
                    if ($statusId) {
                        $ticket->setStatusId($statusId);
                    }
                    if ($owner) {
                        $ticket->initOwner($owner);
                    }
                    $ticket->save();
                }
                if ($spam) {
                    Mage::getSingleton('vendors/session')->addSuccess(
                        Mage::helper('adminhtml')->__(
                            'Total of %d record(s) were moved to the Spam folder', count($ids)
                        )
                    );
                } else {
                    Mage::getSingleton('vendors/session')->addSuccess(
                        Mage::helper('adminhtml')->__(
                            'Total of %d record(s) were successfully updated', count($ids)
                        )
                    );
                }
            } catch (Exception $e) {
                Mage::getSingleton('vendors/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massDeleteAction()
    {

        if (!Mage::helper('helpdesk/permission')->isTicketRemoveAllowed()) {
            return;
        }
        $idss = $this->getRequest()->getParam('ticket_id');
        $ids = explode(',', $idss);
        
        if (!is_array($ids)) {
            Mage::getSingleton('vendors/session')->addError(Mage::helper('adminhtml')->__('Please select ticket(s)'));
        } else {
            try {
                foreach ($ids as $id) {
                    $ticket = Mage::getModel('helpdesk/ticket')
                        ->setIsMassDelete(true)
                        ->load($id);
                    $ticket->delete();
                }
                Mage::getSingleton('vendors/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($ids)
                    )
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('vendors/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function _initTicket()
    {
        $ticket = Mage::getModel('helpdesk/ticket');
        if ($this->getRequest()->getParam('id')) {
            $ticket->load($this->getRequest()->getParam('id'));
            Mage::helper('helpdesk/permission')->checkReadTicketRestrictions($ticket);
        }

        Mage::register('current_ticket', $ticket);

        return $ticket;
    }

    /************************/

    public function customerfindAction()
    {
        $q = $this->getRequest()->getParam('q');
        $result = Mage::helper('helpdesk')->findCustomer($q);
        echo Mage::helper('core')->jsonEncode($result);
        die;
    }

	public function attachmentAction() {
		$id = $this->getRequest()->getParam('id');
		$attachment = Mage::getModel('helpdesk/attachment')->load($id);
		// give our picture the proper headers...otherwise our page will be confused
		header("Content-Disposition: attachment; filename={$attachment->getName()}");
		header("Content-length: {$attachment->getSize()}");
		header("Content-type: {$attachment->getType()}");
		echo $attachment->getBody();
		die;
	}
}
