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
					$html = substr(strip_tags($product_model->getData('description')),0,120).".... <a href='".$this->getUrl('*/*/edit', array('id' => $row->getItemId()))."'> Edit Content</a>";
                    break;
            case "meli_category_name":			
					$html = '<input type="hidden" name="'.$this->getColumn()->getId().'[]"';
					$html .= 'value="' . $row->getData($this->getColumn()->getIndex()) . '"';
					$html .= 'readonly="true" />'.$row->getData($this->getColumn()->getIndex());
					
					$html .= '<input type="hidden" name="mage_category_id[]"';
					$html .= 'value="' . $row->getData('category_id') . '"';
					$html .= 'readonly="true" />';
					
                    break;
			case "meli_category_id":	
				   $cache = Mage::app()->getCache();
				   if(!$cache->load('meliCatCollection_data')){
						 $meliCatCollection = Mage::getModel('items/melicategories')
										   -> getCollection()
										   -> addFieldToSelect('meli_category_id')
										   -> addFieldToSelect('meli_category_name');
										   //-> addFieldToSelect('meli_category_path');
						 $meliCatCollection-> getSelect()
									       -> joinleft(array('melicatfilter'=>'mercadolibre_categories_filter'), "main_table.meli_category_id = melicatfilter.meli_category_id AND melicatfilter.store_id = '".Mage::helper('items')-> _getStore()->getId()."' ",array('melicatfilter.meli_category_path'));
						$meliCatCollectionArr = $meliCatCollection -> getData();
						 $cache->save(serialize($meliCatCollectionArr), "meliCatCollection_data", array("meli_Cat_Collection_data"), 60*60);
				   } else {
						$meliCatCollectionArr = unserialize($cache->load('meliCatCollection_data'));
				   } 
					$mlCatArr = array();
					$mlCatPathArr = array();
					foreach($meliCatCollectionArr as $rowMLCat){
						$mlCatArr[$rowMLCat['meli_category_id']] = $rowMLCat['meli_category_name'];
						$mlCatPathArr[$rowMLCat['meli_category_id']] = $rowMLCat['meli_category_path'];
						
					} 
		
					$html = '<input type="hidden" name="meli_category_name[]"';
					$html .= 'value="' . $mlCatArr[$row->getData($this->getColumn()->getIndex())]. '"';
					$html .= 'readonly="true" /><a href="javascript:void(0);" title="'.$mlCatPathArr[$row->getData($this->getColumn()->getIndex())].'" >'.$mlCatArr[$row->getData($this->getColumn()->getIndex())].'</a>';
					
					$html .= '<input type="hidden" name="mage_category_id[]"';
					$html .= 'value="' . $row->getData('category_id') . '"';
					$html .= 'readonly="true" />';
					
                    break;
            default:
                    $html = '<input type="hidden" name="'.$this->getColumn()->getId().'[]"';
					$html .= 'value="' . $str . '"';
					$html .= 'readonly="true" />'.$str;
        }
        return $html;
		
		
		
		
		
    }
}