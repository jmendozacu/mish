<?php

class Mercadolibre_Items_Block_Adminhtml_Itemlisting_Renderer_InputText extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract


{
    public function render(Varien_Object $row)
    {
		$html = '<input type="hidden" name="'.$this->getColumn()->getId().'[]"';
        $html .= 'value="' . $row->getData($this->getColumn()->getIndex()) . '"';
        $html .= 'readonly="true" />'.$row->getData($this->getColumn()->getIndex());
        return $html;
    }
}