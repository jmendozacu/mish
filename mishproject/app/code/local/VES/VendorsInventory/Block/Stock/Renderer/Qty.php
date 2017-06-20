<?php

class VES_VendorsInventory_Block_Stock_Renderer_Qty extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    private $id;

    public function render(Varien_Object $row) {
        $this->id = $row['entity_id'];
        $html .= '<span id="product_' . $row['entity_id'] . '" onClick="qtybox('.$row['entity_id'].','.$row['qty'].');">' . (int) $row['qty'] . '</span>';
        return $html;
    }

}