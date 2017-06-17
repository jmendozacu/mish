<?php

class Mercadolibre_Items_Block_Adminhtml_Itemlisting_Renderer_Text extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
		$str = $row->getData($this->getColumn()->getIndex());
		$html ="";		
        switch($this->getColumn()->getId()){
            case "descriptions":			
                   	$product_model = Mage::getModel('catalog/product')->load($row->getData('entity_id'));         
					$html = substr(strip_tags($product_model->getData('description')),0,120)."....";
                    break;
            case "meli_category_name":			
					$html = '<input type="hidden" name="'.$this->getColumn()->getId().'[]"';
					$html .= 'value="' . $row->getData($this->getColumn()->getIndex()) . '"';
					$html .= 'readonly="true" />'.$row->getData($this->getColumn()->getIndex());
                    break;
            default:
                    $html = '<input type="hidden" name="'.$this->getColumn()->getId().'[]"';
					$html .= 'value="' . $str . '"';
					$html .= 'readonly="true" />'.$str;
        }
        return $html;
		
		
		
		
		
    }
}