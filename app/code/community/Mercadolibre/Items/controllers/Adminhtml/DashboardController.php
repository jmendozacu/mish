<?php
class Mercadolibre_Items_Adminhtml_DashboardController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->_title($this->__('Mercadolibre Dashboard'));
		$this->loadLayout();
        $this->_setActiveMenu('items/items');
		$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Mercadolibre Dashboard'), Mage::helper('adminhtml')->__('Mercadolibre Dashboard'));
        $this->renderLayout();
    }
	 protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('dashboard');
    }
}
