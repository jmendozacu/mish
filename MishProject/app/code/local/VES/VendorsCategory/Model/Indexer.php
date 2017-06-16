<?php
class VES_VendorsCategory_Model_Indexer extends Mage_Index_Model_Indexer_Abstract
{
	protected $_matchedEntities = array(
			'vendor_catalog' => array(
					Mage_Index_Model_Event::TYPE_SAVE
			)
	);

	public function getName() {
		return Mage::helper('vendorscategory')->__('Vendor Category Url Rewrite');
	}

	public function getDescription() {
		return Mage::helper('vendorscategory')->__('Index categories URL rewrite');
	}

	protected function _registerEvent(Mage_Index_Model_Event $event) {
		// custom register event
		return $this;
	}

	protected function _processEvent(Mage_Index_Model_Event $event) {
		
	}

	public function reindexAll(){
		Mage::getResourceModel('vendorscategory/category')->processUrlRewrite();
		/*$url_rewrite = Mage::getModel('core/url_rewrite')->getCollection()
		->addFieldToFilter('is_vendors_url',array('eq'=>'1'));
		
		foreach($url_rewrite as $_url) {
			$_request_path = $_url->getData('request_path');
			$_target_path = $_url->getData('target_path');
			
			$_request_path_arr = explode('/',$_request_path);
			$_target_path_arr = explode('/',$_target_path);
			
			$_request_path_arr[0] = Mage::getStoreConfig('vendors/vendor_page/url_key');
			$_target_path_arr[0] = Mage::getStoreConfig('vendors/vendor_page/url_key');
			
			$_request_path = implode('/', $_request_path_arr);
			$_target_path = implode('/',$_target_path_arr);
			
			$_url->setData('request_path',$_request_path)
			->setData('target_path',$_target_path);
			$_url->save();
		}
		*/
		//exit;
	}
}