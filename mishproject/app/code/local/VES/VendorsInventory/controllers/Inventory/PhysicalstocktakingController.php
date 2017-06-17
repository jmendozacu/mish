<?php

class VES_VendorsInventory_Inventory_PhysicalstocktakingController extends VES_Vendors_Controller_Action {

    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('inventory')
                ->_addBreadcrumb(
                        Mage::helper('vendorsinventory')->__('Manage Physical Stocktaking'), Mage::helper('vendorsinventory')->__('Manage Physical Stocktaking')
        );
        $this->_title($this->__('Inventory'))
                ->_title($this->__('Manage Physical Stocktaking'));
        return $this;
    }

    public function indexAction() {
        $this->loadLayout()
                ->_setActiveMenu('inventory')->_title($this->__('Manage Physical Stocktaking'))->_title($this->__('Stock'))
                ->_addBreadcrumb(Mage::helper('vendorsinventory')->__('Physical Stocktaking'), Mage::helper('vendorsinventory')->__('Inventory'));
        $this->renderLayout();
    }

    public function gridAction() {        
        $this->loadLayout();
        $this->getLayout()->getBlock('vendorsinventory_listphysicalstocktaking_grid');
        $this->renderLayout();
    }

    public function newAction() {
        $this->_title($this->__('Inventory'))
                ->_title($this->__('Add New Physical Stocktaking'));
        if (!Mage::helper('inventoryplus')->isWarehouseEnabled()) {
            $this->_redirect('*/*/prepare');
        } else {
            $this->loadLayout()->_setActiveMenu('inventory')
                    ->_setActiveMenu('inventory')->_title($this->__('Physical Stocktaking'))->_title($this->__('Physical Stocktaking'))
                    ->_addBreadcrumb(Mage::helper('vendorsinventory')->__('Physical Stocktaking'), Mage::helper('vendorsinventory')->__('Inventory'))
                    ->_addBreadcrumb(Mage::helper('vendorsinventory')->__('Form'), Mage::helper('vendorsinventory')->__('Form'));
            $this->renderLayout();
        }
    }

    public function importproductAction() {
        $FILES = Mage::helper('inventoryplus')->getUploadFile();
        if (isset($FILES['fileToUpload']['name']) && $FILES['fileToUpload']['name'] != '') {
            try {
                $fileName = $FILES['fileToUpload']['tmp_name'];
                $Object = new Varien_File_Csv();
                $dataFile = $Object->getData($fileName);
                $physicalstocktakingProduct = array();
                $physicalstocktakingProducts = array();
                $fields = array();
                $count = 0;
                $physicalstocktakingHelper = Mage::helper('inventoryphysicalstocktaking');
                if (count($dataFile))
                    foreach ($dataFile as $col => $row) {
                        if ($col == 0) {
                            if (!empty($row))
                                foreach ($row as $index => $cell)
                                    $fields[$index] = (string) $cell;
                        }elseif ($col > 0) {
                            if (!empty($row))
                                foreach ($row as $index => $cell) {
                                    if (isset($fields[$index])) {
                                        $physicalstocktakingProduct[$fields[$index]] = $cell;
                                    }
                                }
                            $physicalstocktakingProducts[] = $physicalstocktakingProduct;
                        }
                    }
                $physicalstocktakingHelper->importProduct($physicalstocktakingProducts);
            } catch (Exception $e) {
                Mage::log($e->getMessage(), null, 'inventory_management.log');
            }
        }
    }

    public function prepareAction() {
        $this->_title($this->__('Inventory'))
                ->_title($this->__('Add New Physical Stocktaking'));
        $FILES = Mage::helper('vendorsinventory/adjuststock')->getUploadFile();
        if ($data = $this->getRequest()->getPost() || !Mage::helper('inventoryplus')->isWarehouseEnabled() || $this->getRequest()->getParam('warehouse_id')) { 
            if (isset($FILES['fileToUpload']['name']) && $FILES['fileToUpload']['name'] != '') {
                try {
                    /* Starting upload */
                    $uploader = new Varien_File_Uploader('fileToUpload');

                    // Any extention would work
                    $uploader->setAllowedExtensions(array('csv'));
                    $uploader->setAllowRenameFiles(false);

                    $uploader->setFilesDispersion(false);

                    try {
                        $fileName = $FILES['fileToUpload']['tmp_name'];
                        $Object = new Varien_File_Csv();
                        $dataFile = $Object->getData($fileName);
                        $adjustStockProduct = array();
                        $adjustStockProducts = array();
                        $fields = array();
                        if (count($dataFile))
                            foreach ($dataFile as $col => $row) {
                                if ($col == 0) {
                                    if (!empty($row))
                                        foreach ($row as $index => $cell)
                                            $fields[$index] = (string) $cell;
                                }elseif ($col > 0) {
                                    if (!empty($row))
                                        foreach ($row as $index => $cell) {
                                            if (isset($fields[$index])) {
                                                $adjustStockProduct[$fields[$index]] = $cell;
                                            }
                                        }
                                    $adjustStockProducts[] = $adjustStockProduct;
                                }
                            }
                        if (count($adjustStockProducts)) {
                            $adjustStockProducts['warehouse_id'] = $this->getRequest()->getPost('warehouse_id');
                            Mage::getModel('vendors/session')->setData('physicalstocktaking_product_import', $adjustStockProducts);
                            $this->loadLayout()->_setActiveMenu('inventory');

                            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true)
                                    ->removeItem('js', 'mage/adminhtml/grid.js')
                                    ->addItem('js', 'magestore/adminhtml/inventory/grid.js');
                            $this->_addContent($this->getLayout()->createBlock('vendorsinventory/physicalstocktaking_edit')->setPhysicalstocktakingProducts($adjustStockProducts))
                                    ->_addLeft($this->getLayout()->createBlock('vendorsinventory/physicalstocktaking_edit_tabs')->setPhysicalstocktakingProducts($adjustStockProducts));
                            $this->renderLayout();
                        } else {
                            Mage::getSingleton('vendors/session')->addError(
                                    Mage::helper('inventoryphysicalstocktaking')->__('Unable to find item to save')
                            );
                            $this->_redirect('*/*/new');
                        }
                    } catch (Exception $e) {
                        Mage::log($e->getMessage(), null, 'inventory_management.log');
                    }
                } catch (Exception $e) {
                    Mage::log($e->getMessage(), null, 'inventory_management.log');
                }
            } else {
                if ($this->getRequest()->getPost('categories')) {
                    $categoryIdsString = $this->getRequest()->getPost('categories');
                    $categoryIds = array_map('intval', explode(',', $categoryIdsString));
                    $adjustStockProducts['categoryIds'] = $categoryIds;
                }
                if ($this->getRequest()->getPost('warehouse_id'))
                    $adjustStockProducts['warehouse_id'] = $this->getRequest()->getPost('warehouse_id');

                if (!Mage::helper('inventoryplus')->isWarehouseEnabled()) {
                    $adjustStockProducts['warehouse_id'] = Mage::getModel('inventoryplus/warehouse')->getCollection()->setPageSize(1)->setCurPage(1)
                                    ->getFirstItem()->getId();
                    $adminId = Mage::getSingleton('vendors/session')->getUser()->getId();
                    $canPhysicalAdmins = Mage::getModel('vendorsinventory/permission')->getCollection()
                            ->addFieldToFilter('warehouse_id', $adjustStockProducts['warehouse_id'])
                            ->addFieldToFilter('vendor_id', $adminId)
                            ->addFieldToFilter('can_physical', 1);
                    if (!$canPhysicalAdmins->getSize()) {
                        Mage::getSingleton('vendors/session')->addError(Mage::helper('vendorsinventory')->__('You have not permission to physical stocktaking!'));
                        session_write_close();
                        $this->_redirect('*/*/');
                    }
                } else {

                    if (!$this->getRequest()->getPost('warehouse_id') && $this->getRequest()->getParam('warehouse_id'))
                        $adjustStockProducts['warehouse_id'] = $this->getRequest()->getParam('warehouse_id');
                }

                Mage::getModel('vendors/session')->setData('physicalstocktaking_product_warehouse', $adjustStockProducts);
                $this->loadLayout()->_setActiveMenu('inventory');

                $this->getLayout()->getBlock('head')->setCanLoadExtJs(true)
                        ->removeItem('js', 'mage/adminhtml/grid.js')
                        ->addItem('js', 'magestore/adminhtml/inventory/grid.js');
                $this->_addContent($this->getLayout()->createBlock('vendorsinventory/physicalstocktaking_edit')->setPhysicalstocktakingProducts($adjustStockProducts))
                        ->_addLeft($this->getLayout()->createBlock('vendorsinventory/physicalstocktaking_edit_tabs')->setPhysicalstocktakingProducts($adjustStockProducts));
                $this->renderLayout();
            }
        }else {
            Mage::getSingleton('vendors/session')->addError(
                    Mage::helper('inventoryphysicalstocktaking')->__('Unable to find item to save')
            );
            $this->_redirect('*/*/new');
        }
    }
    
    public function editAction() {
        $adjustStockId = $this->getRequest()->getParam('id');
        $model = Mage::getModel('inventoryphysicalstocktaking/physicalstocktaking')->load($adjustStockId);

        if (!$adjustStockId) {
            $this->_title($this->__('Inventory'))
                    ->_title($this->__('Add New Physical Stocktaking'));
        } else {
            $this->_title($this->__('Inventory'))
                    ->_title($this->__('Edit Physical Stocktaking'));
        }

        if ($model->getId() || $adjustStockId == 0) {
            $data = Mage::getSingleton('vendors/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }
            Mage::register('physicalstocktaking_data', $model);

            $this->loadLayout()->_setActiveMenu('inventory');

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true)
                    ->removeItem('js', 'mage/adminhtml/grid.js')
                    ->addItem('js', 'magestore/adminhtml/inventory/grid.js');
            $this->_addContent($this->getLayout()->createBlock('vendorsinventory/physicalstocktaking_edit'))
                    ->_addLeft($this->getLayout()->createBlock('vendorsinventory/physicalstocktaking_edit_tabs'));

            $this->renderLayout();

        } else {
            Mage::getSingleton('vendors/session')->addError(Mage::helper('inventoryphysicalstocktaking')->__('Adjust Stock does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function productAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('vendorsinventory.physicalstocktaking.edit.tab.products')
                ->setProducts($this->getRequest()->getPost('physicalstocktaking_products', null));
        $this->renderLayout();
        if (Mage::getModel('vendors/session')->getData('physicalstocktaking_product_import')) {
            Mage::getModel('vendors/session')->setData('physicalstocktaking_product_import', null);
        }
    }

    public function productGridAction() { 
        $this->loadLayout();
        $this->getLayout()->getBlock('vendorsinventory.physicalstocktaking.edit.tab.products')
                ->setProducts($this->getRequest()->getPost('physicalstocktaking_products', null));
        $this->renderLayout();
        if (Mage::getModel('vendors/session')->getData('physicalstocktaking_product_import')) {
            Mage::getModel('vendors/session')->setData('physicalstocktaking_product_import', null);
        }
    }
    
    public function saveAction() {
        if ($data = $this->getRequest()->getPost()) {
            $warehouse_id = $data['warehouse_id'];
            $warehouse = Mage::getModel('inventoryplus/warehouse')->load($warehouse_id);
            try {
                if (!isset($data['physicalstocktaking_products']) || empty($data['physicalstocktaking_products'])) {
                    Mage::getSingleton('vendors/session')->addError(
                            Mage::helper('vendorsinventory')->__('Cannot save physical stocktaking with no product')
                    );
                    if (!$this->getRequest()->getParam('id')) {
                        $this->_redirect('vendors/inventory_physicalstocktaking/new');
                        return;
                    } else {
                        $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                        return;
                    }
                }
                $admin = Mage::getModel('vendors/session')->getUser()->getUsername();
                $data['created_by'] = $admin;
                $model = Mage::getModel('inventoryphysicalstocktaking/physicalstocktaking');
                $model->addData($data);
                //create new
                if ($this->getRequest()->getParam('id')) {
                    $model = $model->load($this->getRequest()->getParam('id'));
                    $model->setData('reason', $data['reason'])->save();
                    $model->setData('physicalstocktaking_products', $data['physicalstocktaking_products']);

                } else {
                    $model->create($data, $warehouse);
                }
                //cancel
                if ($this->getRequest()->getParam('cancel')) {
                    $model->cancel();
                    Mage::getSingleton('vendors/session')->addSuccess(
                            Mage::helper('vendorsinventory')->__('The physical stocktaking has been successfully canceled.')
                    );
                    return $this->_redirect('*/*/');
                }
                /* update products in physical stock-taking */
                $model->saveProducts();
                /* remove scanned barcode data */
//                $this->removeScanData();
                
                if ($this->getRequest()->getParam('confirm') || $this->getRequest()->getParam('confirmadjust')) {
                    /* confirm */
                    $model->confirm($data);
                    $adjustModel = $model->confirmAdjust();
                    /* save product location when only confirm Physical Stocktaking*/
                    if ($this->getRequest()->getParam('confirm') && !$this->getRequest()->getParam('confirmadjust')) {
                        $adjustModel->saveLocationProduct();
                    }
                    /* do adjust */
                    if ($this->getRequest()->getParam('confirmadjust')) {
                        $adjustModel->doAdjust($data);
                        /* continue do adjust stock? */
                        if ($adjustModel->getStatus() == Magestore_Inventoryplus_Model_Adjuststock::STATUS_PROCESSING) {
                            return $this->_redirect('vendors/inventory_adjuststock/run', array('id' => $adjustModel->getId(), 'resum' => 1));
                        }
                        $url = Mage::helper('adminhtml')->getUrl('vendors/inventory_adjuststock/edit', array('id' => $adjustModel->getId()));
                        Mage::getSingleton('vendors/session')->addSuccess(Mage::helper('vendorsinventory')->__('The physical stocktaking has been confirmed.'));
                        Mage::getSingleton('vendors/session')->addSuccess(Mage::helper('vendorsinventory')->__('A stock adjustment (Id %s) has already created and been completed automatically. You can <a href="%s"/>Click here</a> to view stock adjustment.', $adjustModel->getId(), $url));
                    } else {
                        $url = Mage::helper('adminhtml')->getUrl('vendors/inventory_adjuststock/edit', array('id' => $adjustModel->getId()));
                        Mage::getSingleton('vendors/session')->addSuccess(Mage::helper('vendorsinventory')->__('The physical stocktaking has been confirmed. A pending stock adjustment (Id %s) has been successfully created. Now you can <a href="%s"/>Click here</a> to update stock of the warehouse.', $adjustModel->getId(), $url));
                    }
                } else {
                    Mage::getSingleton('vendors/session')->addSuccess(
                            Mage::helper('vendorsinventory')->__('The physical stocktaking has been saved successfully.')
                    );
                }
                Mage::getSingleton('vendors/session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $this->_redirect('*/*/edit', array('id' => $model->getId()));
                }
                if ($this->getRequest()->getParam('exportpdf')) {
                    return $this->_redirect('*/*/edit', array('id' => $model->getId(), 'exportpdf' => true));
                }
                return $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('vendors/session')->addError($e->getMessage());
                Mage::getSingleton('vendors/session')->setFormData($data);
                return $this->_redirect('*/*/edit', array('id' => $model->getId()));
            }
        }
        Mage::getSingleton('vendors/session')->addError(
                Mage::helper('vendorsinventory')->__('Unable to find physical stocktaking to save.')
        );
        $this->_redirect('*/*/');
    }


    /**
     * export grid item to CSV type
     */
    public function exportCsvAction() {
        $fileName = 'physicalstocktaking.csv';
        $content = $this->getLayout()
                ->createBlock('vendorsinventory/physicalstocktaking_listphysicalstocktaking_grid')
                ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Edit by simon
     */
    public function exportpdfstocktakingAction() {
                    
        $stocktaking_id = $this->getRequest()->getParam('id');
        $productCollection = Mage::getModel('catalog/product')->getCollection()
                ->addAttributeToSelect('*');
        $productCollection->joinTable(
                'inventoryphysicalstocktaking/physicalstocktaking_product', 'product_id=entity_id', array(
            'physicalstocktaking_id' => 'physicalstocktaking_id',
            'old_qty' => 'old_qty',
            'adjust_qty' => 'adjust_qty',
            'updated_qty' => 'updated_qty',
            'product_location' => 'product_location',        
                ), null, 'left'
        );
        $stocktaking_products = $productCollection->addFieldToFilter('physicalstocktaking_id', $stocktaking_id);
        $img = Mage::getDesign()->getSkinUrl('images/logo_email.gif', array('_area' => 'frontend'));
        $contents = '<div><img src="' . $img . '" /></div>';
        $contents .= $this->getLayout()->createBlock('vendorsinventory/physicalstocktaking')
                ->setStocktakingid($stocktaking_id)
                ->setStocktakingproducts($stocktaking_products)
                ->setTemplate('inventoryphysicalstocktaking/physicalstocktaking/stocktaking.phtml')
                ->toHtml();
        include("lib/MPDF56/mpdf.php");
        $mpdf = new mPDF();

        $mpdf->WriteHTML($contents);
        $fileName = 'stock-taking-list-'. Mage::helper('core')->formatDate(now(),'short') . '-' . $stocktaking_id;
        $mpdf->Output($fileName . '.pdf', 'D');
    }

    /**
     * Initialize category object in registry
     *
     * @return Mage_Catalog_Model_Category
     */
    protected function _initCategory() {
        $categoryId = (int) $this->getRequest()->getParam('id', false);
        $storeId = (int) $this->getRequest()->getParam('store');

        $category = Mage::getModel('catalog/category');
        $category->setStoreId($storeId);

        if ($categoryId) {
            $category->load($categoryId);
            if ($storeId) {
                $rootId = Mage::app()->getStore($storeId)->getRootCategoryId();
                if (!in_array($rootId, $category->getPathIds())) {
                    $this->_redirect('*/*/', array('_current' => true, 'id' => null));
                    return false;
                }
            }
        }

        Mage::register('category', $category);
        Mage::register('current_category', $category);

        return $category;
    }

    /**
     * Get tree node (Ajax version)
     */
    public function categoriesJsonAction() {
        if ($categoryId = (int) $this->getRequest()->getPost('id')) {
            $this->getRequest()->setParam('id', $categoryId);

            if (!$category = $this->_initCategory()) {
                return;
            }
            $this->getResponse()->setBody(
                $this->getLayout()->createBlock('adminhtml/catalog_category_tree')
                    ->setTemplate('ves_vendorsinventory/physicalstocktaking/categories.phtml')
                    ->getTreeJson($category)
            );
        }
    }

    /** end edit by simon */

    /**
     * export grid item to XML type
     */
    public function exportXmlAction() {
        $fileName = 'physicalstocktaking.xml';
        $content = $this->getLayout()
                ->createBlock('vendorsinventory/physicalstocktaking_listphysicalstocktaking_grid')
                ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function backupproductdataAction()
    {
        $productData = $this->getRequest()->getParam('product_data');
        Mage::getSingleton("vendors/session")->setData("product_data", $productData);
    }
    
    /**
     * Reset scan data
     * 
     */
    public function removeScanData() {
        if (!Mage::helper('core')->isModuleEnabled('Magestore_Inventorybarcode')) {
            return;
        }
        $action = Magestore_Inventoryphysicalstocktaking_Block_Adminhtml_Physicalstocktaking_Scanbarcode::SCAN_ACTION;
        Mage::getModel('inventorybarcode/barcode_scanitem')->reset($action);
    }

}
