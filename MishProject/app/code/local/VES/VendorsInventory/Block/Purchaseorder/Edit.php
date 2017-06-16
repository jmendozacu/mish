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
 * @package     Magestore_Inventory
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Inventory Supplier Edit Block
 * 
 * @category     Magestore
 * @package     Magestore_Inventory
 * @author      Magestore Developer
 */
class VES_VendorsInventory_Block_Purchaseorder_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct() {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'vendorsinventory';
        $this->_controller = 'purchaseorder';

        $this->_updateButton('save', 'label', Mage::helper('inventorypurchasing')->__('Save'));
        $this->removeButton('delete');         
        $this->removeButton('save');         
        
        $editable = 0;
        if ($this->getRequest()->getParam('id')) {
            $deliveries = Mage::getModel('inventorypurchasing/purchaseorder_delivery')
                    ->getCollection()
                    ->addFieldToFilter('purchase_order_id', $this->getRequest()->getParam('id'));
            if(count($deliveries) == 0){
                $editable = 1;
            }
        }
        if (isset($deliveries) && count($deliveries) > 0) {
            $this->_addButton('saveandcontinue', array(
                'label' => Mage::helper('adminhtml')->__('Save'),
                'onclick' => 'saveAndContinueEdit()',
                'class' => 'save',
                    ), -100);
        } else {
            $this->_addButton('saveandcontinue', array(
                'label' => Mage::helper('adminhtml')->__('Save'),
                'onclick' => 'saveAndContinueEditCheck()',
                'class' => 'save',
                    ), -100);
            $this->_updateButton('save', 'onclick', 'saveCheckD()');
        }
        if ($this->getRequest()->getParam('id')) {
            $deliveryModel = Mage::getModel('inventorypurchasing/purchaseorder_delivery')->getCollection()->addFieldToFilter('purchase_order_id', $this->getRequest()->getParam('id'));
            $purchaseOrder = $this->getPurchasOrder();
            $pStatus = $purchaseOrder->getStatus();
            $cancelDate = $purchaseOrder->getCanceledDate();
            $canCancel = 0;
            if (strtotime($cancelDate) >= strtotime(now())) {
                $canCancel = 1;
            }
            if (!$deliveryModel->setPageSize(1)->setCurPage(1)->getFirstItem()->getData() && ($canCancel == '1')
                    && ($pStatus == Magestore_Inventorypurchasing_Model_Purchaseorder::PENDING_STATUS
                            || $pStatus == Magestore_Inventorypurchasing_Model_Purchaseorder::WAITING_CONFIRM_STATUS)) {
                $this->_addButton('cancel_order_button', array(
                    'label' => Mage::helper('inventorypurchasing')->__('Cancel'),
                    'onclick' => 'if(confirm(\''. $this->__('Do you want to cancel this purchase order?') .'\')) setLocation(\'' . $this->getUrl('*/*/cancelorder', array('_current' => true)) . '\')',
                    'class' => 'delete',
                        ), 0);
            }
            $id = $this->getRequest()->getParam('id');

            if ($purchaseOrder->getStatus() == 7) {
                $this->removeButton('saveandcontinue');
                $this->removeButton('cancel_order_button');
                $this->removeButton('save');
            }

            if($purchaseOrder->getStatus() == Magestore_Inventorypurchasing_Model_Purchaseorder::PENDING_STATUS){
                if(Mage::getStoreConfig('inventoryplus/purchasing/require_confirmation_from_supplier')) {
                    $this->_addButton('request_confirm_button', array(
                        'label' => Mage::helper('inventorypurchasing')->__('Request Confirmation'),
                        'onclick' => 'if(confirm(\''. $this->__('Do you want to request confirm this purchase order?') .'\')) setLocation(\'' . $this->getUrl('*/*/requestconfirm', array('_current' => true)) . '\')',
                        'class' => 'save'
                    ), -100);
                }else{
                    $this->_addButton('confirm_button', array(
                        'label' => Mage::helper('inventorypurchasing')->__('Confirm Purchase Order'),
                        'onclick' => 'if(confirm(\''. $this->__('Please make sure that you have confirmed this purchase order with Supplier?') .'\')) setLocation(\'' . $this->getUrl('*/*/confirm', array('_current' => true)) . '\')',
                        'class' => 'save'
                    ), -100);
                }
            }

            if($purchaseOrder->getStatus() == Magestore_Inventorypurchasing_Model_Purchaseorder::WAITING_CONFIRM_STATUS){
                $this->_addButton('confirm_button', array(
                    'label' => Mage::helper('inventorypurchasing')->__('Confirm Purchase Order'),
                    'onclick' => 'if(confirm(\''. $this->__('Please make sure that you have confirmed this purchase order with Supplier?') .'\')) setLocation(\'' . $this->getUrl('*/*/confirm', array('_current' => true)) . '\')',
                    'class' => 'save'
                ), -100);
            }
            
            $isResendEmail = $purchaseOrder->getStatus();

            if ($isResendEmail == Magestore_Inventorypurchasing_Model_Purchaseorder::WAITING_CONFIRM_STATUS
                    || $isResendEmail == Magestore_Inventorypurchasing_Model_Purchaseorder::AWAITING_DELIVERY_STATUS
                     || $isResendEmail == Magestore_Inventorypurchasing_Model_Purchaseorder::COMPLETE_STATUS ) {
                $this->_addButton('resend_email', array(
                    'label' => Mage::helper('inventorypurchasing')->__('Resend email to supplier'),
                    'onclick' => 'if(confirm(\''. $this->__('Do you want to resend this purchase order to Supplier?') .'\')) setLocation(\'' . $this->getUrl('*/*/resendemailtosupplier', array('_current' => true)) . '\')',
                        ), -100);
            }    
            
            $this->_addButton('export_csv', array(
                'label' => Mage::helper('inventorypurchasing')->__('Print'),
                'onclick' => 'setLocation(\'' . $this->getUrl('*/*/exportcsvpurchaseorder', array('_current' => true)) . '\')',
                    ), -100);            
       
            $this->removeButton('reset');
        }

