<?php

class Mercadolibre_Items_Block_Adminhtml_Itemlisting_Renderer_Input extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract


{
    public function render(Varien_Object $row)
    {
        $qtyDisplay ="";		
        switch($this->getColumn()->getId()){
            case "meli_quantity":	
					/* Price */
                    if(count($row->getData('meli_price')) > 0 ){
                            $priceDisplay = $row->getData('meli_price');
                    }else{        	
                            $priceDisplay = $row->getData('price');
                    }		
                    $html = '<div  style="width:100px; float:left; margin-bottom:5px;">Price : <input id="meli_price_'.$row->getData('entity_id').'" style="width:60%; padding:5px 0;" class="required-entry validate-zero-or-greater input-text required-entry validation-failed" type="text" name="meli_price[]"';
                    $html .= 'value="'.number_format($priceDisplay, 2, '.', '').'"';
                    $html .= '" /></div>';
					
					/* Quantity */	
                    if(count($row->getData('meli_qty')) > 0 ){
                            $qtyDisplay = $row->getData('meli_qty');
                    }else{        	
                            $qtyDisplay = $row->getData('qty');
                    }		
                    $html .= '<div  style="width:100px; float:left;">&nbsp;&nbsp;&nbsp;&nbsp;Qty : <input id="'.$this->getColumn()->getId().'_'.$row->getData('entity_id').'" style="width:60%; padding:5px 0;" class="required-entry validate-zero-or-greater validate-number" maxlength="5" type="text" name="'.$this->getColumn()->getId().'[]"';
                    $html .= 'value="' . ceil($qtyDisplay).'"';
                    $html .= '" /></div>';
					
                    break;
            case "meli_price":			
                   /* $Itemmodel  = Mage::getModel('items/mercadolibreitem')->getCollection()->addFieldToFilter('product_id',$row->getData('entity_id'));;
                    $Itemmodel  ->addFieldToSelect('price');
                    $PriceArr = $Itemmodel->getData();
                    if(count($PriceArr) > 0 ){
                            $priceDisplay = $PriceArr['0']['price'];
                    }else{        	
                            $priceDisplay = $row->getData($this->getColumn()->getIndex());
                    }		
                    $html = '<div  style="width:100px; float:left;">Price : <input id="'.$this->getColumn()->getId().'_'.$row->getData('entity_id').'" style="width:60%; padding:5px 0;" class="validate-number" type="text" name="'.$this->getColumn()->getId().'[]"';
                    $html .= 'value="' . $priceDisplay.'"';
                    $html .= '" /></div>';
                    break;*/
           case "title":			
                    if(trim($row->getData('meli_product_name')) !='' ){
                            $titleDisplay = $row->getData('meli_product_name');
                    }else{        	
                            $titleDisplay = $row->getData('name');
                    }		
                    $html = '<input id="'.$this->getColumn()->getId().'_'.$row->getData('entity_id').'" style="width:100%; padding:5px 0;" class="required-entry input-text required-entry validation-failed" type="text" name="'.$this->getColumn()->getId().'[]"';
                    $html .= 'value="' . $titleDisplay.'"';
                    $html .= '" />';
                    break;

            default:
                    $html = '<input id="'.$this->getColumn()->getId().'_'.$row->getData('entity_id').'" style="width:100%; padding:5px 0;"  type="text" name="'.$this->getColumn()->getId().'[]"';
                    $html .= 'value="' . $row->getData($this->getColumn()->getIndex()) . '"';
                    $html .= '" />';
                    break;
        }
        return $html;
    }
}