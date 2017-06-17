<?php
class VES_VendorsRma_Block_Vendor_Request_Edit_Renderer_Linkorder extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface {

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $order = Mage::getModel("sales/order")->loadByIncrementId($element->getValue());
        $html = '<tr>
					<td class="label" style="padding-top:10px!important;"><label>'.Mage::helper('vendorsrma')->__('Order Id ') .'</label></td>
					<td class="value">
	                    <a target="_black" href = "'.$this->getOrderLink($order->getId()).'" >#'.$element->getValue().'</a>
	                    <input type="hidden" name="order_id" id="order_id" value="'.$order->getId().'">
					</td>
				</tr>
				';
        return $html;
    }


    public function getOrderLink($order_id){
        return $this->getUrl('vendors/sales_order/view',array("order_id"=>$order_id));
    }
}


