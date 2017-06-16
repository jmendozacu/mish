<?php
class VES_VendorsInventory_Inventory_LowstockController extends VES_Vendors_Controller_Action {
    
    
    /**
     * init layout and set active for current menu
     *
     * @return Magestore_Inventorylowstock_Adminhtml_NotificationlogController
     */
    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('inventory')
                ->_addBreadcrumb(
                        Mage::helper('vendorsinventory')->__('Low Stock Listing'), Mage::helper('vendorsinventory')->__('Low Stock Listing')
        );
        $this->_title($this->__('Inventory'))
             ->_title($this->__('Low Stock Listing'));
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
        $this->renderLayout();
    }

    /**
     * Change supplier
     * 
     * @return type
     */
    public function changesupplierAction() {
        $supplierId = $this->getRequest()->getPost('supplier_id');
        Mage::getSingleton('vendors/session')->setData('lowstock_curr_supplier_id', $supplierId);
    }

    public function createpoAction() {
        $helperClass = Mage::helper('vendorsinventory/draftpo');
        if ($helperClass->getDraftPO()->getId()) {
            Mage::getSingleton('vendors/session')->addNotice(
                    $helperClass->__('There was an existed draft purchase order. Please process it before creating new one'));
            return $this->_redirect('*/*/index', $this->_helper()->prepareParams());
        }
        $data = $this->_helper()->prepareDataForDraftPO();
        try {
            if (!isset($data['product_data']) || !count($data['product_data'])) {
                throw new Exception($this->_helper()->__('There is no product needed to purchase.'));
            }
            $model = Mage::getModel('inventorypurchasing/purchaseorder_draftpo')
                    ->addData($data);
            $model->setCreatedAt(now())
                    ->setCreatedBy($this->_getUser()->getVendorId());
            $model->setType(Magestore_Inventorypurchasing_Model_Purchaseorder_Draftpo::LOWSTOCK_TYPE);
            $model->create();
            Mage::getSingleton('vendors/session')
                    ->addSuccess($this->_helper()->__('The purchase data have been saved successfully as draft purchase order(s).'));
            return $this->_redirect('vendors/inventory_draftpo/view', array('id' => $model->getId()));
        } catch (Exception $ex) {
            Mage::getSingleton('vendors/session')
                    ->addError($this->_helper()->__('There is error while creating new draft purchase order.'));
            Mage::getSingleton('vendors/session')->addError($ex->getMessage());
            return $this->_redirect('*/*/index', $this->_helper()->prepareParams());
        }
    }
    
    
    /**
     * export grid item to CSV type
     */
    public function exportCsvAction() {
        $fileName = 'lowstocks.csv';
        $content = $this->getLayout()
                ->createBlock('vendorsinventory/lowstock_grid')
                ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export grid item to XML type
     */
    public function exportXmlAction() {
        $fileName = 'lowstocks.xml';
        $content = $this->getLayout()
                ->createBlock('vendorsinventory/lowstock_grid')
                ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    protected function _helper() {
        return Mage::helper('vendorsinventory/lowstock');
    }

    /**
     * Get logged-in user
     * 
     * @return Varien_Object
     */
    protected function _getUser() {
        return Mage::getSingleton('vendors/session')->getUser();
    }

    /**
     * 
     * 
     * @return boolean
     */
    protected function _hasSupplier() {
        $colection = Mage::getModel('inventorypurchasing/supplier')->getCollection();
        if ($colection->getSize() > 0)
            return true;
        return false;
    }
    
}
