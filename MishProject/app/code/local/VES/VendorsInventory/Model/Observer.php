<?php

class VES_VendorsInventory_Model_Observer {

    /**
     *
     * Hide the menu if the module is not enabled
     * @param Varien_Event_Observer $observer
     */
    public function ves_vendor_menu_check_acl(Varien_Event_Observer $observer) {
        $resource = $observer->getResource();
        $result = $observer->getResult();

        if ($resource == 'vendors/inventory' && !Mage::helper('vendorsinventory')->moduleEnable()) {
            $result->setIsAllowed(false);
        }
    }

    /**
     * Check if this feature is enabled for the current vendor (Advanced Group plugin is required)
     * @param Varien_Event_Observer $observer
     */
    public function ves_vendorsinventory_module_enable(Varien_Event_Observer $observer) {
        $modules = Mage::getConfig()->getNode('modules')->asArray();
        if (isset($modules['VES_VendorsGroup']) && isset($modules['VES_VendorsGroup']['active']) && $modules['VES_VendorsGroup']['active'] == 'true') {
            $result = $observer->getEvent()->getResult();
            if ($vendor = Mage::getSingleton('vendors/session')->getVendor()) {
                $groupId = $vendor->getGroupId();
                $vendorReportEnableConfig = Mage::helper('vendorsgroup')->getConfig('inventory/enabled', $groupId);
                $result->setData('module_enable', $vendorReportEnableConfig);
                return;
            }
        }
    }

    /**
     * set template for adjust stock add new
     *
     */
    public function adjust_stock_html($observer) {
        $block = $observer->getEvent()->getBlock();
        $block->setTemplate('ves_vendorsinventory/adjuststock/new.phtml');
    }

    /**
     * Add stock action buttons to warehouse page
     * 
     * @param Object $observer
     */
    public function inventoryplus_warehouse_stock_action_buttons($observer) {
        $warehouse = $observer->getEvent()->getData('warehouse');
        if (!Mage::helper('vendorsinventory/warehouse')->isAllowAction('send_request_stock', $warehouse))
            return;

        $actionsObject = $observer->getEvent()->getData('stock_actions_object');
        $stockActions = $actionsObject->getActions();

//        $stockActions['requeststock'] = array('params' => array(
//                'label' => Mage::helper('vendorsinventory')->__('Request Stock'),
//                'onclick' => 'location.href=\'' . Mage::getSingleton('adminhtml/url')->getUrl('vendors/inventory_requeststock/new', array('warehouse_id' => $warehouse->getId())) . '\'',
//                'class' => 'add',
//            ),
//            'position' => -92,
//        );

        $stockActions['physical_stocktaking'] = array('params' => array(
                'label' => Mage::helper('vendorsinventory')->__('Physical Stock-taking'),
                'onclick' => 'location.href=\'' . Mage::getSingleton('adminhtml/url')->getUrl('vendors/inventory_physicalstocktaking/new', array('warehouse_id' => $warehouse->getId())) . '\'',
                'class' => 'save',
            ),
            'position' => -90,
        );
        
//        $stockActions['purchase_stock'] = array('params' => array(
//                'label' => Mage::helper('vendorsinventory')->__('Purchase Stock'),
//                'onclick' => 'location.href=\'' . Mage::getSingleton('adminhtml/url')->getUrl('vendors/inventory_purchaseorders/new') . '\'',
//                'class' => 'add',
//            ),
//            'position' => -300
//        );
//        
//        $stockActions['sendstock'] = array('params' => array(
//                'label' => Mage::helper('vendorsinventory')->__('Send Stock'),
//                'onclick' => 'location.href=\'' . Mage::getSingleton('adminhtml/url')->getUrl('vendors/inventory_sendstock/new', array('warehouse_id' => $warehouse->getId())) . '\'',
//                'class' => 'save',
//            ),
//            'position' => -91,
//        );
        $actionsObject->setActions($stockActions);
    }

    /**
     * Add tabs to Warehouse page
     * 
     * @param object $observer
     */
    public function inventory_add_top_tab_warehouse($observer) {
        $warehouse = $observer->getEvent()->getWarehouse();
        if (!$warehouse->getId())
            return;
        $tab = $observer->getEvent()->getTab();
        $tab->addTab('dashboard_section', array(
            'label' => Mage::helper('vendorsinventory')->__('Dashboard'),
            'title' => Mage::helper('vendorsinventory')->__('Dashboard'),
            'content' => $tab->getLayout()
                    ->createBlock('vendorsinventory/warehouse_edit_tab_dashboard')
                    ->toHtml(),
        ));
    }

