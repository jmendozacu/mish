<?php
    class VES_VendorsInventory_Block_Adjuststock_Listadjuststock_Renderer_Action
	extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row) 
    {
        $html = '';
        $permission = Mage::helper('vendorsinventory/adjuststock')->getPermission($row->getWarehouseId(),'can_adjust');
        if($row->getAdjustStatus() ==  0){
            $html = '<a href="'.$this->getUrl('vendors/inventory_adjuststock/edit',array('id'=>$row->getId())).'">'.Mage::helper('vendorsinventory')->__('Edit').'</a>';
        } else {
            $html = '<a href="'.$this->getUrl('vendors/inventory_adjuststock/edit',array('id'=>$row->getId())).'">'.Mage::helper('vendorsinventory')->__('View').'</a>';
        }
        return $html;
    }
}