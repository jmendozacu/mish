<?php

class VES_VendorsPriceComparison2_Adminhtml_Vendors_AssignedproductsController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('admin/vendors/catalog/assigned_products');
    }
    
	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('vendors/vendors')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}
	
	public function productListGridAction(){
	    $this->getResponse()->setBody($this->getLayout()->createBlock('pricecomparison2/adminhtml_product_grid')->toHtml());
	}
	
	public function massStatusAction(){
	    $priceComparisonIds = $this->getRequest()->getParam('pricecomparison');
	    if($priceComparisonIds && sizeof($priceComparisonIds)){
	        $status = $this->getRequest()->getParam('status');
	        foreach($priceComparisonIds as $id){
    	        $priceComparison = Mage::getModel('pricecomparison2/pricecomparison')->load($id);
    	        $priceComparison->setStatus($status)->save();
	        }
	        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('pricecomparison2')->__('%s item(s) is updated.',sizeof($priceComparisonIds)));
	    }else{
	        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('pricecomparison2')->__('The item is not available.'));
	    }
	    $this->_redirect('*/*/');
	}
}