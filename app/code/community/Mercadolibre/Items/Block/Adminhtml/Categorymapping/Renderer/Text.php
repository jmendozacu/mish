<?php

class Mercadolibre_Items_Block_Adminhtml_Categorymapping_Renderer_Text extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	private $str = '';
    public function render(Varien_Object $row)
    {
		$this->str = $row->getData($this->getColumn()->getIndex());
		if(trim($this->getColumn()->getId()) == 'meli_category_name'){
			if(trim($row->getData($this->getColumn()->getIndex())) == 'No Mapping'){
				$this->str = '<div style="background:#FDFBB2; width:100%; padding-left:5px;" >'.$row->getData($this->getColumn()->getIndex()).'</div>';
			} else {
				$this->str = $row->getData($this->getColumn()->getIndex());
			}
		}
		return $this->str;
    }
}