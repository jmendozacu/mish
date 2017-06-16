<?php

class Mercadolibre_Items_Block_Adminhtml_Feedbacks_Renderer_Hidden extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract


{
    public function render(Varien_Object $row)
    {
       	$html = '';	
		switch($this->getColumn()->getId()){
   		   case "action":
				if($row->getData('reply') == ''){
		   			$html = '<div style="text-align:center;" ><a href="'.$this->getUrl('*/*/edit', array('id' => $row->getId())).'" title="feedback" >Post Feedback</a></div>';
				} else {
					$html = '<div style="text-align:center;" >Feedback Posted</div>';
				}
				break;
		  default:
                    $html = '';
                    break;
        }
        return $html;
    }
}