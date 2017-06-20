<?php

class Mercadolibre_Items_Block_Adminhtml_Itemlisting_Renderer_Hidden extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract


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
			case "sent_to_publish":
				if($row->getData('sent_to_publish') == 'Published'){
					$html .= '<div  style="width:70px; float:left; margin-bottom:5px; background-color:#EFF5EA; color:#3D6611 ; border:1px solid #95A486;  padding:2px 0px 2px 5px ; margin-top:5px;">'. $row->getData('sent_to_publish').' </div>';
				} else if($row->getData('sent_to_publish') !='') {
					$html .= '<div  style="width:70px; float:left; margin-bottom:5px; background-color:#FAEBE7; color:#DF280A ; border:1px solid #F16048;  padding:2px 5px 2px 5px ; margin-top:5px;"> Ready To Publish </div>';
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