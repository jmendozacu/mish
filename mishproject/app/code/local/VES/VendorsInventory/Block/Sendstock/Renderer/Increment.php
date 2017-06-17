<?php

class VES_VendorsInventory_Block_Sendstock_Renderer_Increment extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        $increment = Mage::helper('inventorywarehouse')->getIncrementId($row);
        return $increment;
    }

}
