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
 * @package     Magestore_Inventorywarehouse
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Inventorywarehouse Adminhtml Block
 * 
 * @category    Magestore
 * @package     Magestore_Inventorywarehouse
 * @author      Magestore Developer
 */
class VES_VendorsInventory_Block_Requeststock_Renderer_Action extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        $requeststockId = $row->getId();
        if (Mage::helper('vendorsinventory/requeststock')->checkCancelRequeststock($requeststockId))
            $html = '<a href="' . $this->getUrl('vendors/inventory_requeststock/edit', array('id' => $requeststockId)) . '">' . Mage::helper('inventorywarehouse')->__('Edit') . '</a>';
        else
            $html = '<a href="' . $this->getUrl('vendors/inventory_requeststock/edit', array('id' => $requeststockId)) . '">' . Mage::helper('inventorywarehouse')->__('View') . '</a>';
        return $html;
    }

}
