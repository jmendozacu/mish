<?php

class Mercadolibre_Items_Block_Adminhtml_Itemdetailprofile_Renderer_Custom extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
   
	public function render(Varien_Object $row)
    {

	    $html = '';	
        switch($this->getColumn()->getId()){
			case "descriptions":			     
				$html .= $row->getData('description_header');
				$html .= $row->getData('description_body');
				$html .= $row->getData('description_footer');
			break;
			case "action":
				if($this->getRequest()->getParam('store')){
					$storeId = (int) $this->getRequest()->getParam('store');
				} else if(Mage::helper('items')-> getMlDefaultStoreId()){
					$storeId = Mage::helper('items')-> getMlDefaultStoreId();
				} else {
					$storeId = $this->getStoreId();
				}			     
				$html = '<div style="text-align:center;" ><a href="'.$this->getUrl('*/*/edit', array('id' => $row->getId(), 'store' => $storeId)).'" title="Edit" >Edit</a></div>';
			break;
			default:
				/* content */
				//if(!empty($row->getData($this->getColumn()->getIndex()))){
					//$html = $row->getData($this->getColumn()->getIndex());
				//}
                break;
        }
        return $html;
    }
}