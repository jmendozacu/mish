<?php
class VES_VendorsRma_Block_Vendor_Request_Edit_Renderer_Order extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface {

    public function render(Varien_Data_Form_Element_Abstract $element) {
        $html = '<tr>
					<td class="label" style="padding-top:10px!important;"><label for="order_incremental_id">'.Mage::helper('vendorsrma')->__('Assign to order # ') .'</label></td>
					<td class="value">
							<input style= "margin-bottom:10px" type="text" name="ticket[order_incremental_id]" class="input-text required-entry validate-number" style="width:200px;position:relative;vertical-align: top;padding:7px;" id="order_incremental_id" autocomplete="off" value="'.$this->getOrderData('increment_id').'"/>
							<input type="hidden" name="order_id" id="order_id" value="'.$this->getOrderData('entity_id').'">
							<div id="result_order" class="result_order" style="display:none"></div>
							<button onclick="requestOption.viewOrder()" type="button" class="scalable '.$this->viewOrder().'" id="order_button_view">'.Mage::helper('vendorsrma')->__('View Order') .'</button>
					</td>
				</tr>
				';
        return $html;
    }

    public function getOrderData($value){
        if(Mage::registry('current_request')){
            $order=Mage::registry('current_request')->getData();
            if($order[$value] && $order[$value] != null) return $order[$value];
            return '';
        }
        return '';
    }

    public function viewOrder(){
        if($this->getOrderData('entity_id')) return '';
        return 'disabled';
    }
}
