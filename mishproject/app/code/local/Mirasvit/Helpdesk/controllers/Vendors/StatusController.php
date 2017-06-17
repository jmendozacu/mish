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


class Mirasvit_Helpdesk_Vendors_StatusController extends VES_Vendors_Controller_Action
{
    protected function _initAction ()
    {
        $this->loadLayout()->_setActiveMenu('helpdesk');

        return $this;
    }

    public function indexAction ()
    {
        $this->_title($this->__('Statuses'));
        $this->_initAction();
        $this->_addContent($this->getLayout()
            ->createBlock('helpdesk/vendors_status'));
        $this->renderLayout();
    }

    public function addAction ()
    {
        $this->_title($this->__('New Status'));

        $this->_initStatus();

        $this->_initAction();
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Status  Manager'),
                Mage::helper('adminhtml')->__('Status Manager'), mage::getUrl('*/*/'));
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Add Status '), Mage::helper('adminhtml')->__('Add Status'));

        $this->getLayout()
            ->getBlock('head')
            ->setCanLoadExtJs(true);
        $this->_addContent($this->getLayout()->createBlock('helpdesk/vendors_status_edit'));
        $this->renderLayout();
    }

    public function editAction ()
    {
        $status = $this->_initStatus();

        if ($status->getId()) {
            $this->_title($this->__("Edit Status '%s'", $status->getName()));
            $this->_initAction();
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Statuses'),
                    Mage::helper('adminhtml')->__('Statuses'), mage::getUrl('*/*/'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Edit Status '),
                    Mage::helper('adminhtml')->__('Edit Status '));

            $this->getLayout()
                ->getBlock('head')
                ->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('helpdesk/vendors_status_edit'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('vendors/session')->addError(Mage::helper('adminhtml')->__('The Status does not exist.'));
            $this->_redirect('*/*/');
        }
    }

    public function saveAction ()
    {
        if ($data = $this->getRequest()->getPost()) {

            $status = $this->_initStatus();
            $status->addData($data);
            //format date to standart
            // $format = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
            // Mage::helper('mstcore/date')->formatDateForSave($status, 'active_from', $format);
            // Mage::helper('mstcore/date')->formatDateForSave($status, 'active_to', $format);

            try {
                $status->save();

                Mage::getSingleton('vendors/session')->addSuccess(Mage::helper('adminhtml')->__('Status was successfully saved'));
                Mage::getSingleton('vendors/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $status->getId(), 'store' => $status->getStoreId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('vendors/session')->addError($e->getMessage());
                Mage::getSingleton('vendors/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('vendors/session')->addError(Mage::helper('adminhtml')->__('Unable to find Status to save'));
        $this->_redirect('*/*/');
    }

    public function deleteAction ()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                $status = Mage::getModel('helpdesk/status')->load($id);
                $code = $status->getCode();
                if ($code == Mirasvit_Helpdesk_Model_Config::STATUS_OPEN
                    || $code == Mirasvit_Helpdesk_Model_Config::STATUS_CLOSED) {
                    throw new Exception("You can't remove this status. It's required by system.");
                }
                $status->delete();

                Mage::getSingleton('vendors/session')->addSuccess(
                        Mage::helper('adminhtml')->__('Status was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('vendors/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()
                    ->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    public function massDeleteAction()
    {
        $idss = $this->getRequest()->getParam('status_id');
         $ids=explode(',', $idss);
        if (!is_array($ids)) {
            Mage::getSingleton('vendors/session')->addError(Mage::helper('adminhtml')->__('Please select Status(s)'));
        } else {
            try {
                foreach ($ids as $id) {
                    $status = Mage::getModel('helpdesk/status')
                        ->setIsMassDelete(true)
                        ->load($id);
                    $status->delete();
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

    public function _initStatus()
    {
        $status = Mage::getModel('helpdesk/status');
        if ($this->getRequest()->getParam('id')) {
            $status->load($this->getRequest()->getParam('id'));
            if ($storeId = (int)$this->getRequest()->getParam('store')) {
                $status->setStoreId($storeId);
            }
        }

        Mage::register('current_status', $status);

        return $status;
    }

    /************************/

}