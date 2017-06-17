<?php

class Mercadolibre_Items_Block_Adminhtml_Itemlisting_Renderer_ProductImage extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
		$ProductThumbImage='';
		$ModelProduct = Mage::getModel('catalog/product')->load($row->getData('entity_id'));
		$MediaGalleryArr = $ModelProduct->getMediaGallery('images');
		$html = '';
		$checkBoxName = '';
		$checkBoxName = "item_image[".$row->getData('entity_id')."][]";
		$meliImageArr = array();
		if(trim($row->getData('meli_images'))!=''){
			$meliImageArr =  unserialize(stripslashes($row->getData('meli_images')));
		}
		$html .= '<div style="width:160px;">';
		foreach($MediaGalleryArr as $row ){
				$html .= '<div style="width:70px; vertical-align:top; float:left; padding:5px;"><input type="checkbox" name="'.$checkBoxName.'" value="'. $row['value_id'] . '"';
				if(is_array($meliImageArr) && in_array($row['value_id'],$meliImageArr)){
					$html .= 'checked="checked"';
				}
				$html .=  ' ><img style="vertical-align:middle; padding-left:5px; width:50px;"';
				$html .= 'id="' . $this->getColumn()->getId() . '" ';
				$html .= 'src="'.Mage::getBaseUrl('media').'catalog/product' . $row['file'] . '"';
				$html .= 'class="grid-image ' . $this->getColumn()->getInlineCss() . '"/></div>';
		}
		$html .= '</div>';
		return $html;
    }
}