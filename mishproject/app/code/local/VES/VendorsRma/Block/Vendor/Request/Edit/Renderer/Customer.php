<?php
class VES_VendorsRma_Block_Vendor_Request_Edit_Renderer_Customer extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface {

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $html = '<tr>
					<td class="label" style="padding-top:10px!important;"><label>'.Mage::helper('vendorsrma')->__('Customer ') .'</label></td>
					<td class="value">';
        $html .=   $element->getValue();
        $html .= '</td></tr>';
        return $html;
    }

}