        if($this->getPurchasOrder()
            && $this->getPurchasOrder()->getId()
                && in_array($this->getPurchasOrder()->getStatus(), array(Magestore_Inventorypurchasing_Model_Purchaseorder::PENDING_STATUS,
                                                                            Magestore_Inventorypurchasing_Model_Purchaseorder::COMPLETE_STATUS))){
            $this->_addButton('move_to_trash', array(
                'label' => Mage::helper('inventorypurchasing')->__('Move to Trash'),
                'onclick' => 'if(confirm(\''. $this->__('Do you want to move this purchase order to trash?') .'\')) setLocation(\'' . $this->getUrl('*/*/movetotrash', array('id' => $this->getPurchasOrder()->getId(), '_current' => true)) . '\');',
                'class' => 'delete'
                    ), -1);            
        }
        if($this->getPurchasOrder()
            && $this->getPurchasOrder()->getId()
                && in_array($this->getPurchasOrder()->getStatus(), array(Magestore_Inventorypurchasing_Model_Purchaseorder::AWAITING_DELIVERY_STATUS,
                                                                            Magestore_Inventorypurchasing_Model_Purchaseorder::RECEIVING_STATUS,
                                                                                Magestore_Inventorypurchasing_Model_Purchaseorder::COMPLETE_STATUS))
                    && !$this->getPurchasOrder()->getPaidAll()){
            $this->_addButton('mark_as_paid', array(
                'label' => Mage::helper('inventorypurchasing')->__('Mark as paid'),
                'onclick' => 'if(confirm(\''. $this->__('Do you want to mark this purchase order as paid?') .'\')) setLocation(\'' . $this->getUrl('*/*/markaspaid', array('id' => $this->getPurchasOrder()->getId(), '_current' => true)) . '\');',
                'class' => 'save'
            ), -1);
        }
        if($this->getPurchasOrder()
            && $this->getPurchasOrder()->getId()
                && in_array($this->getPurchasOrder()->getStatus(), array(Magestore_Inventorypurchasing_Model_Purchaseorder::RECEIVING_STATUS))){
            $this->_addButton('complete_button', array(
                'label' => Mage::helper('inventorypurchasing')->__('Complete'),
                'onclick' => 'if(confirm(\''. $this->__('Do you want to complete this purchase order? (there are some products still not received)') .'\')) setLocation(\'' . $this->getUrl('*/*/completepurchaseorder', array('id' => $this->getPurchasOrder()->getId(), '_current' => true)) . '\');',
                'class' => 'save'
            ), -1);
        }


        $this->_formScripts[] = "
            Event.observe('ship_via','change',function(){
                if($('ship_via').value == 'new'){
                    $('ship_via_new').show();
                }else{
                    $('ship_via_new').hide();
                }
            });
            Event.observe('payment_term','change',function(){
                if($('payment_term').value == 'new'){
                    $('payment_term_new').show();
                }else{
                    $('payment_term_new').hide();
                }
            });
            