    //add more transaction warehouse tab
    public function addWarehouseTab($observer) {
        $warehouseId = $observer->getEvent()->getWarehouseId();
        if ($warehouseId) {
            $tab = $observer->getTab();
            $tab->addTab('transaction_section', array(
                'label' => Mage::helper('inventorywarehouse')->__('Stock Movements'),
                'title' => Mage::helper('inventorywarehouse')->__('Stock Movements'),
                'url' => $tab->getUrl('vendors/inventory_warehouse/transaction', array(
                    '_current' => true,
                    'id' => $warehouseId,
                )),
                'class' => 'ajax'
            ));
        }
        $this->addTabStore($observer);        
    }

    /**
     * Add store view to warehouse
     *
     * @param $observer
     */
    public function addTabStore($observer) {
        if (Mage::getStoreConfig('inventoryplus/general/select_warehouse') != 4) {
            return;
        }
        $tab = $observer->getEvent()->getTab();
        $tab->addTab('store_section', array(
            'label' => Mage::helper('inventorywarehouse')->__('Associated Stores'),
            'title' => Mage::helper('inventorywarehouse')->__('Associated Stores'),
            'url' => $tab->getUrl('vendors/inventory_warehouse/store', array(
                '_current' => true,
                'id' => $tab->getRequest()->getParam('id'),
                'store' => $tab->getRequest()->getParam('store')
            )),
            'class' => 'ajax',
        ));
    }

    /**
     * Add dashboard tab to supplier
     * 
     * @param Object $observer
     */
    public function addDashboardTabSupplier($observer) {
        $supplier = $observer->getEvent()->getData('supplier');
        if (!$supplier->getId())
            return;
        $tab = $observer->getEvent()->getTab();
        $tab->addTab('dashboard_section', array(
            'label' => Mage::helper('inventorypurchasing')->__('Dashboard'),
            'title' => Mage::helper('inventorypurchasing')->__('Dashboard'),
            'content' => $tab->getLayout()
                    ->createBlock('vendorsinventory/supplier_edit_tab_dashboard')
                    ->toHtml(),
        ));
    }
    
    public function addSupplierTabs($observer) {
        if (!Mage::getStoreConfig('inventoryplus/dropship/enable'))
            return;
        $tabs = $observer->getEvent()->getTabs();
        $tabs->addTab('dropship_section', array(
            'label' => Mage::helper('inventoryplus')->__('Drop Shipments'),
            'title' => Mage::helper('inventoryplus')->__('Drop Shipments'),
            'url' => Mage::getUrl('vendors/inventory_inventorydropship/supplierdropship', array(
                '_current' => true,
                'id' => Mage::app()->getRequest()->getParam('id'),
                'store' => Mage::app()->getRequest()->getParam('store')
            )),
            'class' => 'ajax',
        ));
    }
    
    /**
     * process supplier_form_after event
     *
     * @return Magestore_Inventorydropship_Model_Observer
     */
    public function supplier_form_after($observer) {
        $form = $observer->getEvent()->getForm();
        if (Mage::getStoreConfig('inventoryplus/dropship/enable')) {
            $fieldset = $form->addFieldset('supplierpass_form', array(
                'legend' => Mage::helper('inventoryplus')->__('Password Management')
            ));

            $fieldset->addField('new_password', 'text', array(
                'label' => Mage::helper('inventoryplus')->__('New Password'),
                'required' => false,
                'name' => 'new_password',
            ));
            
            $fieldset->addField('auto_general_password', 'checkbox', array(
                'label' => Mage::helper('inventoryplus')->__('Auto-generated password'),
                'required' => false,
                'name' => 'auto_general_password',
            ));

            $fieldset->addField('send_mail', 'checkbox', array(
                'label' => Mage::helper('inventoryplus')->__('Send new password to supplier'),
                'required' => false,
                'name' => 'send_mail',
            ));
        }
    }
    
    /**
     * Retrieve random password
     *
     * @param   int $length
     * @return  string
     */
    public function generatePassword($length = 8)
    {
        $chars = Mage_Core_Helper_Data::CHARS_PASSWORD_LOWERS
            . Mage_Core_Helper_Data::CHARS_PASSWORD_UPPERS
            . Mage_Core_Helper_Data::CHARS_PASSWORD_DIGITS
            . Mage_Core_Helper_Data::CHARS_PASSWORD_SPECIALS;
        return Mage::helper('core')->getRandomString($length, $chars);
    }

    public function saveSupplierPassword($observer) {
        if (!Mage::getStoreConfig('inventoryplus/dropship/enable')) {
            return;
        }
        $data = $observer->getEvent()->getDatas();
        $model = $observer->getEvent()->getModel();
        
        if(isset($data['auto_general_password'])){
            $data['new_password'] = $this->generatePassword();
        }
        
        if ($data['new_password']) {
            $newPassword = $data['new_password'];
            $newPasswordHash = md5($newPassword);
            $model->setPasswordHash($newPasswordHash);
        }
        if(array_key_exists('send_mail',$data))
            Mage::helper('inventorydropship')->sendPasswordResetConfirmationEmail($model,$newPassword); 
        
    }
    
}
