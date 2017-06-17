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


class Mirasvit_Helpdesk_Vendors_PriorityController extends VES_Vendors_Controller_Action
{
    protected function _initAction ()
    {
        $this->loadLayout()->_setActiveMenu('helpdesk');

        return $this;
    }

    public function indexAction ()
    {
        $this->_title($this->__('Priorities'));
        $this->_initAction();
        $this->_addContent($this->getLayout()
            ->createBlock('helpdesk/vendors_priority'));
        $this->renderLayout();
    }

    public function addAction ()
    {
        $this->_title($this->__('New Priority'));

        $this->_initPriority();

        $this->_initAction();
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Priority  Manager'),
                Mage::helper('adminhtml')->__('Priority Manager'), mage::getUrl('*/*/'));
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Add Priority '), Mage::helper('adminhtml')->__('Add Priority'));

        $this->getLayout()
            ->getBlock('head')
            ->setCanLoadExtJs(true);
        $this->_addContent($this->getLayout()->createBlock('helpdesk/vendors_priority_edit'));
        $this->renderLayout();
    }

    public function editAction ()
    {
        $priority = $this->_initPriority();

        if ($priority->getId()) {
            $this->_title($this->__("Edit Priority '%s'", $priority->getName()));
            $this->_initAction();
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Priorities'),
                    Mage::helper('adminhtml')->__('Priorities'), mage::getUrl('*/*/'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Edit Priority '),
                    Mage::helper('adminhtml')->__('Edit Priority '));

            $this->getLayout()
                ->getBlock('head')
                ->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('helpdesk/vendors_priority_edit'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('vendors/session')->addError(Mage::helper('adminhtml')->__('The Priority does not exist.'));
            $this->_redirect('*/*/');
        }
    }

    public function saveAction ()
    {
        if ($data = $this->getRequest()->getPost()) {

            $priority = $this->_initPriority();
            $priority->addData($data);
            //format date to standart
            // $format = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
            // Mage::helper('mstcore/date')->formatDateForSave($priority, 'active_from', $format);
            // Mage::helper('mstcore/date')->formatDateForSave($priority, 'active_to', $format);

            try {
                $priority->save();

                Mage::getSingleton('vendors/session')->addSuccess(Mage::helper('adminhtml')->__('Priority was successfully saved'));
                Mage::getSingleton('vendors/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $priority->getId(), 'store' => $priority->getStoreId()));
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
        Mage::getSingleton('vendors/session')->addError(Mage::helper('adminhtml')->__('Unable to find Priority to save'));
        $this->_redirect('*/*/');
    }

    public function deleteAction ()
    {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $priority = Mage::getModel('helpdesk/priority');

                $priority->setId($this->getRequest()
                    ->getParam('id'))
                    ->delete();

                Mage::getSingleton('vendors/session')->addSuccess(
                        Mage::helper('adminhtml')->__('Priority was successfully deleted'));
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
        $idss = $this->getRequest()->getParam('priority_id');
         $ids=explode(',', $idss);
        if (!is_array($ids)) {
            Mage::getSingleton('vendors/session')->addError(Mage::helper('adminhtml')->__('Please select Priority(s)'));
        } else {
            try {
                foreach ($ids as $id) {
                    $priority = Mage::getModel('helpdesk/priority')
                        ->setIsMassDelete(true)
                        ->load($id);
                    $priority->delete();
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

    public function _initPriority()
    {
        $priority = Mage::getModel('helpdesk/priority');
        if ($this->getRequest()->getParam('id')) {
            $priority->load($this->getRequest()->getParam('id'));
            if ($storeId = (int)$this->getRequest()->getParam('store')) {
                $priority->setStoreId($storeId);
            }
        }

        Mage::register('current_priority', $priority);

        return $priority;
    }

    /************************/

}