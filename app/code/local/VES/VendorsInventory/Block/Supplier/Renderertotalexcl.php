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
 * Warehouse Edit Form Content Tab Block
 * 
 * @category    Magestore
 * @package     Magestore_Inventory
 * @author      Magestore Developer
 */
class VES_VendorsInventory_Block_Supplier_Renderertotalexcl extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $data = $row->getData();
        $total_amount =  $row->getData($this->getColumn()->getIndex());
        $purchaseOrderId = $data['purchase_order_id'];
        $pruchaseOrder = Mage::getModel('inventorypurchasing/purchaseorder')->load($purchaseOrderId);
        $totalexcl_curency = $total_amount  + $pruchaseOrder->getShippingCost();
        $currency = $row->getCurrency();
        if(!$currency)
            $currency = Mage::app()->getStore()->getBaseCurrencyCode();			
        return Mage::getModel('directory/currency')->load($currency)->formatTxt($totalexcl_curency);  
    }
}
