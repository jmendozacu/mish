<?php
class VES_Vendors_Block_Additinal extends Mage_Adminhtml_Block_Template
{
	public function getConfigurationUrl(){
		//echo "<pre>"; print_r($this->getRequest()->getParams());
		return Mage::getUrl('vendors/index/configuration',$this->getRequest()->getParams());
	}
}
