<?php
class VES_VendorsVacation_Block_Adminhtml_Widget_Grid_Column_Renderer_Vendor extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        return '<b>'.$row->getData($this->getColumn()->getIndex()).'</b>';
    }
}
?>