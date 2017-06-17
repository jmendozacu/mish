<?php

class VES_VendorsInventory_Inventory_AdjuststockController extends VES_Vendors_Controller_Action {

    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('inventory')
                ->_addBreadcrumb(
                        Mage::helper('vendorsinventory')->__('Adjust stock Manager'), Mage::helper('vendorsinventory')->__('Adjust stock Manager')
        );
        $this->_title($this->__('Inventory'))
                ->_title($this->__('Manage Adjust Stocks'));
        return $this;
    }

    public function indexAction() {
        $this->loadLayout()
                ->_setActiveMenu('inventory')->_title($this->__('Adjust Stock'))->_title($this->__('Stock'))
                ->_addBreadcrumb(Mage::helper('vendorsinventory')->__('Adjust Stock'), Mage::helper('vendorsinventory')->__('Inventory'));
        //->_addBreadcrumb(Mage::helper('vendorsinventory')->__('Withdrawal'), Mage::helper('vendorsinventory')->__('Withdrawal'));
        $this->renderLayout();
    }

    public function gridAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('vendorsinventory_listadjuststock_grid');
        $this->renderLayout();
    }

    public function newAction() {
        $this->_title($this->__('Inventory'))
                ->_title($this->__('Add New Adjust Stock'));
        if (!Mage::helper('inventoryplus')->isWarehouseEnabled()) {
            $this->_redirect('*/*/prepare');
        } else {
            $this->loadLayout();
            $this->_setActiveMenu('inventory')
                    ->_setActiveMenu('inventory')->_title($this->__('Adjust Stock'))->_title($this->__('Adjust Stock'))
                    ->_addBreadcrumb(Mage::helper('vendorsinventory')->__('Adjust Stock'), Mage::helper('vendorsinventory')->__('Inventory'))
                    ->_addBreadcrumb(Mage::helper('vendorsinventory')->__('Form'), Mage::helper('vendorsinventory')->__('Form'));
            $this->renderLayout();
        }
    }

    public function prepareAction() {
        $this->_title($this->__('Inventory'))
                ->_title($this->__('Add New Adjust Stock'));
        $data = $this->getRequest()->getPost();
        if (!$data && Mage::helper('vendorsinventory/adjuststock')->isWarehouseEnabled() && !$this->getRequest()->getParam('warehouse_id')) {
            $this->_getSession()->addError(
                    Mage::helper('vendorsinventory')->__('Unable to find item to save')
            );
            $this->_redirect('*/*/index');
        }

        $adjustStockProduct = array();
        $adjustStockProducts = array();
        if ($this->getRequest()->getPost('warehouse_id'))
            $adjustStockProducts['warehouse_id'] = $this->getRequest()->getPost('warehouse_id');

        if (!Mage::helper('vendorsinventory/adjuststock')->isWarehouseEnabled()) {
            $adjustStockProducts['warehouse_id'] = Mage::getModel('inventoryplus/warehouse')->getCollection()
                            ->getFirstItem()->getId();

            $adminId = Mage::getSingleton('vendors/session')->getUser()->getId();
            $canPhysicalAdmins = Mage::getModel('inventoryplus/warehouse_permission')->getCollection()
                    ->addFieldToFilter('warehouse_id', $adjustStockProducts['warehouse_id'])
                    ->addFieldToFilter('vendor_id', $adminId)
                    ->addFieldToFilter('can_adjust', 1);
            if (!$canPhysicalAdmins->getSize()) {
                $this->_getSession()->addError(Mage::helper('vendorsinventory')->__('You have not permission to adjust stock!'));
                session_write_close();
                $this->_redirect('*/*/');
            }
        } else {
            if (!$this->getRequest()->getPost('warehouse_id') && $this->getRequest()->getParam('warehouse_id'))
                $adjustStockProducts['warehouse_id'] = $this->getRequest()->getParam('warehouse_id');
        }
        $files = Mage::helper('vendorsinventory/adjuststock')->getUploadFile();
        if (isset($files['fileToUpload']['name']) && $files['fileToUpload']['name'] != '') {
            /* process upload file */
            $this->_processUploadFile($files);
        } else {

            $this->_getSession()->setData('adjuststock_product_warehouse', $adjustStockProducts);
            $this->loadLayout()->_setActiveMenu('inventory');
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true)
                    ->removeItem('js', 'mage/adminhtml/grid.js')
                    ->addItem('js', 'magestore/adminhtml/inventory/grid.js');
            $this->_addContent($this->getLayout()->createBlock('vendorsinventory/adjuststock_edit')->setAdjustStockProducts($adjustStockProducts))
                    ->_addLeft($this->getLayout()->createBlock('vendorsinventory/adjuststock_edit_tabs')->setAdjustStockProducts($adjustStockProducts))
            ;
            $this->renderLayout();
        }
    }

    public function saveAction() {
        if ($data = $this->getRequest()->getPost()) {
            $model = Mage::getModel('inventoryplus/adjuststock');
            $admin = Mage::getModel('vendors/session')->getUser()->getVendorId();
            $data['created_by'] = $admin;
            $model->addData($data);
            $warehouse_id = $data['warehouse_id'];
            $warehouse = Mage::getModel('inventoryplus/warehouse')->load($warehouse_id);
            $model->setWarehouse($warehouse);
            try {
                if ($this->getRequest()->getParam('id')) {
                    $model->setId($this->getRequest()->getParam('id'));
                    /* Cancel an adjuststock */
                    if ($this->getRequest()->getParam('cancel')) {
                        Mage::helper('inventoryplus/adjuststock')->cancelAdjuststock($model);
                        return $this->_redirect('*/*/');
                    }
                }
                /* validate post adjust stock data */
                if (!isset($data['adjuststock_products']) || empty($data['adjuststock_products'])) {
                    Mage::getSingleton('vendors/session')->addError(
                            Mage::helper('inventoryplus')->__('Can\'t save stock adjustment without product')
                    );
                    if (!$this->getRequest()->getParam('id')) {
                        return $this->_redirect('vendors/inventory_adjuststock/new');
                    } elseif ($this->getRequest()->getParam('back')) {
                        return $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    }
                    return $this->_redirect('*/*/');
                }
                /* create a new adjuststock */
                $this->_saveAdjustStock($model, $warehouse, $data);

                /* update products in adjust stock */
                $model->saveProducts();

                /* do adjust */
                if ($this->getRequest()->getParam('confirm')) {
                    $model->doAdjust($data);
                }

                /* continue do adjust stock? */
                if ($model->getStatus() == Magestore_Inventoryplus_Model_Adjuststock::STATUS_PROCESSING) {
                    return $this->_redirect('*/*/run', array('id' => $model->getId(), 'resum' => 1));
                }

                /* prepare messages */
                $this->_prepareMessageAfterAdjust();

                Mage::getSingleton('vendors/session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $this->_redirect('*/*/edit', array('id' => $model->getId()));
                }
                return $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('vendors/session')->addError($e->getMessage());
                Mage::getSingleton('vendors/session')->setFormData($data);
                return $this->_redirect('*/*/edit', array('id' => $model->getId()));
            }
        }
        $this->_getSession()->addError(Mage::helper('inventoryplus')->__('Unable to find adjust stock to create'));
        $this->_redirect('*/*/');
    }

    protected function _prepareMessageAfterAdjust() {
        if ($this->getRequest()->getParam('confirm')) {
            $this->_getSession()->addSuccess(Mage::helper('vendorsinventory')->__('The stock adjustment has been confirmed successfully.'));
        } else {
            $this->_getSession()->addSuccess(Mage::helper('vendorsinventory')->__('The stock adjustment has been saved successfully.'));
        }
    }

    protected function _saveAdjustStock($model, $warehouse, $data) {
        if (!$this->getRequest()->getParam('id')) {
            $model->create($data, $warehouse);
        } else {
            $model->setData('reason', $data['reason'])->save();
            $model->setData('stock_data', $data['adjuststock_products']);
            if (isset($data['reason'])) {
                $model->setData('reason', $data['reason'])->save();
            }
        }
    }

    public function productAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('vendorsinventory.adjuststock.edit.tab.products')->setProducts($this->getRequest()->getPost('adjuststock_products', null));
        $this->renderLayout();
        if (Mage::getSingleton('vendors/session')->getData('adjuststock_product_import')) {
            Mage::getSingleton('vendors/session')->setData('adjuststock_product_import', null);
        }
    }

    public function productGridAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('vendorsinventory.adjuststock.edit.tab.products')
                ->setProducts($this->getRequest()->getPost('adjuststock_products', null));
        $this->renderLayout();
    }

    protected function _processUploadFile($files) {        
        try {
            /* Starting upload */
            $uploader = new Varien_File_Uploader('fileToUpload');

            // Any extention would work
            $uploader->setAllowedExtensions(array('csv'));
            $uploader->setAllowRenameFiles(false);

            $uploader->setFilesDispersion(false);

            $fileName = $files['fileToUpload']['tmp_name'];
            $Object = new Varien_File_Csv();
            $dataFile = $Object->getData($fileName);

            $fields = array();
            if (count($dataFile))
                foreach ($dataFile as $col => $row) {
                    if ($col == 0) {
                        if (count($row))
                            foreach ($row as $index => $cell)
                                $fields[$index] = (string) $cell;
                    }elseif ($col > 0) {
                        if (count($row))
                            foreach ($row as $index => $cell) {
                                if (isset($fields[$index])) {
                                    $adjustStockProduct[$fields[$index]] = $cell;
                                }
                            }
                        $adjustStockProducts[] = $adjustStockProduct;
                    }
                }
            if (count($adjustStockProducts)) {

                Mage::getModel('vendors/session')->setData('adjuststock_product_import', $adjustStockProducts);
                $this->loadLayout()->_setActiveMenu('inventory');
                $this->getLayout()->getBlock('head')->setCanLoadExtJs(true)
                        ->removeItem('js', 'mage/adminhtml/grid.js')
                        ->addItem('js', 'magestore/adminhtml/inventory/grid.js');
                $this->_addContent($this->getLayout()->createBlock('vendorsinventory/adjuststock_edit')->setAdjustStockProducts($adjustStockProducts))
                        ->_addLeft($this->getLayout()->createBlock('vendorsinventory/adjuststock_edit_tabs')->setAdjustStockProducts($adjustStockProducts));
                $this->renderLayout();
            } else {
                Mage::getSingleton('vendors/session')->addError(Mage::helper('inventoryplus')->__('Unable to find item to save'));
                $this->_redirect('*/*/new');
            }
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'inventory_management.log');
        }
    }

    public function importproductAction() {        
        $files = Mage::helper('inventoryplus')->getUploadFile();
        if (!isset($files['fileToUpload']['name']) || !$files['fileToUpload']['name']) {
            return;
        }
        try {
            $fileName = $files['fileToUpload']['tmp_name'];
            $Object = new Varien_File_Csv();
            $dataFile = $Object->getData($fileName);
            $adjuststockProduct = array();
            $adjuststockProducts = array();
            $fields = array();
            $count = 0;
            $adjuststockHelper = Mage::helper('inventoryplus/adjuststock');
            if (!count($dataFile)) {
                return;
            }
            foreach ($dataFile as $col => $row) {
                if ($col == 0) {
                    if (!count($row))
                        continue;
                    foreach ($row as $index => $cell)
                        $fields[$index] = (string) $cell;
                }elseif ($col > 0) {
                    if (!count($row))
                        continue;
                    foreach ($row as $index => $cell) {
                        if (isset($fields[$index])) {
                            $adjuststockProduct[$fields[$index]] = $cell;
                        }
                    }
                    $adjuststockProducts[] = $adjuststockProduct;
                }
            }
            $adjuststockHelper->importProduct($adjuststockProducts);
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'inventory_management.log');
        }
    }

    public function editAction() {
        $this->_title($this->__('Inventory'))
                ->_title($this->__('Edit New Adjust Stock'));

        $adjustStockId = $this->getRequest()->getParam('id');
        $model = Mage::getModel('inventoryplus/adjuststock')->load($adjustStockId);
        if ($model->getId() || $adjustStockId == 0) {
            $data = Mage::getSingleton('vendors/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }
            Mage::register('adjuststock_data', $model);

            $this->loadLayout()->_setActiveMenu('inventory');
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true)
                    ->removeItem('js', 'mage/adminhtml/grid.js')
                    ->addItem('js', 'magestore/adminhtml/inventory/grid.js');
            $this->_addContent($this->getLayout()->createBlock('vendorsinventory/adjuststock_edit'))
                    ->_addLeft($this->getLayout()->createBlock('vendorsinventory/adjuststock_edit_tabs'));
            $this->renderLayout();
        } else {
            $this->_getSession()->addError(Mage::helper('inventoryplus')->__('Adjust Stock does not exist'));
            $this->_redirect('*/*/');
        }
    }

}
