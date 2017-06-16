<?php

class Mercadolibre_Items_Block_Adminhtml_Itemtemplates_Renderer_Hidden extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract


{
    public function render(Varien_Object $row)
    {
		
		$qtyDisplay ="";	
		$html = '';	
        switch($this->getColumn()->getId()){
            case "price":
				/* Price */
				$html = '<div  style="width:100px; float:left; margin-bottom:5px;">Price : <input type="hidden" name="'.$this->getColumn()->getId().'[]"';
				$html .= 'value="'.number_format($row->getData($this->getColumn()->getIndex()), 2, '.', '').'"';
				$html .= 'readonly="true" />'.number_format($row->getData($this->getColumn()->getIndex()), 2, '.', '').'</div>';
				
				/* Quantiry */
				$html .= '<div  style="width:100px; float:left;  padding-left:10px;">Qty : <input type="hidden" name="available_quantity[]"';
				$html .= 'value="' . ceil($row->getData('qty')) . '"';
				$html .= 'readonly="true" />'.ceil($row->getData('qty')).'</div>';;
                break;
			case "buying_mode_id":
				$CommonModel = Mage::getModel('items/common');
				$optionsBuyingType = $CommonModel->getBuyingType();
				if($row->getData($this->getColumn()->getIndex())!=''){ 
					$html = $optionsBuyingType[$row->getData($this->getColumn()->getIndex())];
				}
				break;
			case "listing_type_id":
				$CommonModel = Mage::getModel('items/common');
				$optionsListingType = $CommonModel->getListingType(); 
				if($row->getData($this->getColumn()->getIndex())!=''){ 	
					$html = $optionsListingType[$row->getData($this->getColumn()->getIndex())];
				}
				break;
			case "condition_id":
				$CommonModel = Mage::getModel('items/common');
				$optionsCondition = $CommonModel->getCondition(); 
				if($row->getData($this->getColumn()->getIndex())!=''){ 	
					$html = $optionsCondition[$row->getData($this->getColumn()->getIndex())];
				}
				break;
				
			default:
				$html = '<input type="hidden" name="'.$this->getColumn()->getId().'[]"';
				$html .= 'value="' . $row->getData($this->getColumn()->getIndex()) . '"';
				$html .= 'readonly="true" />'.$row->getData($this->getColumn()->getIndex());
                break;
        }
        return $html;
    }
}