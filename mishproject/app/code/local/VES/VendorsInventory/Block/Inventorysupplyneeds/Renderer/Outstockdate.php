<?php

class VES_VendorsInventory_Block_Inventorysupplyneeds_Renderer_Outstockdate extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        $html = '';
        //require info process
        $product_id = $row->getProductId();
        $data = Mage::helper('inventorysupplyneeds')->processFilterData();
        $dateto = $data['date_to'];
        $datefrom = $data['date_from'];
        $warehouse = $data['warehouse'];
        $outstock_date = Mage::helper('vendorsinventory/supplyneeds')->getOutstockDate($product_id,$datefrom,$dateto,$warehouse,$row);
        $html .= '<span>'. $outstock_date .'</span>';

        return $html;
    }

}