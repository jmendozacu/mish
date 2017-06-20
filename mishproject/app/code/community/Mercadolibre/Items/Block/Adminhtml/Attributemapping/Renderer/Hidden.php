<?php

class Mercadolibre_Items_Block_Adminhtml_Attributemapping_Renderer_Hidden extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract


{
    public function render(Varien_Object $row)
    {
        $qtyDisplay ="";	
		$html = '';	
		$categoryId = '';
		switch($this->getColumn()->getId()){
   		   case "meli_value_id":
		   		
				if($this->getRequest()->getParam('store')){
					$store_id = $this->getRequest()->getParam('store');
				} else {
					$store_id = Mage::helper('items')-> getMlDefaultStoreId();
				}
		   		
				$default_value ='';
				$default_value = $row->getData('value');
		   		$attribute_id = '';
				$attribute_code = 'size';
				if($this->getRequest()->getParam('attribute_id')!=''){
					$attribute_id = $this->getRequest()->getParam('attribute_id');
					$attributes = Mage::getResourceModel('eav/entity_attribute_collection')
								->addFieldToFilter('attribute_id',$attribute_id);
					$attributesArr = $attributes->getData();
					$attribute_code = $attributesArr['0']['attribute_code'];
				} 
				//check for the condition for color and size 
				if(Mage::getStoreConfig("mlitems/globalattributesmapping/magesizeattr",Mage::app()->getStore($store_id))){
					$mageSizeAttrIds = Mage::getStoreConfig("mlitems/globalattributesmapping/magesizeattr",Mage::app()->getStore($store_id));
					$mageSizeAttrIds = explode(",", $mageSizeAttrIds);
					if(in_array($attribute_id, $mageSizeAttrIds )){
						$attribute_code = '';
						$attribute_code = 'size';
					}
				}
				if(Mage::getStoreConfig("mlitems/globalattributesmapping/magecolorattr",Mage::app()->getStore($store_id))){
					$mageColorAttrIds = Mage::getStoreConfig("mlitems/globalattributesmapping/magecolorattr",Mage::app()->getStore($store_id));
					$mageColorAttrIds = explode(",", $mageColorAttrIds);
					if(in_array($attribute_id, $mageColorAttrIds )){
						$attribute_code = '';
						$attribute_code = 'color';
					}
				}
				
				if($this->getRequest()->getParam('category_id')!=''){
					$category_id = $this->getRequest()->getParam('category_id');
				}
				
				
				$melicategoryattributes = Mage::getModel('items/melicategoryattributes')->getCollection()
										 ->addFieldToFilter('melicat.meli_category_id',$category_id)
										 ->addFieldToFilter('meli_attribute_type',$attribute_code);
				$melicategoryattributes  -> getSelect()-> joinleft(array('melicat'=>'mercadolibre_categories'), 'main_table.category_id = melicat.meli_category_id',array('melicat.category_id'));	
				
				$meliattributevaluemapping  = Mage::getModel('items/meliattributevaluemapping')->getCollection()
											->addFieldToFilter('melicat.meli_category_id',$category_id)
											->addFieldToFilter('mage_attribute',$attribute_code)
											->addFieldToFilter('mage_attribute_original',$attributesArr['0']['attribute_code'])
											->addFieldToFilter('mage_attribute_option_id',$row->getData('mage_attribute_option_id'));
				$meliattributevaluemapping  -> getSelect()-> joinleft(array('melicat'=>'mercadolibre_categories'), " main_table.category_id = melicat.meli_category_id and main_table.store_id = '".$store_id."'" ,array('melicat.category_id'));
				
				

				$selMeliValueIds = array();
				$attribute_mapping_idArr = array();

				foreach($meliattributevaluemapping->getData() as $selValAtr){
					if($selValAtr['meli_value_id']!=''){
						$selMeliValueIds[] = $selValAtr['meli_value_id'];
						$attribute_mapping_idArr[$selValAtr['meli_attribute_id']] = $selValAtr['attribute_mapping_id'];
					}
				}	

				if(count($melicategoryattributes->getData()) > 0){
					$selectIdIndex = 0;
					foreach($melicategoryattributes->getData() as $rowAtr){	
							$selectIdIndex++;
							$attribute_mapping_id='';
							if(isset($rowAtr['meli_attribute_id']) && count($attribute_mapping_idArr) > 0){
								$attribute_mapping_id = $attribute_mapping_idArr[$rowAtr['meli_attribute_id']];
							}
							$html .= '<input id="meli_attribute_id[]"  type="hidden" name="meli_attribute_id_'.$row->getData('option_id').'[]" value="'.$rowAtr['meli_attribute_id'].'">';
							$melicategoryattributevalues = Mage::getModel('items/melicategoryattributevalues')
														->getCollection()
														->addFieldToFilter('attribute_id',$rowAtr['meli_attribute_id'])
														->addFieldToFilter('meli_category_id',$this->getRequest()->getParam('category_id'));		

							$changeFunc = "";
							if($rowAtr['meli_attribute_name'] == "Color Primario"){
								$changeFunc = "onchange = 'onChangeMlColorAttribute(".$row->getData('option_id').")'";
							}
									
							$html .= '<div style="margin:5px 0;">
								<div style="width:100px; clear:right; float:left; ">'.$rowAtr['meli_attribute_name'].'</div>
								<div style="float:left;  margin-right:10px;">
									<input type="hidden" name="attribute_mapping_id_'.$rowAtr['meli_attribute_id'].'_'.$row->getData('option_id').'[]" value="'.$attribute_mapping_id.'">
									<select id ="meli_value_id_'.$selectIdIndex.'_'.$row->getData('option_id').'" name="meli_value_id_'.$rowAtr['meli_attribute_id'].'_'.$row->getData('option_id').'[]" style="width:80px;" '.$changeFunc.' >';
							
							//$html .='<option value="" >'.$rowAtr['meli_attribute_type'].'('.$rowAtr['meli_attribute_name'].')'.'</option>';
							$html .='<option value="" >'.$rowAtr['meli_attribute_name'].'</option>';
							foreach($melicategoryattributevalues->getData() as $rowValue){
								$sel = '';
								if(in_array($rowValue['meli_value_id'], $selMeliValueIds)) { $sel = 'selected'; }
								if($default_value == $rowValue['meli_value_name'] && $sel ==''){ $sel = 'selected'; }
								if($rowAtr['meli_attribute_type'] == 'color' && $rowValue['meli_value_name_extended'] != "" ){
									$html .='<option value="'.$rowValue['meli_value_id'].'" '.$sel.' style="background-color:'.$rowValue['meli_value_name_extended'].'">'.$rowValue['meli_value_name'].'</option>';
								}else{
									$html .='<option value="'.$rowValue['meli_value_id'].'" '.$sel.' >'.$rowValue['meli_value_name'].'</option>';
								}
							}
							$html .= '</select></div></div>';
							$categoryId = $rowAtr['category_id'];
					}
				} else {
					$html ='<div style="color:#DF280A;">There is no attribute value(s) found to map with magento attribute value. please select another MercadoLibre Category.</div>';
				}
					$html .= '<input id="category_id" style="width:100%; padding:5px 0;" type="hidden" name="category_id" value="'.$this->getRequest()->getParam('category_id').'" >';
					break;
		  case "mage_attribute_value":
		  		//get the store name
				if($this->getRequest()->getParam('store')){
					$store_id = $this->getRequest()->getParam('store');
				} else {
					$store_id = Mage::helper('items')-> getMlDefaultStoreId();
				}
				$currStoreName = Mage::getModel('core/store')->load($store_id)->getName();
				$write = Mage::getSingleton('core/resource')->getConnection('core_write');
				$attrValueByStore = $write->fetchCol("SELECT value from  eav_attribute_option_value WHERE store_id = '".$store_id."' and option_id = '".$row->getData('option_id')."' ");
		
				$html .= '<input type="hidden" name="mage_attribute[]" value="' . $row->getData('attribute_code') . '" >';
				$html .= '<input type="hidden" name="mage_attribute_value[]" value="' . $row->getData($this->getColumn()->getIndex()) . '" >'.$attrValueByStore[0];
				$html .= '<input type="hidden" name="mage_attribute_option_id[]" value="' . $row->getData('option_id') . '" >';
				$html .= '<input type ="hidden" value="'.$store_id.'" name="store_id">';
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