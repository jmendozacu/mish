<?php

class Mercadolibre_Items_Block_Adminhtml_Categorymapping_Renderer_Input extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    
	public function render(Varien_Object $row)
    {
	
		$qtyDisplay ="";	
		$html = '';
		$str = $row->getData($this->getColumn()->getIndex());	
		switch($this->getColumn()->getId()){
			case "mapping_id":
				$root_id = '';
				if($this->getRequest()->getParam('root_id')){
					 $root_id = $this->getRequest()->getParam('root_id');
				}
				$html = '<input type="hidden" name="meli_root_id" value="'.$root_id.'" readonly="true" />';
				$html .= '<input type="hidden" name="mapping_id[]" value="' . $row->getData('mapping_id') . '" readonly="true" />';
				$path = '';
				$path = explode('/', $row->getData('path'));
				$categoryCollection = Mage::getResourceModel('catalog/category_collection')
									-> addAttributeToSelect('name')
									-> addAttributeToFilter('entity_id', array('in'=>$path))
									-> addIsActiveFilter();
				$arrCatPath = array();
				foreach($categoryCollection as $cat){
				  $arrCatPath[$cat->getId()] = $cat->getName();
				}
				$pathValArr = array();
				for($i=0; $i < count($path); $i++ ){
					if($arrCatPath[$path[$i]]!=''){
						$pathValArr[] = $arrCatPath[$path[$i]];
					}
				}
				if(count($pathValArr) > 0){
					$str = implode(' > ',$pathValArr);
				}
				$html .= '<input type="hidden" name="mage_category_id[]" value="' . $row->getData('mage_category_id') . '"';
				$html .= 'readonly="true" />'. $str;
				break;
				
			case "has_attributes":
				if($row->getData('has_attributes') == 'Yes'){
					$html = '<table width="100%" border="0" cellspacing="0" cellpadding="0">';
					$html .='<tr><td><strong>Variation</strong></td><td><strong>Required</strong></td></tr>';
					$melicategoryattributes = Mage::getModel('items/melicategoryattributes')->getCollection()->addFieldToFilter('category_id',$row->getData('meli_category_id'));
					
					foreach($melicategoryattributes->getData() as $rowAtr){	
						if($rowAtr['meli_attribute_name'] == 'Color Primario'){
							$meliAttrName = "Primario";
						}elseif($rowAtr['meli_attribute_name'] == 'Color Secundario'){
							$meliAttrName = "Secundario";
						}else{
							$meliAttrName = $rowAtr['meli_attribute_name'];
						}
						if($rowAtr['required'] == 1)
							$html .= '<tr><td>'.ucfirst($rowAtr['meli_attribute_type']).' ('.$meliAttrName.') </td><td>Yes</td></tr>';
						else		
							$html .= '<tr><td>'.ucfirst($rowAtr['meli_attribute_type']).' ('.$meliAttrName.') </td><td>No</td></tr>';					
					}
					$html .='</table>';
				}
				break;	
			case "meli_category_id":
					$category = '';	
					$category = Mage::getModel('catalog/category')
							  ->load($row->getData('mage_category_id'));
							  
					$root_id = '';
					if($this->getRequest()->getParam('root_id')){
						 $root_id = $this->getRequest()->getParam('root_id');
					}
					$cache = Mage::app()->getCache();
					$mlCategoriesToMapData = "ml_categories_to_map_cache_data_".$root_id;
					$MercadoCategoriesToMap = "ML_Categories_To_Map_".$root_id;
					if(!$cache->load($mlCategoriesToMapData) || $root_id == ""){
						/* Save Data in cache */
						$CommonModel = Mage::getModel('items/common');
						$optionsMeliCat = $CommonModel->getMLCategoriesToMap($categories = array($root_id)); // 
						$cache->save(serialize($optionsMeliCat), $mlCategoriesToMapData, array($MercadoCategoriesToMap), 60*60);
					} else {
						/* Get Data from cache */
						$optionsMeliCat = unserialize($cache->load($mlCategoriesToMapData));
					}
					if(count($optionsMeliCat) > 0 ){
						$html .='&nbsp;&nbsp;&nbsp;<select name="meli_category_id[]">';
						$selCount = 0;
						foreach($optionsMeliCat as $keyCat => $valCat){
							$selCat = '';
							$realText = str_replace('*&nbsp;&nbsp;&nbsp;','',$valCat);
							if(trim($row->getData('meli_category_id')) == trim($keyCat) ) { 
								$selCat = 'selected="selected"'; 
								$selCount++;
							} elseif(!$selCount && trim($category->getData('name')) == trim($realText)) {
								 $selCat = 'selected="selected"'; 
							}
							$html .='<option value="'.$keyCat.'" '.$selCat.' >'.$valCat.'</option>';
						}
					}
					$html .='</select>';
				break;	
			case "meliCatName": 
					$root_id = '';
					if($this->getRequest()->getParam('root_id')){
						 $root_id = $this->getRequest()->getParam('root_id');
					}
					if($root_id && ($row->getData('has_attributes') == 'Yes')){
						$html = '<a title="Click here to attribute mapping" href="'.$this->getUrl('items/adminhtml_attributemapping',array('root_id'=>$root_id,'category_id'=> $row->getData('meli_category_id'))).'">'.$row->getData('meli_category_path').'</a>';	
					} else {
						$html = $row->getData('meli_category_path');
					}
					if($this->getRequest()->getParam('store')){
						$store_id = $this->getRequest()->getParam('store');
					} else {
						$store_id = Mage::helper('items')-> getMlDefaultStoreId();
					}
					$html .='<input type = "hidden" value="'.$store_id.'" name="store_id">';
					break;
			default:
				$html = '<input type="hidden" name="'.$this->getColumn()->getId().'"';
				$html .= 'value="' . $row->getData($this->getColumn()->getIndex()) . '"';
				$html .= 'readonly="true" />'. $str;
				break;
        }
        return $html;
    }
		
}