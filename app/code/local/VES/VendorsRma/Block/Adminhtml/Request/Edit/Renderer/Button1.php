<?php
class VES_VendorsRma_Block_Adminhtml_Request_Edit_Renderer_Button1 extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface {
	public function render(Varien_Data_Form_Element_Abstract $element) {
		//You can write html for your button here
		$html = '<tr>
					<td class="lable"></td>
					<td class="value">
							<div>
							<button class="disabled" disabled="disabled" id="ves-preview-vendor-template" onclick="previewTemplateVendor()" type="button">Preview Template</button>
							<strong style="color:green" id="test_connect"></strong>
							</div>
					</td>
				</tr>';
		return $html;
	}
}