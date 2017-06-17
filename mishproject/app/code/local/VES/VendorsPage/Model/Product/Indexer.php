<?php
class VES_VendorsPage_Model_Product_Indexer extends Mage_Index_Model_Indexer_Abstract
{

	public function getName() {
		return Mage::helper('vendorspage')->__('Vendor Product Url Rewrite');
	}

	public function getDescription() {
		return Mage::helper('vendorspage')->__('Index Product URL rewrite');
	}

	protected function _registerEvent(Mage_Index_Model_Event $event) {
		// custom register event
		return $this;
	}

	protected function _processEvent(Mage_Index_Model_Event $event) {
		return $this;
	}

	public function reindexAll(){
		Mage::getModel('vendorspage/resource_product')->processUrlRewrite();
	}
}