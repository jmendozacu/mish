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
 * Warehouse Edit Block
 * 
 * @category     Magestore
 * @package     Magestore_Inventory
 * @author      Magestore Developer
 */
class Magestore_Inventoryplus_Block_Adminhtml_Stock_Renderer_Productlocation extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        $product_id = $row->getId();
        $content = '';
        $totalWarehouse = 0;
        $warehouse_products = Mage::getModel('inventoryplus/warehouse_product')
                ->getCollection()
                ->addFieldToFilter('product_id', $product_id);
        $check = 0;
        foreach ($warehouse_products as $warehouse_product) {
            $totalWarehouse++;
            $warehouse_id = $warehouse_product->getWarehouseId();
            $productLocation = $warehouse_product->getProductLocation();
            $url = Mage::helper('adminhtml')->getUrl('adminhtml/inp_warehouse/edit', array('id' => $warehouse_id));
            $warehouse = Mage::getModel('inventoryplus/warehouse')
                    ->getCollection()
                    ->addFieldToFilter('warehouse_id', $warehouse_id)
                    ->setPageSize(1)->setCurPage(1)
                    ->getFirstItem();
            $name = $warehouse->getWarehouseName();
            if (!$productLocation) {
                $productLocation = $this->__('N/A Location');
            }

            if (in_array(Mage::app()->getRequest()->getActionName(), array('exportCsv', 'exportXml'))) {
                if ($check)
                    $content.=', ' . $name . '(' . $productLocation . ')';
                else
                    $content.=$name . '(' . $productLocation . ')';
            } else
                $content .= "<a href=" . $url . ">$name</a>" . "<br/>" . '(' . $productLocation . ')' . "<br/>";
            $check++;
        }
        if ($totalWarehouse > 5) {
            $contentScroll = '<div style="overflow-y:scroll; height: 110px;">' . $content . '</div>';
            return $contentScroll;
        }
        return $content;
    }

}
