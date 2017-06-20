<?php

class Mercadolibre_Items_Block_Adminhtml_Anstemplate_Renderer_Hidden extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract


{
    public function render(Varien_Object $row)
    {
        $qtyDisplay ="";	
		$html = '';	
		$categoryId = '';
        switch($this->getColumn()->getId()){
   		   case "action":
				$html = '<div style="text-align:center;" ><a href="'.$this->getUrl('*/*/delete', array('id' => $row->getId())).'" onclick="return confirm(\'Are you sure? you want delete this template.\');">Delete</a></div>';
				break;
		   default:
				$html = '';
				break;
        }
        return $html;
    }
}