<?php

class Code5_Supplierinventory_Adminhtml_LowstockController extends Mage_Adminhtml_Controller_Action {

    public function indexAction() { 
//        echo 'tst';die;
        // Let's call our initAction method which will set some basic params for each action
        $this->_initAction()->renderLayout();
    }

    public function newAction() {
        // We just forward the new action to a blank edit form
        $this->_forward('edit');
    }
    public function messageAction() {
        $data = Mage::getModel('foo_bar/baz')->load($this->getRequest()->getParam('id'));
        echo $data->getContent();
    }

    /**
     * Initialize action
     *
     * Here, we set the breadcrumbs and the active menu
     *
     * @return Mage_Adminhtml_Controller_Action
     */
    protected function _initAction() {
        $this->loadLayout()
                // Make the active menu match the menu config nodes (without 'children' inbetween)
                ->_setActiveMenu('supplierinventory')
                ->_title($this->__('Supplierinventory'));//->_title($this->__('Baz'))
                //->_addBreadcrumb($this->__('Sales'), $this->__('Sales'))
                //->_addBreadcrumb($this->__('Baz'), $this->__('Baz'));

        return $this;
    }

    /**
     * Check currently called action by permissions for current user
     *
     * @return bool
     */
    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('sales/foo_bar_baz');
    }

}
