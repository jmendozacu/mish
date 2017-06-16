<?php
class Mish_OrderBlock_Model_Adminhtml_Observer{
	public function addOrderBlock(Varien_Event_Observer $observer){
		$block = $observer->getBlock();
		if(($block->getNameInLayout() == 'order_info') && ($child = $block->getChild('mish.order.info.block'))){
			$transport = $observer->getTransport();
			if($transport){
				$html = $transport->getHtml();
				$html .= $child->toHtml();
				$transport->setHtml($html);
			}
		}
		
	}
}