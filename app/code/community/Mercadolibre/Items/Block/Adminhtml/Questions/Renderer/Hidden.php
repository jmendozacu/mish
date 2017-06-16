<?php

class Mercadolibre_Items_Block_Adminhtml_Questions_Renderer_Hidden extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract


{
    public function render(Varien_Object $row)
    {
        $qtyDisplay ="";	
		$html = '';	
		$categoryId = '';
        switch($this->getColumn()->getId()){
   		   case "action":
				if($row->getData('status') == 'UNANSWERED'){
					if($this->getRequest()->getParam('store')){
						$storeId = (int) $this->getRequest()->getParam('store');
					} else if(Mage::helper('items')-> getMlDefaultStoreId()){
						$storeId = Mage::helper('items')-> getMlDefaultStoreId();
					} else {
						$storeId = $this->getStoreId();
					}
		   			$html = '<div style="text-align:center;" ><a href="'.$this->getUrl('*/*/edit', array('id' => $row->getId(), 'store' => $storeId)).'" title="Answer Now" >Answer</a></div>';
				} else {
					$html = '<div style="text-align:center;" >&nbsp;</div>';
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