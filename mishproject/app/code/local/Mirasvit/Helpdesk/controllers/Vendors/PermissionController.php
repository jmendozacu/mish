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


class Mirasvit_Helpdesk_Vendors_PermissionController extends VES_Vendors_Controller_Action
{
    protected function _initAction ()
    {
        $this->loadLayout()->_setActiveMenu('helpdesk');

        return $this;
    }

    public function indexAction ()
    {
        $this->_title($this->__('Permissions'));
        $this->_initAction();
        $this->_addContent($this->getLayout()
            ->createBlock('helpdesk/vendors_permission'));
        $this->renderLayout();
    }

    public function addAction ()
    {
        $this->_title($this->__('New Permission'));

        $this->_initPermission();

        $this->_initAction();
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Permission  Manager'),
                Mage::helper('adminhtml')->__('Permission Manager'), mage::getUrl('*/*/'));
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Add Permission '), Mage::helper('adminhtml')->__('Add Permission'));

        $this->getLayout()
            ->getBlock('head')
            ->setCanLoadExtJs(true);
        $this->_addContent($this->getLayout()->createBlock('helpdesk/vendors_permission_edit'));
        $this->renderLayout();
    }

    public function editAction ()
    {
        $permission = $this->_initPermission();

        if ($permission->getId()) {
            $this->_title($this->__("Edit Permission"));
            $this->_initAction();
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Permissions'),
                    Mage::helper('adminhtml')->__('Permissions'), mage::getUrl('*/*/'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Edit Permission '),
                    Mage::helper('adminhtml')->__('Edit Permission '));

            $this->getLayout()
                ->getBlock('head')
                ->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('helpdesk/vendors_permission_edit'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('vendors/session')->addError(Mage::helper('adminhtml')->__('The Permission does not exist.'));
            $this->_redirect('*/*/');
        }
    }

    public function saveAction ()
    {
        if ($data = $this->getRequest()->getPost()) {

            $permission = $this->_initPermission();
            $permission->addData($data);
            //format date to standart
            // $format = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
            // Mage::helper('mstcore/date')->formatDateForSave($permission, 'active_from', $format);
            // Mage::helper('mstcore/date')->formatDateForSave($permission, 'active_to', $format);

            try {
                $permission->save();

                Mage::getSingleton('vendors/session')->addSuccess(Mage::helper('adminhtml')->__('Permission was successfully saved'));
                Mage::getSingleton('vendors/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $permission->getId()));
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
        Mage::getSingleton('vendors/session')->addError(Mage::helper('adminhtml')->__('Unable to find Permission to save'));
        $this->_redirect('*/*/');
    }

    public function deleteAction ()
    {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $permission = Mage::getModel('helpdesk/permission');

                $permission->setId($this->getRequest()
                    ->getParam('id'))
                    ->delete();

                Mage::getSingleton('vendors/session')->addSuccess(
                        Mage::helper('adminhtml')->__('Permission was successfully deleted'));
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
        $idss = $this->getRequest()->getParam('permission_id');
         $ids=explode(',', $idss);
        if (!is_array($ids)) {
            Mage::getSingleton('vendors/session')->addError(Mage::helper('adminhtml')->__('Please select Permission(s)'));
        } else {
            try {
                foreach ($ids as $id) {
                    $permission = Mage::getModel('helpdesk/permission')
                        ->setIsMassDelete(true)
                        ->load($id);
                    $permission->delete();
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

    public function _initPermission()
    {
        $permission = Mage::getModel('helpdesk/permission');
        if ($this->getRequest()->getParam('id')) {
            $permission->load($this->getRequest()->getParam('id'));
        }

        Mage::register('current_permission', $permission);

        return $permission;
    }

    /************************/

}