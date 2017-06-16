<?php

class Mercadolibre_Items_Block_Adminhtml_Itempublishing_Renderer_ProductImage extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
		//</associated product all images>
		$html = '<div id="image'.$row->getData('product_id').'" style="background: none repeat scroll 0 0 #FFFFFF !important;"><div onclick="loadProductImage(\'image'.$row->getData('product_id').'\',\''.$row->getData('product_id').'\')" class="place-holder" style=" border: 1px solid #AEAEAE; height: 100px; text-align: center; width: 100px;display: block;"><span>Click here to view image(s) </span></div></div>';
		return $html;
	}
}