<?php

class Mercadolibre_Items_Block_Adminhtml_Mastertemplates_Renderer_Hidden extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract


{
    public function render(Varien_Object $row)
    {
		$html = '';	
        switch($this->getColumn()->getId()){
            case "paymentData":
				$payTempIds = '';
				$paymentTemplates = array();
				$payTempIds = explode(',', $row->getData('payment_id'));
				if(count($payTempIds)>0){
					$paymentTemplates = Mage::getModel('items/melipaymentmethods')->getCollection()
									  -> addFieldToFilter('id',$payTempIds)
									  -> addFieldToSelect('payment_name');
					if(count($paymentTemplates)>0){
						foreach($paymentTemplates as $payTempName){
								$html .=  $payTempName['payment_name'].'<br/>';
						}
					}else{
						$html = 'No Template';
					}
				}else{
					$html = 'No Template';
				}
				break;
			default:
				$html = 'No Template';
				break;
        }
        return $html;
    }
}