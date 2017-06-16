<?php

/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_Inventorydropship
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Inventorydropship Adminhtml Controller
 * 
 * @category    Magestore
 * @package     Magestore_Inventorydropship
 * @author      Magestore Developer
 */
class Magestore_Inventorydropship_Adminhtml_Indr_InventorydropshipController extends Magestore_Inventoryplus_Controller_Action {

    /*
     * Menu path
     * 
     * @var string
     */
    protected $_menu_path = 'inventoryplus/dropship';
    
    /**
     * init layout and set active for current menu
     *
     * @return Magestore_Inventorydropship_Adminhtml_Indr_InventorydropshipController
     */
    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu($this->_menu_path);
        return $this;
    }

    public function indexAction()
    {
        $this->_initAction();
        $this->_title($this->__('Inventory'))
            ->_title($this->__('Manage Drop Shipments'));
        $this->renderLayout();
    }

    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('inventorydropship/adminhtml_inventorydropship_grid')->toHtml()
        );
    }

    public function selectsupplierAction() {
        $div_id = $this->getRequest()->getParam('div_id');

        if ($div_id) {
            $div_id1 = str_replace('show_select_warehouse_supplier_', '', $div_id);
            if ($div_id1 == $div_id)
                $div_id1 = str_replace('show_select_only_warehouse_', '', $div_id);
            if ($div_id1 == $div_id)
                $div_id1 = str_replace('show_select_only_supplier_', '', $div_id);
            $div_id = $div_id1;
            $div_id = explode('_', $div_id);
            $orderItemId = $div_id[0];
        }else {
            $orderItemId = $this->getRequest()->getParam('item_id');
        }


        $orderItem = Mage::getModel('sales/order_item')->load($orderItemId);

        if ($orderItem->getProductType() == 'configurable') {
            $itemData = unserialize($orderItem->getData('product_options'));
            $productSku = $itemData['simple_sku'];
            $productId = Mage::getModel('catalog/product')->getIdBySku($productSku);
        } else {
            if ($div_id) {
                $productId = $div_id[1];
            } else {
                $productId = $orderItem->getProductId();
            }
        }

        $blockSingleton = Mage::getBlockSingleton('bundle/adminhtml_sales_order_items_renderer');


        $supplierProductModel = Mage::getModel('inventorypurchasing/supplier_product')->getCollection()
                ->addFieldToFilter('product_id', $productId);
        $return = '';
        if (!empty($supplierProductModel)) {
            $firstSupplier = '';
            $return .= "<select class='supplier-shipment' name='supplier-shipment[items][$orderItemId]' onchange='changeviewsupplier(this,$orderItemId);' id='supplier-shipment[items][$orderItemId]'>";
            foreach ($supplierProductModel as $model) {
                $supplierId = $model->getSupplierId();
                $supplierName = Mage::getModel('inventorypurchasing/supplier')->load($supplierId)->getSupplierName();
                if (!$firstSupplier)
                    $firstSupplier = $supplierId;
                $return .= "<option value='$supplierId' ";
                $return .= ">$supplierName</option>";
            }
            $return .= "</select>";
            $return .= "<br />";
            $return .= "<div style='float:right;'><a id='view_supplier-shipment[items][$orderItemId]' target='_blank' href='" . $this->getUrl('adminhtml/inpu_supplier/edit', array('id' => $firstSupplier)) . "'>" . $this->__('view') . "</a></div>";
        }
        echo $return;
    }

    public function selectwarehouseAction() {
        $div_id = $this->getRequest()->getParam('div_id');
        $div_id = str_replace('show_select_warehouse_supplier_', '', $div_id);
        $div_id = explode('_', $div_id);
        $orderItemId = $div_id[0];
        $productId = $div_id[1];
        $allWarehouse = Mage::helper('inventoryplus/warehouse')->getAllWarehouseNameEnable();
        $warehouseProductModel = Mage::getModel('inventoryplus/warehouse_product')->getCollection()
                ->addFieldToFilter('product_id', $productId)
                ->setOrder('total_qty', 'DESC');
        $warehouseHaveProduct = array();
        $return = "<select class='warehouse-shipment' name='warehouse-shipment[items][$orderItemId]' onchange='changeviewwarehouse(this,$orderItemId);checkStatusAvailableAOrderItemByEvent(this.value,$productId,0,$orderItemId);' id='warehouse-shipment[items][$orderItemId]'>";
        $firstWarehouse = '';
        foreach ($warehouseProductModel as $model) {
            $warehouseId = $model->getwarehouseId();
            $warehouseName = $allWarehouse[$warehouseId];
            $productQty = $model->getTotalQty();

            if ($warehouseName != '') {
                if (!$firstWarehouse)
                    $firstWarehouse = $warehouseId;
                $return .= "<option value='$warehouseId' ";
                $return .= ">$warehouseName(" . $productQty . " product(s))</option>";
                $warehouseHaveProduct[] = $allWarehouse[$warehouseId];
            }
        }
        foreach ($allWarehouse as $warehouseIdKey => $warehouseNameValue) {
            if ($warehouseNameValue != '') {
                if (in_array($allWarehouse[$warehouseIdKey], $warehouseHaveProduct) == false) {
                    if (!$firstWarehouse)
                        $firstWarehouse = $warehouseIdKey;
                    $return .= "<option value='$warehouseIdKey' ";
                    $return .= ">$warehouseNameValue(0 product(s))</option>";
                }
            }
        }

        $return .= "</select><br />";
        $return .= "<div style='float:right;'><a id='view_warehouse-shipment[items][$orderItemId]' target='_blank' href='" . $this->getUrl('adminhtml/inp_warehouse/edit', array('id' => $firstWarehouse)) . "'>" . $this->__('view') . "</a></div>";
        echo $return;
    }

    public function checkparentAction() {
        $itemId = $this->getRequest()->getParam('itemid');
        $results = Mage::getResourceModel('inventorydropship/inventorydropship')->getParentItemIdByItem($itemId);
        $parentId = $itemId;
        foreach ($results as $result) {
            if ($result['parent_item_id']) {
                $parentItem = Mage::getModel('sales/order_item')->load($result['parent_item_id']);
                $next = false;
                if ($options = $parentItem->getProductOptions()) {
                    if (isset($options['shipment_type']) && $options['shipment_type'] == Mage_Catalog_Model_Product_Type_Abstract::SHIPMENT_SEPARATELY) {
                        $next = true;
                    }
                }
                if (!$next)
                    $parentId = $result['parent_item_id'];
            }
        }
        echo $parentId;
    }

    public function savedropshipAction() {
        $session = md5(now());
        $data = $this->getRequest()->getPost();
        $orderId = $this->getRequest()->getParam('order_id');
        $order = Mage::getModel('sales/order')->load($orderId);
        $admin = Mage::getModel('admin/session')->getUser();
        $adminName = $admin->getName();
        $adminEmail = $admin->getEmail();
        $items = $data['shipment']['items'];


        $suppliers = $data['supplier-shipment']['items'];

        $supplierNotNeedToConfirmProvide = true;
        if (Mage::getStoreConfig('inventoryplus/dropship/supplier_confirm_provide'))
            $supplierNotNeedToConfirmProvide = false;
        $adminNotNeedToApprove = true;
        if (Mage::getStoreConfig('inventoryplus/dropship/admin_approve'))
            $adminNotNeedToApprove = false;
        $supplierNotNeedToConfirmShipped = true;
        if (Mage::getStoreConfig('inventoryplus/dropship/supplier_confirm_shipped'))
            $supplierNotNeedToConfirmShipped = false;
        $qtyRequest = array();

        $parrentItems = array();
        foreach ($suppliers as $itemId => $supplierId) {
            $parentItemId = 0;
            $orderItem = Mage::getModel('sales/order_item')->load($itemId);


            if (isset($data['shipment']['items'][$orderItem->getParentItemId()]) && $data['shipment']['items'][$orderItem->getParentItemId()]) { //neu cha no đc gán qty to ship
                $items[$itemId] = $data['shipment']['items'][$orderItem->getParentItemId()];
                $parentItemId = $orderItem->getParentItemId();

                $items[$parentItemId] = $data['shipment']['items'][$orderItem->getParentItemId()];
            }



            $supplier = Mage::getModel('inventorypurchasing/supplier')->load($supplierId);
            $supplierName = $supplier->getSupplierName();
            $dropshipModel = Mage::getModel('inventorydropship/inventorydropship')
                    ->getCollection()
                    ->addFieldToFilter('order_id', $orderId)
                    ->addFieldToFilter('supplier_id', $supplierId)
                    ->addFieldToFilter('session', $session)
                    ->setPageSize(1)
                    ->setCurPage(1)
                    ->getFirstItem();

            if (isset($items[$itemId]) && (!is_numeric($items[$itemId]) || $items[$itemId] < 0))
                $items[$itemId] = 0;



            if (!$dropshipModel->getId() && $items[$itemId] > 0) {
                $dropshipModel = Mage::getModel('inventorydropship/inventorydropship');

                /* check status for drop shipment after create */
                if ($supplierNotNeedToConfirmProvide) { //supplier does not need to confirm qty product to provide
                    $statusDropShip = 3; //qty requested = qty confirmed = qty approved; need to ship from supplier
                    if ($supplierNotNeedToConfirmShipped) // supplier does not need to confirm shipped
                        $statusDropShip = 6;
                }else {
                    $statusDropShip = 1;
                    if ($adminNotNeedToApprove) { // admin does not need to approve qty product to supplier ships
                        if ($supplierNotNeedToConfirmShipped) {
                            $statusDropShip = 3;
                        }
                    }
                }

                $dropshipModel->setData('order_id', $orderId)
                        ->setData('increment_id', $order->getIncrementId())
                        ->setData('supplier_id', $supplierId)
                        ->setData('supplier_name', $supplierName)
                        ->setData('supplier_email', $supplier->getSupplierEmail())
                        ->setData('shipping_name', $order->getShippingAddress()->getName())
                        ->setData('created_on', now())
                        ->setData('admin_name', $adminName)
                        ->setData('admin_email', $adminEmail)
                        ->setStatus($statusDropShip)
                        ->setSession($session)
                        ->save();
            }

            if (isset($items[$parentItemId]) && $items[$parentItemId] > 0 && !in_array($parentItemId, $parrentItems)) {

                $parrentItems[] = $parentItemId;

                $orderParrentItem = Mage::getModel('sales/order_item')->load($parentItemId);
                $dropshipProduct = Mage::getModel('inventorydropship/inventorydropship_product');

                $qtyShipped = 0;
                $qtyRequest[$parentItemId] = $items[$parentItemId];
                /* set qty confirmed(offer), qty approve */
                if ($supplierNotNeedToConfirmProvide) {
                    $qtyOffer = $items[$parentItemId];
                    $qtyApprove = $items[$parentItemId];
                    if ($supplierNotNeedToConfirmShipped) {
                        $qtyShipped = $items[$parentItemId];

                        /* return avaiable qty for warehouse */
                        $warehouseOrder = Mage::getModel('inventoryplus/warehouse_order')->getCollection()
                                ->addFieldToFilter('order_id', $orderId)
                                ->addFieldToFilter('product_id', $orderParrentItem->getProductId());

                        $warehouseId = $warehouseOrder->setPageSize(1)->setCurPage(1)->getFirstItem()->getWarehouseId();

                        $warehouseProduct = Mage::getModel('inventoryplus/warehouse_product')->getCollection()
                                        ->addFieldToFilter('warehouse_id', $warehouseId)
                                        ->addFieldToFilter('product_id', $orderParrentItem->getProductId())
                                        ->setPageSize(1)
                                        ->setCurPage(1)
                                        ->getFirstItem();
                        $availableQty = $warehouseProduct->getAvailableQty();
                        try {
                            $warehouseProduct->setAvailableQty($availableQty + $qtyApprove)
                                    ->save();
                        } catch (Exception $e) {
                            Mage::log($e->getMessage(), null, 'inventory_dropship.log');
                        }
                    }
                } else {
                    $qtyApprove = 0;
                    $qtyOffer = 0;
                    $qtyShipped = 0;
                    if ($adminNotNeedToApprove) { // admin does not need to approve qty product to supplier ships
                        if ($supplierNotNeedToConfirmShipped) {
                            $qtyApprove = $items[$parentItemId];
                        }
                    }
                }

                $dropshipProduct->setData('dropship_id', $dropshipModel->getId())
                        ->setData('item_id', $parentItemId)
                        ->setData('supplier_id', $supplier->getId())
                        ->setData('supplier_name', $supplierName)
                        ->setData('product_id', $orderParrentItem->getProductId())
                        ->setData('product_sku', $orderParrentItem->getSku())
                        ->setData('product_name', $orderParrentItem->getName())
                        ->setData('qty_request', $items[$parentItemId])
                        ->setData('qty_offer', $qtyOffer)
                        ->setData('qty_approve', $qtyApprove)
                        ->setData('qty_shipped', $qtyShipped)
                        ->save();
            }

            if ((isset($items[$itemId]) && $items[$itemId] > 0)) {
                $dropshipProduct = Mage::getModel('inventorydropship/inventorydropship_product');

                $qtyShipped = 0;
                $qtyRequest[$itemId] = $items[$itemId];
                /* set qty confirmed(offer), qty approve */
                if ($supplierNotNeedToConfirmProvide) {
                    $qtyOffer = $items[$itemId];
                    $qtyApprove = $items[$itemId];
                    if ($supplierNotNeedToConfirmShipped) {
                        $qtyShipped = $items[$itemId];


                        if ($orderItem->getProductType() == 'configurable') {
                            $itemData = unserialize($orderItem->getData('product_options'));
                            $productSku = $itemData['simple_sku'];
                            $productId = Mage::getModel('catalog/product')->getIdBySku($productSku);
                        } else {
                            $productId = $orderItem->getProductId();
                        }

                        /* return avaiable qty for warehouse */
                        $warehouseOrder = Mage::getModel('inventoryplus/warehouse_order')->getCollection()
                                ->addFieldToFilter('order_id', $orderId)
                                ->addFieldToFilter('product_id', $productId);

                        $warehouseId = $warehouseOrder->setPageSize(1)->setCurPage(1)->getFirstItem()->getWarehouseId();

                        $warehouseProduct = Mage::getModel('inventoryplus/warehouse_product')->getCollection()
                                        ->addFieldToFilter('warehouse_id', $warehouseId)
                                        ->addFieldToFilter('product_id', $productId)
                                        ->setPageSize(1)
                                        ->setCurPage(1)
                                        ->getFirstItem();
                        $availableQty = $warehouseProduct->getAvailableQty();
                        try {
                            $warehouseProduct->setAvailableQty($availableQty + $qtyApprove)
                                    ->save();
                        } catch (Exception $e) {
                            Mage::log($e->getMessage(), null, 'inventory_dropship.log');
                        }
                    }
                } else {
                    $qtyApprove = 0;
                    $qtyOffer = 0;
                    $qtyShipped = 0;
                    if ($adminNotNeedToApprove) { // admin does not need to approve qty product to supplier ships
                        if ($supplierNotNeedToConfirmShipped) {
                            $qtyApprove = $items[$itemId];
                        }
                    }
                }

                $dropshipProduct->setData('dropship_id', $dropshipModel->getId())
                        ->setData('item_id', $itemId)
                        ->setData('supplier_id', $supplier->getId())
                        ->setData('supplier_name', $supplierName)
                        ->setData('product_id', $orderItem->getProductId())
                        ->setData('product_sku', $orderItem->getSku())
                        ->setData('product_name', $orderItem->getName())
                        ->setData('qty_request', $items[$itemId])
                        ->setData('qty_offer', $qtyOffer)
                        ->setData('qty_approve', $qtyApprove)
                        ->setData('qty_shipped', $qtyShipped)
                        ->save();
            }
        }
        $allDropshipCreates = Mage::getModel('inventorydropship/inventorydropship')
                ->getCollection()
                ->addFieldToFilter('order_id', $orderId)
                ->addFieldToFilter('session', $session);
        if (!empty($allDropshipCreates)) {
            Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('inventorydropship')->__('The drop shipment(s) has been created!')
            );

            if ($supplierNotNeedToConfirmProvide) {
                if ($supplierNotNeedToConfirmShipped) {
                    Mage::getSingleton('adminhtml/session')->addSuccess(
                            Mage::helper('inventorydropship')->__('The shipment(s) has been created!')
                    );
                } else {
                    Mage::getSingleton('adminhtml/session')->addNotice(
                            Mage::helper('inventorydropship')->__('Awaiting supplier\'s shipment')
                    );
                }
            } else {
                if ($adminNotNeedToApprove) { // admin does not need to approve qty product to supplier ships
                    if ($supplierNotNeedToConfirmShipped) {
                        Mage::getSingleton('adminhtml/session')->addNotice(
                                Mage::helper('inventorydropship')->__('Awaiting supplier\'s confirmation and shipment')
                        );
                    } else {
                        Mage::getSingleton('adminhtml/session')->addNotice(
                                Mage::helper('inventorydropship')->__('Awaiting supplier\'s confirmation')
                        );
                    }
                } else {
                    Mage::getSingleton('adminhtml/session')->addNotice(
                            Mage::helper('inventorydropship')->__('Awaiting supplier\'s confirmation')
                    );
                }
            }
            $inventoryShipmentData = array();
            try {

                foreach ($allDropshipCreates as $dropshipCreate) {

                    if (!$supplierNotNeedToConfirmProvide) {

                        if ($adminNotNeedToApprove) { // admin does not need to approve qty product to supplier ships
                            if ($supplierNotNeedToConfirmShipped) {
                                Mage::helper('inventorydropship')->sendEmailApproveDropShipToSupplier($dropshipCreate->getId());
                            } else {
                                Mage::helper('inventorydropship')->sendEmailOfferToSupplier($dropshipCreate->getId());
                            }
                        } else {
                            Mage::helper('inventorydropship')->sendEmailOfferToSupplier($dropshipCreate->getId());
                        }
                    }
                    if ($supplierNotNeedToConfirmProvide && !$supplierNotNeedToConfirmShipped) {
                        Mage::helper('inventorydropship')->sendEmailApproveDropShipToSupplier($dropshipCreate->getId());
                    }

                    $savedQtys = array();
                    $productIds = array();
                    $dropshipProductShips = Mage::getModel('inventorydropship/inventorydropship_product')
                            ->getCollection()
                            ->addFieldToFilter('dropship_id', $dropshipCreate->getId());

                    foreach ($dropshipProductShips as $dropshipProductShip) {
                        $savedQtys[$dropshipProductShip->getItemId()] = $dropshipProductShip->getQtyApprove();
                        $productIds[$dropshipProductShip->getItemId()] = $dropshipProductShip->getProductId();
                    }

                    try {
                        //create shipment when supplier approved

                        if ($savedQtys) {

                            $shipment = Mage::getModel('sales/service_order', $order)->prepareShipment($savedQtys);

                            if ($supplierNotNeedToConfirmProvide && $supplierNotNeedToConfirmShipped) {
                                Mage::getModel('admin/session')->setData('break_shipment_event_dropship', true);
                                Mage::getModel('core/session')->setData('break_shipment_event_dropship', true);

                                $order = Mage::getModel('sales/order')->load($dropshipCreate->getOrderId());
                                $transaction = Mage::getModel('core/resource_transaction')
                                        ->addObject($order);

                                $shipment->register();
                                $shipment->getOrder()->setIsInProcess(true);

                                $transactionSave = Mage::getModel('core/resource_transaction')
                                        ->addObject($shipment)
                                        ->addObject($shipment->getOrder())
                                        ->save();
                            }

                            foreach ($savedQtys as $itemShipId => $qtyShip) {
                                $item = Mage::getModel('sales/order_item')->load($itemShipId);


                                if (in_array($item->getProductType(), array('configurable', 'bundle', 'grouped', 'virtual', 'downloadable')))
                                    continue;
                                //row_total_incl_tax       
                                $inventoryShipmentData[$item->getItemId()]['qty'] = '';
                                $inventoryShipmentData[$item->getItemId()]['supplier'] = '';
                                if ($item->getParentItemId()) {
                                    if (isset($data['shipment']['items'][$item->getParentItemId()])) {
                                        $item_parrent = Mage::getModel('sales/order_item')->load($item->getParentItemId());
                                        $options = $item->getProductOptions();
                                        if (isset($options['bundle_selection_attributes'])) {
                                            $option = unserialize($options['bundle_selection_attributes']);

                                            $parentQty = $data['shipment']['items'][$item->getParentItemId()];

                                            $itemQty = (int) $option['qty'] * (int) $parentQty;


                                            $inventoryShipmentData[$item->getItemId()]['qty'] = $itemQty;
                                            $inventoryShipmentData[$item->getItemId()]['qty_request'] = $qtyRequest[$item->getItemId()];


                                            if (isset($data['supplier-shipment']['items'][$item->getParentItemId()]) && $data['supplier-shipment']['items'][$item->getParentItemId()] != '') {
                                                $inventoryShipmentData[$item->getItemId()]['supplier'] = $data['supplier-shipment']['items'][$item->getParentItemId()];
                                            } else {
                                                $inventoryShipmentData[$item->getItemId()]['supplier'] = $data['supplier-shipment']['items'][$item->getItemId()];
                                            }

                                            $inventoryShipmentData[$item->getItemId()]['product_id'] = $item->getProductId();
                                        } else {
                                            $inventoryShipmentData[$item->getItemId()]['qty'] = $data['shipment']['items'][$item->getParentItemId()];
                                            if (isset($data['supplier-shipment']['items'][$item->getParentItemId()]) && $data['supplier-shipment']['items'][$item->getParentItemId()] != '')
                                                $inventoryShipmentData[$item->getItemId()]['supplier'] = $data['supplier-shipment']['items'][$item->getParentItemId()];
                                            else
                                                $inventoryShipmentData[$item->getItemId()]['supplier'] = $data['supplier-shipment']['items'][$item->getItemId()];

                                            $inventoryShipmentData[$item->getItemId()]['product_id'] = $item->getProductId();
                                            $inventoryShipmentData[$item->getItemId()]['price_incl_tax'] = $item->getPriceInclTax();
                                        }
                                    } else {

                                        $inventoryShipmentData[$item->getItemId()]['qty'] = $data['shipment']['items'][$item->getItemId()];
                                        $inventoryShipmentData[$item->getItemId()]['qty_request'] = $qtyRequest[$item->getItemId()];

                                        $inventoryShipmentData[$item->getItemId()]['supplier'] = $data['supplier-shipment']['items'][$item->getItemId()];
                                        $inventoryShipmentData[$item->getItemId()]['product_id'] = $item->getProductId();
                                        $inventoryShipmentData[$item->getItemId()]['price_incl_tax'] = $item->getPriceInclTax();
                                    }
                                } else {

                                    if (!$item->getHasChildren()) {
                                        if (isset($data['shipment']['items'][$item->getItemId()])) {
                                            $inventoryShipmentData[$item->getItemId()]['qty'] = $data['shipment']['items'][$item->getItemId()];
                                            $inventoryShipmentData[$item->getItemId()]['qty_request'] = $qtyRequest[$item->getItemId()];
                                        } elseif (isset($data['shipment']['items'][$item->getParentItemId()])) {
                                            $inventoryShipmentData[$item->getItemId()]['qty'] = $data['shipment']['items'][$item->getParentItemId()];
                                            $inventoryShipmentData[$item->getItemId()]['qty_request'] = $qtyRequest[$item->getParentItemId()];
                                        }
                                        if (isset($data['supplier-shipment']['items'][$item->getItemId()])) {
                                            $inventoryShipmentData[$item->getItemId()]['supplier'] = $data['supplier-shipment']['items'][$item->getItemId()];
                                        } elseif (isset($data['supplier-shipment']['items'][$item->getParentItemId()])) {
                                            $inventoryShipmentData[$item->getItemId()]['supplier'] = $data['supplier-shipment']['items'][$item->getParentItemId()];
                                        }
                                        $inventoryShipmentData[$item->getItemId()]['product_id'] = $item->getProductId();
                                        $inventoryShipmentData[$item->getItemId()]['price_incl_tax'] = $item->getPriceInclTax();
                                    } else {

                                        $supplierName = Mage::getModel('inventorypurchasing/supplier')->load($data['supplier-shipment']['items'][$item->getItemId()])->getSupplierName();
                                        $inventoryShipmentModel = Mage::getModel('inventorydropship/suppliershipment');
                                        $inventoryShipmentModel->setItemId($item->getItemId())
                                                ->setProductId($item->getProductId())
                                                ->setOrderId($orderId)
                                                ->setWarehouseId($data['supplier-shipment']['items'][$item->getItemId()])
                                                ->setWarehouseName($supplierName)
                                                ->setShipmentId($shipment->getId());
                                        if ($supplierNotNeedToConfirmProvide && $supplierNotNeedToConfirmShipped) {
                                            $inventoryShipmentModel->setQtyShipped($data['shipment']['items'][$item->getItemId()]);
                                        } else {
                                            $inventoryShipmentModel->setQtyRequested($qtyRequest[$item->getItemId()]);
                                        }
                                    }
                                }
                                if ($inventoryShipmentData[$item->getItemId()]['qty'] > ($item->getQtyOrdered() - $item->getQtyRefunded())) {
                                    $inventoryShipmentData[$item->getItemId()]['qty'] = ($item->getQtyOrdered() - $item->getQtyRefunded());
                                }
                            }

                            foreach ($inventoryShipmentData as $key => $dataArray) {

                                $supplierName = Mage::getModel('inventorypurchasing/supplier')->load($data['supplier-shipment']['items'][$item->getItemId()])->getSupplierName();
                                $inventoryShipmentModel = Mage::getModel('inventorydropship/suppliershipment');
                                $inventoryShipmentModel->setItemId($key)
                                        ->setProductId($dataArray['product_id'])
                                        ->setOrderId($orderId)
                                        ->setWarehouseId($dataArray['supplier'])
                                        ->setWarehouseName($supplierName)
                                        ->setShipmentId($shipment->getId());
                                if ($supplierNotNeedToConfirmProvide && $supplierNotNeedToConfirmShipped) {
                                    $inventoryShipmentModel->setQtyShipped($dataArray['qty']);
                                } else {
                                    $inventoryShipmentModel->setQtyRequested($dataArray['qty_request']);
                                }

                                $inventoryShipmentModel->save();
                            }
                        }
                    } catch (Exception $e) {
                        Mage::log($e->getMessage(), null, 'inventory_dropship.log');
                    }
                }
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        Mage::dispatchEvent('inventorydropship_dropship_save_after', array('dropship_model' => $dropshipModel));
        $this->_redirect('adminhtml/sales_order/view', array('order_id' => $orderId));
    }


    /* Drop shipment in order tab */

    public function dropshipAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('sales.order.view.dropship')
                ->setDropships($this->getRequest()->getPost('dropships', null));
        $this->renderLayout();
    }

    public function dropshipGridAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('sales.order.view.dropship')
                ->setDropships($this->getRequest()->getPost('dropships', null));
        $this->renderLayout();
    }

    /* cancel dropship from admin */

    public function canceldropshipAction() {
        $dropshipId = $this->getRequest()->getParam('dropship_id');
        $dropship = Mage::getModel('inventorydropship/inventorydropship')->load($dropshipId);
        $dropship->setStatus('5')->save();
        Mage::getSingleton('adminhtml/session')->addSuccess(
                Mage::helper('adminhtml')->__('The drop shipment has been canceled!')
        );
        Mage::helper('inventorydropship')->sendEmailCancelDropShipToSupplier($dropshipId);
        $this->_redirect('*/*/edit', array('id' => $dropshipId));
    }

    /* approve dropship from admin */

    public function approvedropshipAction() {
        if ($data = $this->getRequest()->getPost()) {
            $dropshipId = $data['dropship_id'];
            $itemApproves = $data['item']['approve'];
            $dropshipProducts = Mage::getModel('inventorydropship/inventorydropship_product')
                    ->getCollection()
                    ->addFieldToFilter('dropship_id', $dropshipId);
            $success = false;
            $resource = Mage::getSingleton('core/resource');
            $writeConnection = $resource->getConnection('core_write');
            foreach ($dropshipProducts as $dropshipProduct) {
                if ($itemApproves[$dropshipProduct->getItemId()] && is_numeric($itemApproves[$dropshipProduct->getItemId()]) && $itemApproves[$dropshipProduct->getItemId()] > 0) {
                    $success = true;
                    if ($itemApproves[$dropshipProduct->getItemId()] > $dropshipProduct->getQtyOffer())
                        $itemApproves[$dropshipProduct->getItemId()] = $dropshipProduct->getQtyOffer();
                    $dropshipProduct->setData('qty_approve', $itemApproves[$dropshipProduct->getItemId()])
                            ->save();
                    $pId = $dropshipProduct->getProductId();
                    $itemOrderId = $dropshipProduct->getItemId();
                    $allChildrenIds = array();
                    $orderId = Mage::getModel('inventorydropship/inventorydropship')->load($dropshipId)->getOrderId();
                    $childrenCollection = Mage::getModel('sales/order_item')
                            ->getCollection()
                            ->addFieldToFilter('order_id', $orderId)
                            ->addFieldToFilter('parent_item_id', $itemOrderId);
                    if (!empty($childrenCollection)) {
                        foreach ($childrenCollection as $child) {
                            $allChildrenIds[] = $child->getProductId();
                        }
                    }
                    $code = $itemApproves[$dropshipProduct->getItemId()];
                    $product = Mage::getModel('catalog/product')->load($pId);
                    $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($pId);
                    $manageStock = $stockItem->getManageStock();
                    if ($stockItem->getUseConfigManageStock()) {
                        $manageStock = Mage::getStoreConfig('cataloginventory/item_options/manage_stock', Mage::app()->getStore()->getStoreId());
                    }
                    
                    if ($manageStock) {
                        $results = Mage::getResourceModel('inventorydropship/inventorydropship')->getQtyProductByProduct($pId);
                        foreach ($results as $result) {
                            $oldQtyProduct = $result['qty'];
                        }
                        $minToChangeStatus = Mage::getStoreConfig('cataloginventory/item_options/min_qty');
                        
                        $backorders = $stockItem->getBackorders();
                        $useConfigBackorders = $stockItem->getUseConfigBackorders();
                        if ($useConfigBackorders) {
                            $backorders = Mage::getStoreConfig('cataloginventory/item_options/backorders', Mage::app()->getStore()->getStoreId());
                        }
                        
                        if (($oldQtyProduct + $code) > $minToChangeStatus) {
                            $sqlUpdateProduct = 'UPDATE ' . $resource->getTableName("cataloginventory_stock_item") . ' SET qty = qty + ' . $code . ', is_in_stock = 1 WHERE (product_id = ' . $pId . ')';
                            $sqlUpdateProductStatus = 'UPDATE ' . $resource->getTableName("cataloginventory_stock_status") . ' SET qty = qty + ' . $code . ', stock_status = 1 WHERE (product_id = ' . $pId . ')';
                        } else {
                            if ($product->getTypeId() != 'configurable') {
                                if(!$backorders){
                                    $sqlUpdateProduct = 'UPDATE ' . $resource->getTableName("cataloginventory_stock_item") . ' SET qty = qty + ' . $code . ', is_in_stock = 0 WHERE (product_id = ' . $pId . ')';
                                }else{
                                    $sqlUpdateProduct = 'UPDATE ' . $resource->getTableName("cataloginventory_stock_item") . ' SET qty = qty + ' . $code . ' WHERE (product_id = ' . $pId . ')';
                                }
                            } else {
                                $sqlUpdateProduct = 'UPDATE ' . $resource->getTableName("cataloginventory_stock_item") . ' SET qty = qty + ' . $code . ' WHERE (product_id = ' . $pId . ')';
                            }
                            if(!$backorders){
                                $sqlUpdateProductStatus = 'UPDATE ' . $resource->getTableName("cataloginventory_stock_status") . ' SET qty = qty + ' . $code . ', stock_status = 0 WHERE (product_id = ' . $pId . ')';
                            }else{
                                $sqlUpdateProductStatus = 'UPDATE ' . $resource->getTableName("cataloginventory_stock_status") . ' SET qty = qty + ' . $code . ' WHERE (product_id = ' . $pId . ')';
                            }
                        }
                        $writeConnection->query($sqlUpdateProduct);
                        $writeConnection->query($sqlUpdateProductStatus);
                    }
                    
                    if (!empty($allChildrenIds)) {
                        foreach ($allChildrenIds as $children) {
                            $product = Mage::getModel('catalog/product')->load($children);
                            $childStockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($children);
                            if ($childStockItem->getManageStock()) {
                                $results = Mage::getResourceModel('inventorydropship/inventorydropship')->getQtyProductByProduct($children);
                                foreach ($results as $result) {
                                    $oldQtyProduct = $result['qty'];
                                }
                                $minToChangeStatus = Mage::getStoreConfig('cataloginventory/item_options/min_qty');
                                
                                $backorders = $childStockItem->getBackorders();
                                $useConfigBackorders = $childStockItem->getUseConfigBackorders();
                                if($useConfigBackorders){
                                    $backorders = Mage::getStoreConfig('cataloginventory/item_options/backorders',Mage::app()->getStore()->getStoreId());                        
                                }
                                
                                if (($oldQtyProduct + $code) > $minToChangeStatus || $backorders) {
                                    $sqlUpdateProduct = 'UPDATE ' . $resource->getTableName("cataloginventory_stock_item") . ' SET qty = qty + ' . $code . ', is_in_stock = 1 WHERE (product_id = ' . $children . ')';
                                    $sqlUpdateProductStatus = 'UPDATE ' . $resource->getTableName("cataloginventory_stock_status") . ' SET qty = qty + ' . $code . ', stock_status = 1 WHERE (product_id = ' . $children . ')';
                                } else {
                                    if ($product->getTypeId() != 'configurable') {
                                        $sqlUpdateProduct = 'UPDATE ' . $resource->getTableName("cataloginventory_stock_item") . ' SET qty = qty + ' . $code . ', is_in_stock = 0 WHERE (product_id = ' . $children . ')';
                                    } else {
                                        $sqlUpdateProduct = 'UPDATE ' . $resource->getTableName("cataloginventory_stock_item") . ' SET qty = qty + ' . $code . ' WHERE (product_id = ' . $children . ')';
                                    }
                                    $sqlUpdateProductStatus = 'UPDATE ' . $resource->getTableName("cataloginventory_stock_status") . ' SET qty = qty + ' . $code . ', stock_status = 0 WHERE (product_id = ' . $children . ')';
                                }
                                $writeConnection->query($sqlUpdateProduct);
                                $writeConnection->query($sqlUpdateProductStatus);
                            }
                        }
                    }
                }
            }
            if ($success) {
                $dropship = Mage::getModel('inventorydropship/inventorydropship')->load($dropshipId);
                $dropship->setStatus('3')->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('adminhtml')->__('Drop ship was successfully approved!')
                );
                Mage::helper('inventorydropship')->sendEmailApproveDropShipToSupplier($dropshipId);
                $this->_redirect('*/*/edit', array('id' => $dropshipId));
            } else {
                Mage::getSingleton('adminhtml/session')->addError(
                        Mage::helper('adminhtml')->__('Please enter Qty Approved greater than 0 to approve this dropship!!')
                );
                $this->_redirect('*/*/edit', array('id' => $dropshipId));
            }
        }
    }

    public function dropshipordersAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function dropshipordersgridAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function submitshipmentAction() {
        if ($data = $this->getRequest()->getPost()) {
            $dropshipId = $data['dropship_id'];

            $dropship = Mage::getModel('inventorydropship/inventorydropship')->load($dropshipId);
            $success = false;
            if ($dropship->getId()) {
                $resource = Mage::getSingleton('core/resource');
                $writeConnection = $resource->getConnection('core_write');
                $readConnection = $resource->getConnection('core_read');
                $itemIds = $data['item']['lastship'];
//                $itemApproves = $data['item']['lastship'];                
                $shipped = array();
                $savedQtys = array();
                $productIds = array();
                try {
                    foreach ($itemIds as $itemId => $key) {

                        if (!is_numeric($key) || $key < 0)
                            $key = 0;
                        $dropshipProduct = Mage::getModel('inventorydropship/inventorydropship_product')
                                ->getCollection()
                                ->addFieldToFilter('dropship_id', $dropshipId)
                                ->addFieldToFilter('item_id', $itemId)
                                ->setPageSize(1)
                                ->setCurPage(1)
                                ->getFirstItem();
                        if ($dropshipProduct->getId()) {
                            $success = true;
                            if ($key > $dropshipProduct->getQtyApprove() - $dropshipProduct->getQtyShipped())
                                $key = $dropshipProduct->getQtyApprove() - $dropshipProduct->getQtyShipped();
                            $shipped[$itemId] = $key;
                            if ($key > 0) {
                                $savedQtys[$dropshipProduct->getItemId()] = $key;
                                $productIds[$dropshipProduct->getItemId()] = $dropshipProduct->getProductId();
                            }
                            $dropshipProduct->setData('qty_shipped', $dropshipProduct->getQtyShipped() + $key)->save();
                        }
                    }
                } catch (Exception $e) {
                    $this->_getSession()->addError($exception->getMessage());
                    $this->_redirect('*/*/edit', array('id' => $dropshipId));
                    return;
                }
                if ($success) {
                    $partial = false;
                    $dropshipProducts = Mage::getModel('inventorydropship/inventorydropship_product')
                            ->getCollection()
                            ->addFieldToFilter('dropship_id', $dropshipId);
                    foreach ($dropshipProducts as $dropshipP) {
                        if ($dropshipP->getQtyShipped() != $dropshipP->getQtyApprove()) {
                            $partial = true;
                            break;
                        }
                    }
                    if ($partial)
                        $dropship->setStatus('4')->save();
                    else
                        $dropship->setStatus('6')->save();
                    try {
                        //create shipment when supplier approved
                        if ($savedQtys) {
                            Mage::getModel('admin/session')->setData('break_shipment_event_dropship', true);
                            Mage::getModel('core/session')->setData('break_shipment_event_dropship', true);

                            $order = Mage::getModel('sales/order')->load($dropship->getOrderId());
                            $transaction = Mage::getModel('core/resource_transaction')
                                    ->addObject($order);
                            $shipment = Mage::getModel('sales/service_order', $order)->prepareShipment($savedQtys);
                            $shipment->register();
                            $shipment->getOrder()->setIsInProcess(true);
                            $transactionSave = Mage::getModel('core/resource_transaction')
                                    ->addObject($shipment)
                                    ->addObject($shipment->getOrder())
                                    ->save();
                        }
                    } catch (Exception $e) {
                        Mage::log($e->getMessage(), null, 'inventory_dropship.log');
                    }

                    $this->_getSession()->addSuccess($this->__('Drop ship was successfully shipped!'));
                    $this->_redirect('*/*/edit', array('id' => $dropshipId));
                    return;
                }
            }
            $this->_getSession()->addError($this->__('Please enter Qty To Ship greater than 0 to ship this dropship!'));
            $this->_redirect('*/*/edit', array('id' => $dropshipId));
            return;
        }
    }

    public function editAction() {
        $this->loadLayout()
                ->_setActiveMenu($this->_menu_path)
                ->renderLayout();
    }

    /* Drop shipment in supplier tab */

    public function supplierdropshipAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('inventorydropship.supplier.view.dropship');
        $this->renderLayout();
    }

    public function supplierdropshipGridAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('inventorydropship.supplier.view.dropship');
        $this->renderLayout();
    }

    public function additemcommentAction() {
        if ($this->getRequest()->getPost()) {
            $result = array();
            $itemId = $this->getRequest()->getParam('item_id');
            $itemComment = $this->getRequest()->getParam('item_comment');

            $orderItem = Mage::getModel('sales/order_item')->load($itemId);
            $orderItem->setData('item_comment', $itemComment);

            try {
                $orderItem->save();
                $result['status'] = 1;
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
            } catch (Exception $e) {
                $result['status'] = 0;
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
            }
        } else {
            $result['status'] = 0;
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
    }

    /**
     * mass delete item(s) action
     */
    public function massDeleteAction() {
        $inventoryIds = $this->getRequest()->getParam('inventorydropship');
        if (!is_array($inventoryIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($inventoryIds as $inventoryId) {
                    $inventory = Mage::getModel('inventorydropship/inventorydropship')->load($inventoryId);
                    $inventory->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Total of %d record(s) were successfully deleted', count($inventoryIds))
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * mass change status for item(s) action
     */
    public function massStatusAction() {
        $inventoryIds = $this->getRequest()->getParam('inventorydropship');
        if (!is_array($inventoryIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($inventoryIds as $inventoryId) {
                    Mage::getSingleton('inventorydropship/inventorydropship')
                        ->load($inventoryId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($inventoryIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * export grid item to CSV type
     */
    public function exportCsvAction() {
        $fileName = 'dropshipments.csv';
        $content = $this->getLayout()
            ->createBlock('inventorydropship/adminhtml_inventorydropship_grid')
            ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export grid item to XML type
     */
    public function exportXmlAction() {
        $fileName = 'dropshipments.xml';
        $content = $this->getLayout()
            ->createBlock('inventorydropship/adminhtml_inventorydropship_grid')
            ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Upload attachment to drop shipment
     */
    public function uploadattachmentAction()
    {
        $fileToUpload = Mage::helper('inventoryplus')->getUploadFile();
        if ($this->getRequest()->getPost()) {
            $dropshipId = $this->getRequest()->getParam('dropship_id');
            $targetDir = Mage::getBaseDir("media") . DS . "inventorydropship" . DS;

            // Create folder /media/inventorydropship if it's not already existed
            if (!file_exists($targetDir)) {
                Mage::helper('inventoryplus')->mkDir(Mage::getBaseDir("media") . DS . "inventorydropship", 0777, true);
            }
            $targetFile = $targetDir . Mage::helper('inventoryplus')->baseName($fileToUpload["fileToUpload"]["name"]);
            $uploadOk = 1;
            $imageFileType = pathinfo($targetFile, PATHINFO_EXTENSION);
            // Check if image file is a actual image or fake image

            // Check if file already exists
            if (file_exists($targetFile)) {
                echo $this->__("Sorry, file already exists.");
                $uploadOk = 0;
            }
            // Check file size
            if ($fileToUpload["fileToUpload"]["size"] > 10000000) {
                echo $this->__("Sorry, your file is too large.");
                $uploadOk = 0;
            }
            // Allow certain file formats

            $dropship = Mage::getModel('inventorydropship/inventorydropship')->load($dropshipId);
            if (!$dropship) {
                echo $this->__("Sorry, drop shipment not found.");
                $uploadOk = 0;
            }

            // Check if $uploadOk is set to 0 by an error
            if ($uploadOk == 0) {
                echo $this->__("Sorry, your file was not uploaded.");
                // if everything is ok, try to upload file
            } else {
                // Save attachment path to dropship database record
                $oldPhysicalPath = $dropship->getData('attachment_physical_path');
                $newPhysicalPath = $oldPhysicalPath . $targetFile . ";";
                $dropship->setData('attachment_physical_path', $newPhysicalPath);

                $oldWebPath = $dropship->getData('attachment_web_path');
                $newWebPath = $oldWebPath . Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . "inventorydropship/" . Mage::helper('inventoryplus')->baseName($fileToUpload["fileToUpload"]["name"]) . ";";
                $dropship->setData('attachment_web_path', $newWebPath);
                try {
                    $dropship->save();
                    if (Mage::helper('inventoryplus')->moveUploadedFile($fileToUpload["fileToUpload"]["tmp_name"], $targetFile)) {
                        echo $this->__("The file " . Mage::helper('inventoryplus')->baseName($fileToUpload["fileToUpload"]["name"]) . " has been uploaded.");
                    } else {
                        $dropship->setData('attachment_physical_path', $oldPhysicalPath);
                        $dropship->setData('attachment_web_path', $oldWebPath);
                        $dropship->save();
                        echo $this->__("Sorry, there was an error uploading your file.");
                    }
                } catch (Exception $e) {
                    // If create file failed, delete attachment path to dropship database record
                    $dropship->setData('attachment_physical_path', $oldPhysicalPath);
                    $dropship->setData('attachment_web_path', $oldWebPath);
                    $dropship->save();
                }
            }
        } else {
            echo $this->__("Sorry, file not found.");
        }
    }

    public function deleteattachmentAction() {
        if ($this->getRequest()->getPost()) {
            $filePath = $this->getRequest()->getParam('file_path');
            $dropshipId = $this->getRequest()->getParam('dropship_id');

            // Get name of file to be deleted
            $fileName = substr($filePath, strrpos($filePath, DS) + 1);
            // Get full web path of file to be deleted
            $deleteWebPath = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . "inventorydropship/" . $fileName;

            $dropship = Mage::getModel('inventorydropship/inventorydropship')->load($dropshipId);
            $physicalPath = $dropship->getData('attachment_physical_path');
            $webPath = $dropship->getData('attachment_web_path');

            $newWebPath = str_replace($deleteWebPath . ';', '', $webPath);
            $newPhysicalPath = str_replace($filePath . ';', '', $physicalPath);

            try {
                $dropship->setData('attachment_web_path', $newWebPath);
                $dropship->setData('attachment_physical_path', $newPhysicalPath);
                $dropship->save();

                // Delete real file
                if (Mage::helper('inventoryplus')->isFile($filePath)) {
                    Mage::helper('inventoryplus')->unLink($filePath);
                }

                $attachmentBlockHtml = $this->getLayout()->createBlock('inventorydropship/adminhtml_inventorydropship_attachment')
                    ->setData('dropship', $dropship)
                    ->toHtml();
                $this->getResponse()->setBody($attachmentBlockHtml);
            } catch (Exception $e) {
                // If action failed, restore the data in database
                $dropship->setData('attachment_web_path', $webPath);
                $dropship->setData('attachment_physical_path', $physicalPath);
                $dropship->save();
            }
        }
    }
}
