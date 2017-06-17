<?php
class VES_VendorsSelectAndSell_Block_Product extends Mage_Core_Block_Template{
	/**
	 * 
	 * Get Vendor Session
	 * @return VES_Vendors_Model_Session
	 */
	protected function _getSession(){
		return Mage::getSingleton('vendors/session');
	}
	
	/**
	 * Get vendor from session
	 * @return VES_Vendors_Model_Vendor
	 */
	public function getVendor(){
		return $this->_getSession()->getVendor();
	}
	
	/**
	 * Get Vendor Id from session
	 * @return int
	 */
	public function getVendorId(){
		return $this->_getSession()->getVendorId();
	}
	
	/**
	 * Is vendor logged in
	 * @return boolean
	 */
	public function isVendorLoggedIn(){
		return $this->_getSession()->isLoggedIn();
	}
	
	/**
	 * Get current product
	 * @return Mage_Catalog_Model_Product
	 */
	public function getProduct(){
		return Mage::registry('product');
	}
	
	/**
	 * Get Select and sell URL
	 */
	public function getSelectAndSellUrl(){
		return $this->getUrl('vendors/selectandsell/index',array('product_id'=>$this->getProduct()->getId(),'__secure'=>false));
	}
	
	public function canSelectAndSell(){
		return $this->getProduct()->getVendorId() && $this->isVendorLoggedIn() && ($this->getProduct()->getVendorId() != $this->getVendor()->getId()) && !$this->isAlreadySellTheProduct();
	}

	public function isAlreadySellTheProduct(){
        if(Mage::helper('catalog/product_flat')->isEnabled()) {
            $emulationModel = Mage::getModel('core/app_emulation');
            $init = $emulationModel->startEnvironmentEmulation(0, Mage_Core_Model_App_Area::AREA_ADMINHTML);
        }

		$products = Mage::getModel('catalog/product')->getCollection()
					->addAttributeToSelect('vendor_related_product')
					->addAttributeToFilter('vendor_related_product',$this->getProduct()->getId());

        if(Mage::helper('catalog/product_flat')->isEnabled()) {
            $emulationModel->stopEnvironmentEmulation($init);
        }
		return $products->count();
	}
	public function getTransationJSON(){
		return json_encode(array(
			'LOADING'					=> $this->__('Loading ...'),
			'SELL_A_PRODUCT_LIKE_THIS'	=> $this->__('Sell a Product like this'),	
		));
	}
}