            function toggleEditor() {
                if (tinyMCE.getInstanceById('inventory_content') == null)
                    tinyMCE.execCommand('mceAddControl', false, 'inventory_content');
                else
                    tinyMCE.execCommand('mceRemoveControl', false, 'inventory_content');
            }
            function saveAndContinueEdit()
            {
                editForm.submit($('edit_form').action+'back/edit/');
            }
            
            function saveAndContinueEditCheck(){
                var editable = '".$editable."';
                var checkProduct = checkProductQty();
                if(((!checkProduct) || (checkProduct=='')) && editable == '0'){
                    alert('Please fill qty for product(s) and qty greater than 0 to purchase order!');
                    return false;
                }else{
                    var parameters = {products: checkProduct};
                    var check_product_url = '" . $this->getUrl('vendors/inventory_purchaseorders/checkproduct') . "';
                    var request = new Ajax.Request(check_product_url, {	
                        parameters: parameters,
                        onSuccess: function(transport) {
                            if(transport.status == 200)	{                                                                
                                var response = transport.responseText;  
                                if(response=='1'){
                                    editForm.submit($('edit_form').action+'back/edit/');
                                }else{
                                    alert('Please select product(s) and enter Qty greater than 0 to create purchase order!');
                                    return false;
                                }
                            }
                        },
                        onFailure: ''
                    });
                    return false;
                }
//                editForm.submit($('edit_form').action+'back/edit/');
            }
			
			function saveCheckD(){
                var editable = '".$editable."';
                var checkProduct = checkProductQty();
                if(((!checkProduct) || (checkProduct=='')) && editable == '0'){
                    alert('Please fill qty for product(s) and qty greater than 0 to purchase order!');
                    return false;
                }else{
                    var parameters = {products: checkProduct};
                    var check_product_url = '" . $this->getUrl('vendors/inventory_purchaseorders/checkproduct') . "';
                    var request = new Ajax.Request(check_product_url, {	
                        parameters: parameters,
                        onSuccess: function(transport) {
                            if(transport.status == 200)	{                                                                
                                var response = transport.responseText;  
                                if(response=='1'){
                                    editForm.submit($('edit_form').action);
                                }else{
                                    alert('Please select product(s) and enter Qty greater than 0 to create purchase order!');
                                    return false;
                                }
                            }
                        },
                        onFailure: ''
                    });
                    return false;
                }
//                editForm.submit($('edit_form').action+'back/edit/');
            }
            
            function saveCheck(){
                var checkProduct = checkProductQty();
                if((!checkProduct) || (checkProduct=='')){
                    alert('Please fill qty for product(s) and qty greater than 0 to purchase order!');
                    return false;
                }else{
                    var parameters = {products: checkProduct};
                    var check_product_url = '" . $this->getUrl('vendors/inventory_purchaseorders/checkproduct') . "';
                    var request = new Ajax.Request(check_product_url, {	
                        parameters: parameters,
                        onSuccess: function(transport) {
                            if(transport.status == 200)	{                                                                
                                var response = transport.responseText;  
                                if(response=='1'){
                                    editForm.submit($('edit_form').action);
                                }else{
                                    alert('Please select product(s) and enter Qty greater than 0 to create purchase order!');
                                    return false;
                                }
                            }
                        },
                        onFailure: ''
                    });
                    return false;
                }
            }
            
            function checkProductQty()
            {
                var purchaseorder_products = document.getElementsByName('purchaseorder_products');
                if(purchaseorder_products && purchaseorder_products != '' && purchaseorder_products[0]){                
                    return purchaseorder_products[0].value;
                }else{                    
                    return false;                    
                }
            }

            function fileSelected() {
                var file = document.getElementById('fileToUpload').files[0];
                if (file) {
                    var fileSize = 0;
                    if (file.size > 1024 * 1024)
                        fileSize = (Math.round(file.size * 100 / (1024 * 1024)) / 100).toString() + 'MB';
                    else
                        fileSize = (Math.round(file.size * 100 / 1024) / 100).toString() + 'KB';

                    document.getElementById('fileName').innerHTML = 'Name: ' + file.name;
                    document.getElementById('fileSize').innerHTML = 'Size: ' + fileSize;
                    document.getElementById('fileType').innerHTML = 'Type: ' + file.type;
                }
            }

