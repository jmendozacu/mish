<?php


class VES_VendorsInventory_Inventory_NotificationlogController extends VES_Vendors_Controller_Action {

    /**
     * Menu Path
     * 
     * @var string 
     */
    protected $_menu_path = 'vendors/inventory_notificationlog/';
    
    /**
     * init layout and set active for current menu
     *
     * @return Magestore_Inventorylowstock_Adminhtml_NotificationlogController
     */
    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('inventory')
                ->_addBreadcrumb(
                        Mage::helper('vendorsinventory')->__('Items Manager'), Mage::helper('vendorsinventory')->__('Item Manager')
        );
        $this->_title($this->__('Inventory'))
             ->_title($this->__('Manage Notification'));
        return $this;
    }

    /**
     * index action
     */
    public function indexAction() {
        $this->_initAction()
                ->renderLayout();
    }

    /**
     * Grid action
     */
    public function gridAction() {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('vendorsinventory/notificationlog_grid')->toHtml()
        );
    }

    /**
     * export grid item to CSV type
     */
    public function exportCsvAction() {
        $fileName = 'notificationlog.csv';
        $content = $this->getLayout()
                ->createBlock('vendorsinventory/notificationlog_grid')
                ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export grid item to XML type
     */
    public function exportXmlAction() {
        $fileName = 'notificationlog.xml';
        $content = $this->getLayout()
                ->createBlock('vendorsinventory/notificationlog_grid')
                ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function viewAction() {
       
        $notificationLogId = $this->getRequest()->getParam('id');
        $model = Mage::getModel('inventorylowstock/sendemaillog')->load($notificationLogId);
        $this->_title($this->__('Inventory'))
             ->_title($this->__('Manage Notification'));
        if ($model->getId()) {
            $data = Mage::getSingleton('vendors/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }
            Mage::register('notificationloglog_data', $model);

            $this->loadLayout()->_setActiveMenu($this->_menu_path);
          
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true)
                    ->removeItem('js', 'mage/adminhtml/grid.js')
                    ->addItem('js', 'magestore/adminhtml/inventory/grid.js');
            $this->_addContent($this->getLayout()->createBlock('vendorsinventory/notificationlog_edit'))
                    ->_addLeft($this->getLayout()->createBlock('vendorsinventory/notificationlog_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('vendors/session')->addError(
                    Mage::helper('vendorsinventory')->__('Notification log does not exist')
            );
            $this->_redirect('*/*/');
        }
    }
    
    public function productsAction() {
        $this->loadLayout();
        
        $this->getLayout()->getBlock('notificationlog.edit.tab.products')
                ->setProducts($this->getRequest()->getPost('oproducts', null));
        $this->renderLayout();
        if (Mage::getModel('vendors/session')->getData('notificationlog_import'))
            Mage::getModel('vendors/session')->setData('notificationlog_import', null);
    }

    public function productsGridAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('notificationlog.edit.tab.products')
                ->setProducts($this->getRequest()->getPost('oproducts', null));
        $this->renderLayout();
    }
    
    public function productAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function productGridAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

}
