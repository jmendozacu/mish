<?php
class Mirasvit_Rma_RmaController extends Mage_Core_Controller_Front_Action
{
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }

    public function preDispatch()
    {
        parent::preDispatch();
        $action = $this->getRequest()->getActionName();
        if ($action != 'external' && $action != 'postexternal') {
            if (!Mage::getSingleton('customer/session')->authenticate($this)) {
                $this->setFlag('', 'no-dispatch', true);
            }
        }
    }

    public function indexAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->renderLayout();
    }

    protected function _initRma()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            $customer = $this->_getSession()->getCustomer();
            $rma = Mage::getModel('rma/rma')->load($id);
            if ($rma->getId() > 0 && $rma->getCustomerId() == $customer->getId()) {
                Mage::register('current_rma', $rma);
                return $rma;
            }
        }
    }

    public function viewAction()
    {
        if ($rma = $this->_initRma()) {
            if ($this->getRequest()->getParam('shipping_confirmation')) {
                $rma->confirmShipping();
                $session  = $this->_getSession();
                $session->addNotice(Mage::helper('rma')->__('Shipping is confirmed. Thank you!'));
            }
            $this->loadLayout();
            $this->_initLayoutMessages('customer/session');
            $this->renderLayout();
        } else {
            $this->_forward('no_rote');
        }
    }

    public function newAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->renderLayout();
    }

    public function saveAction()
    {
        $session  = $this->_getSession();
        $customer = $session->getCustomer();
        $data = $this->getRequest()->getParams();

        $items = $data['items'];
        unset($data['items']);

        try {
            $rma = Mage::helper('rma/process')->createRmaFromPost($data, $items, $customer);
            $session->addSuccess($this->__('RMA was successfuly created'));
            $this->_redirect('*/*/view', array('id' => $rma->getId()));
        } catch (Mage_Core_Exception $e) {
            $session->addError($e->getMessage());
            $session->setFormData($data);
            if ($this->getRequest()->getParam('id')) {
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            } else {
                $this->_redirect('*/*/add', array('order_id' => $this->getRequest()->getParam('order_id')));
            }
        }
    }

    public function savecommentAction()
    {
        $session  = $this->_getSession();
        $customer = $session->getCustomer();
        $rmaId = $this->getRequest()->getParam('id');
        if (!$rma = $this->_initRma()) {
            $this->_redirect('*/*/index');
            return;
        }
        try {
            Mage::helper('rma/process')->createCommentFromPost($rma, $this->getRequest()->getParams());
            $session->addSuccess($this->__('Your comment was successfuly added'));
            $this->_redirect('*/*/view', array('id' => $rma->getId()));
        } catch (Mage_Core_Exception $e) {
            $session->addError($e->getMessage());
            $this->_redirectUrl($rma->getUrl());
        }
    }

    /**
     * @depricated
     */
    public function printAction()
    {
        if (!$rma = $this->_initRma()) {
            return;
        }
        $this->loadLayout('print');
        $this->renderLayout();
    }
}