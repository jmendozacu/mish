<?php

class VES_VendorsInventory_Block_Stock_Renderer_Date extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        $date = $row['updated_at'];
        return date("d-m-Y H:i", strtotime($date));        
    }

}