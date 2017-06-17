<?php

class Mercadolibre_Items_Block_Adminhtml_Itemlisting_Renderer_Input extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	 protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store',Mage::helper('items')-> getMlDefaultStoreId());
        return Mage::app()->getStore($storeId);
    }
	
    public function render(Varien_Object $row)
    {
        $qtyDisplay ="";	
		$attributeOptionText = '';	
        switch($this->getColumn()->getId()){
            case "meli_quantity":	
					/* Price */
                    if(count($row->getData('meli_price')) > 0 ){
                            $priceDisplay = $row->getData('meli_price');
                    }else{        	
                            $priceDisplay = $row->getData('price');
                    }		
                    $html = '<div  style="width:100px; float:left; margin-bottom:5px;">Price : <input id="meli_price_'.$row->getData('entity_id').'" style="width:50%; padding:5px 0;" class="required-entry validate-zero-or-greater input-text required-entry validation-failed" type="text" name="meli_price[]"';
                    $html .= 'value="'.number_format($priceDisplay, 2, '.', '').'"';
                    $html .= '" /></div>';
					
					/* Quantity */	
                    if(count($row->getData('meli_qty')) > 0 ){
                            $qtyDisplay = $row->getData('meli_qty');
                    }else{        	
                            $qtyDisplay = $row->getData('qty');
                    }		
                    $html .= '<div  style="width:100px; float:left;">&nbsp;&nbsp;&nbsp;&nbsp;Qty : <input id="'.$this->getColumn()->getId().'_'.$row->getData('entity_id').'" style="width:50%; padding:5px 0;" class="required-entry validate-zero-or-greater input-text required-entry validation-failed" maxlength="5" type="text" name="'.$this->getColumn()->getId().'[]"';
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
                    break; */
           case "title":			
                    if(trim($row->getData('meli_product_name')) !='' ){
                            $titleDisplay = $row->getData('meli_product_name');
                    }else{        	
                            $titleDisplay = $row->getData('name');
                    }		
                    $html = '<input type="hidden" name="mage_type_id_'.$row->getData('entity_id').'" value="'.$row->getData('type_id').'"><input id="'.$this->getColumn()->getId().'_'.$row->getData('entity_id').'" style="width:100%; padding:5px 0;" class="required-entry input-text required-entry validation-failed" type="text" name="'.$this->getColumn()->getId().'[]"';
                    $html .= 'value="' . $titleDisplay.'"';
                    $html .= '" />';
                    break;
		   case "variation":
					$html = '';
		   			if($row->getData('has_attributes')){					
						$html .= '<div id="variation_'.$row->getData('entity_id').'"><a href="javascript:void(0);" title="Click here to view variation " onclick = showItemVariation(\'variation_'.$row->getData('entity_id').'\',\''.$row->getData('entity_id').'\',\''.$row->getData('category_id').'\'); >View Variation</a></div>';
						$html .= '<div style="display:none" id="hide_variation_'.$row->getData('entity_id').'"><a href="javascript:void(0);" title="Click here to hide variation " onclick = hideItemVariation(\'variation_'.$row->getData('entity_id').'\',\''.$row->getData('entity_id').'\',\''.$row->getData('category_id').'\'); >Hide Variation</a></div>';
						$html .= '<div id="data_variation_'.$row->getData('entity_id').'"></div>';
					} else {
						$html = 'No Variation';
					}
					break;
		    case "check_box":
					$html = '';
		   			if($row->getData('has_attributes')){					
						$html = '<input type="checkbox" id="checkbox_'.$row->getData('entity_id').'" name="checkbox_'.$row->getData('entity_id').'" onclick = "showItemVariation(\'variation_'.$row->getData('entity_id').'\',\''.$row->getData('entity_id').'\',\''.$row->getData('category_id').'\'); loadProductImage(\'image'.$row->getData('entity_id').'\',\''.$row->getData('entity_id').'\');">';
					} else {
						$html = '<input type="checkbox" id="checkbox_'.$row->getData('entity_id').'" name="checkbox_'.$row->getData('entity_id').'"  onclick = "loadProductImage(\'image'.$row->getData('entity_id').'\',\''.$row->getData('entity_id').'\');">';
					}
					break;
			case "master_temp_id[]":
					$html = '';
					$shipping_modesArr = array();
					if(trim($row->getData('shipping_modes')) != ''){
			          $shipping_modesArr =  unserialize($row->getData('shipping_modes'));
					}
					/* Master template */
						$masterTempCollection = Mage::getModel('items/melimastertemplates')
						                      -> getCollection()
											  -> addFieldToFilter('main_table.store_id', $this->_getStore()->getId());
						$masterTempCollection -> getSelect()
											  -> joinleft(array('shippingtbl'=>'mercadolibre_shipping'), 'main_table.shipping_id = shippingtbl.shipping_id',array('shippingtbl.shipping_mode'));
						$melimasterTemplates = $masterTempCollection->getData();
						if(count($melimasterTemplates) > 0 ){
							$html .= '<select name="master_temp_id[]">';
							$html .= '<option value="">Please Select</option>';
							$totalTemplates = count($melimasterTemplates);
							foreach($melimasterTemplates as $rowtemp){
								if($rowtemp['master_temp_id'] == $row->getData('master_temp_id') || $totalTemplates == 1) { $selTemplate = 'selected="selected"'; }else{ $selTemplate =''; }
								if(is_array($shipping_modesArr) && in_array(trim($rowtemp['shipping_mode']), $shipping_modesArr))
								{
								    $html .='<option value="'.$rowtemp['master_temp_id'].'" '.$selTemplate.' >'.$rowtemp['master_temp_title'].'</option>';
								}
							}
							$html .='</select>';
						}
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