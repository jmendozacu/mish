<?php

class VES_VendorsInventory_Block_Warehouse_Edit_Tab_Renderer_Fieldchanged extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        $warehouseHistoryId = $row->getWarehouseHistoryId();
        $fields = Mage::getResourceModel('inventoryplus/warehouse_historycontent')->getFields($warehouseHistoryId);
        $content = implode($fields, '<br/>');
        return $content;
    }

}
