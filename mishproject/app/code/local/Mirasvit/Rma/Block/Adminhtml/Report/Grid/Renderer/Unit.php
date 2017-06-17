<?php
class Mirasvit_Rma_Block_Adminhtml_Report_Grid_Renderer_Unit
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Number
{
    public function render(Varien_Object $row)
    {
    	$result = parent::render($row);
        if ($result == '') {
        	return '-';
    	} else {
    		return $this->__($this->getColumn()->getUnit(), $result);
    	}
    }

    /************************/

}