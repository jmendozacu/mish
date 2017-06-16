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


class Mirasvit_Helpdesk_Vendors_SatisfactionController extends VES_Vendors_Controller_Action
{
    protected function _initAction ()
    {
        $this->loadLayout()->_setActiveMenu('helpdesk');

        return $this;
    }

    public function indexAction ()
    {
        $this->_title($this->__('Customer Satisfaction'));
        $this->_initAction();
        $this->_addContent($this->getLayout()
            ->createBlock('helpdesk/vendors_satisfaction'));
        $this->renderLayout();
    }

    public function addAction ()
    {
        $this->_title($this->__('New Satisfaction'));

        $this->_initSatisfaction();

        $this->_initAction();
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Satisfaction  Manager'),
                Mage::helper('adminhtml')->__('Satisfaction Manager'), mage::getUrl('*/*/'));
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Add Satisfaction '), Mage::helper('adminhtml')->__('Add Satisfaction'));

        $this->getLayout()
            ->getBlock('head')
            ->setCanLoadExtJs(true);
        $this->_addContent($this->getLayout()->createBlock('helpdesk/vendors_satisfaction_edit'));
        $this->renderLayout();
    }

    public function editAction ()
    {
        $satisfaction = $this->_initSatisfaction();

        if ($satisfaction->getId()) {
            $this->_title($this->__("Edit Satisfaction '%s'", $satisfaction->getName()));
            $this->_initAction();
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Customer Satisfaction'),
                    Mage::helper('adminhtml')->__('Customer Satisfaction'), mage::getUrl('*/*/'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Edit Satisfaction '),
                    Mage::helper('adminhtml')->__('Edit Satisfaction '));

            $this->getLayout()
                ->getBlock('head')
                ->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('helpdesk/vendors_satisfaction_edit'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('vendors/session')->addError(Mage::helper('adminhtml')->__('The Satisfaction does not exist.'));
            $this->_redirect('*/*/');
        }
    }

    public function saveAction ()
    {
        if ($data = $this->getRequest()->getPost()) {

            $satisfaction = $this->_initSatisfaction();
            $satisfaction->addData($data);
            //format date to standart
            // $format = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
            // Mage::helper('mstcore/date')->formatDateForSave($satisfaction, 'active_from', $format);
            // Mage::helper('mstcore/date')->formatDateForSave($satisfaction, 'active_to', $format);

            try {
                $satisfaction->save();

                Mage::getSingleton('vendors/session')->addSuccess(Mage::helper('adminhtml')->__('Satisfaction was successfully saved'));
                Mage::getSingleton('vendors/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $satisfaction->getId()));
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
        Mage::getSingleton('vendors/session')->addError(Mage::helper('adminhtml')->__('Unable to find Satisfaction to save'));
        $this->_redirect('*/*/');
    }

    public function deleteAction ()
    {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $satisfaction = Mage::getModel('helpdesk/satisfaction');

                $satisfaction->setId($this->getRequest()
                    ->getParam('id'))
                    ->delete();

                Mage::getSingleton('vendors/session')->addSuccess(
                        Mage::helper('adminhtml')->__('Satisfaction was successfully deleted'));
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
        $idss = $this->getRequest()->getParam('satisfaction_id');
         $ids=explode(',', $idss);
        if (!is_array($ids)) {
            Mage::getSingleton('vendors/session')->addError(Mage::helper('adminhtml')->__('Please select Satisfaction(s)'));
        } else {
            try {
                foreach ($ids as $id) {
                    $satisfaction = Mage::getModel('helpdesk/satisfaction')
                        ->setIsMassDelete(true)
                        ->load($id);
                    $satisfaction->delete();
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

    public function _initSatisfaction()
    {
        $satisfaction = Mage::getModel('helpdesk/satisfaction');
        if ($this->getRequest()->getParam('id')) {
            $satisfaction->load($this->getRequest()->getParam('id'));
        }

        Mage::register('current_satisfaction', $satisfaction);

        return $satisfaction;
    }

    /************************/

}