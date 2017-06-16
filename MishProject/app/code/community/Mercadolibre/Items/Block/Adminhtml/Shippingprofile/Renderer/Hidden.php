<?php

class Mercadolibre_Items_Block_Adminhtml_Shippingprofile_Renderer_Hidden extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract


{
    public function render(Varien_Object $row)
    {
		
		$qtyDisplay ="";	
		$html = '';	
        switch($this->getColumn()->getId()){
            case "service_name_cost":

					$melishippingcustoms = Mage::getModel('items/melishippingcustom')-> getCollection()
																				 -> addFieldToFilter('shipping_id ',$row->getData('shipping_id'));
				if(count($melishippingcustoms->getData()) > 0){
					$html .= '<table width="100%" border="0" cellspacing="0" cellpadding="0">
							  <tr>
								<td><strong>Service Name</strong></td>
								<td><strong>Cost</strong></td>
							  </tr>';
					foreach($melishippingcustoms->getData() as $melishippingcustom){
					$html .= '<tr><td>'.$melishippingcustom['shipping_service_name'].'</td><td>'.$melishippingcustom['shipping_cost'].'</td></tr>';
					}
					$html .= "</table>";
				}
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
				$html = '<input type="hidden" name="'.$this->getColumn()->getId().'[]"';
				$html .= 'value="' . $row->getData($this->getColumn()->getIndex()) . '"';
				$html .= 'readonly="true" />'.$row->getData($this->getColumn()->getIndex());
                break;
        }
        return $html;
    }
}