             function uploadFile() {
                if(!$('fileToUpload') || !$('fileToUpload').value){
                    alert('Please choose CSV file to import!');return false;
                }
                if($('loading-mask')){
                    $('loading-mask').style.display = 'block';
                }
                var fd = new FormData();
                fd.append('fileToUpload', document.getElementById('fileToUpload').files[0]);
                fd.append('form_key', document.getElementById('form_key').value);
                var xhr = new XMLHttpRequest();
                xhr.upload.addEventListener('progress', uploadProgress, false);
                xhr.addEventListener('load', uploadComplete, false);
                xhr.addEventListener('error', uploadFailed, false);
                xhr.addEventListener('abort', uploadCanceled, false);
                xhr.open('POST', '" . $this->getUrl('vendors/inventory_purchaseorders/importproduct') . "');
                xhr.send(fd);
            }

            function uploadProgress(evt) {

            }

            function uploadComplete(evt) {
                $('purchaseorder_tabs_products_section').addClassName('notloaded');
                purchaseorder_tabsJsTabs.showTabContent($('purchaseorder_tabs_products_section'));
                //varienGlobalEvents.attachEventHandler('showTab',function(){ productGridJsObject.doFilter(); });
            }

            function uploadFailed(evt) {
                    alert('There was an error attempting to upload the file.');
            }

            function uploadCanceled(evt) {
                    alert('The upload has been canceled by the user or the browser dropped the connection.');
            }

            function createNewDeliveryOrder(){
                var url = '" . $this->getUrl('vendors/inventory_purchaseorders/newdelivery', array('purchaseorder_id' => $this->getRequest()->getParam('id'))) . "';
                window.location.href = url;
           }
           
           function showhistory(purchaseOrderHistoryId){
                var url = '" . $this->getUrl('vendors/inventory_purchaseorders/showhistory') . "';               
                var purchaseOrderHistoryId = purchaseOrderHistoryId;                
                var url = url+'purchaseOrderHistoryId/'+purchaseOrderHistoryId;
                TINY.box.show(url,1, 800, 400, 1);
            }   
        ";
    }

    /**
     * get text to show in header when edit an item
     *
     * @return string
     */
    public function getHeaderText() {
        if (Mage::registry('purchaseorder_data') && Mage::registry('purchaseorder_data')->getId()
        ) {
            $message = '';
            $sendEmail = Mage::registry('purchaseorder_data')->getSendMail();
            $paidAll = Mage::registry('purchaseorder_data')->getPaidAll();
            if($paidAll == 1) {
                $message .= '<a style="background: #008000;" href="" onclick="return false" title="' . Mage::helper('inventorypurchasing')->__('The payment has been made completely') . '">' . Mage::helper('inventorypurchasing')->__('PAID') . '</a>';
            }else {
                $paid = Mage::registry('purchaseorder_data')->getPaid();
                if ($paid > 0) {
                    $message .= '<a style="background: #0000FF;" href="" onclick="return false" title="' . Mage::helper('inventorypurchasing')->__('You have paid partially') . '">' . Mage::helper('inventorypurchasing')->__('PARTIALLY PAID') . '</a>';
                } else {
                    $message .= '<a style="background: #FF0000;" href="" onclick="return false" title="' . Mage::helper('inventorypurchasing')->__('The payment hasn\'t made yet') . '">' . Mage::helper('inventorypurchasing')->__('NOT PAID') . '</a>';
                }
            }
            if ($sendEmail) {
                $message .= '<a style="background: #008000;" href="" onclick="return false" title="' . Mage::helper('inventorypurchasing')->__('You have sent the email of this PO to Supplier') . '">' . Mage::helper('inventorypurchasing')->__('SENT') . '</a>';
            } else {
                $message .= '<a style="background: #FF0000;" href="" onclick="return false" title="' . Mage::helper('inventorypurchasing')->__('You haven\'t sent the email of this PO to Supplier') . '">' . Mage::helper('inventorypurchasing')->__('NOT SENT') . '</a>';
            }
            return Mage::helper('inventorypurchasing')->__("Edit Order No. '%s'", $this->htmlEscape(Mage::registry('purchaseorder_data')->getId())
            ).$message;
        }
        return Mage::helper('inventorypurchasing')->__('Add New Purchase Order');
    }
    
    public function getPurchasOrder() {
        return Mage::registry('purchaseorder_data');
    }

}
