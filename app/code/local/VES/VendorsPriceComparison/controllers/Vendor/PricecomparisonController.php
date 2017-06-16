<?php
class VES_VendorsPriceComparison_Vendor_PricecomparisonController extends VES_Vendors_Controller_Action
{
	/**
     * Load edit form
     */
	public function selectProductAction(){
		$parentProductId = $this->getRequest()->getParam('parent_product');
		Mage::registry('parent_product_id',$parentProductId);
		$this->loadLayout();
		$this->getLayout()->getBlock('content')->append($this->getLayout()->createBlock('pricecomparison/vendor_product_edit'));
		$this->getLayout()->getBlock('left')->append($this->getLayout()->createBlock('pricecomparison/vendor_product_edit_tabs'));
		$this->renderLayout();
	}
}