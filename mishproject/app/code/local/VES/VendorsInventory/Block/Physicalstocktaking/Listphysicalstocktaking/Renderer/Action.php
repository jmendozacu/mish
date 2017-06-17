<?php

class VES_VendorsInventory_Block_Physicalstocktaking_Listphysicalstocktaking_Renderer_Action extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        $html = '';
        $permission = Mage::helper('vendorsinventory/adjuststock')->getPermission($row->getWarehouseId(), 'can_physical');
        if ($row->getPhysicalStatus() == 0 && $permission) {
            $html = '<a href="' . $this->getUrl('vendors/inventory_physicalstocktaking/edit', array('id' => $row->getId())) . '">' . Mage::helper('vendorsinventory')->__('Edit') . '</a>';
        } else {
            $html = '<a href="' . $this->getUrl('vendors/inventory_physicalstocktaking/edit', array('id' => $row->getId())) . '">' . Mage::helper('vendorsinventory')->__('View') . '</a>';
        }
        return $html;
    }

}
