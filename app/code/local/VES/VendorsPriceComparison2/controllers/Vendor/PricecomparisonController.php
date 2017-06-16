<?php
class VES_VendorsPriceComparison2_Vendor_PricecomparisonController extends VES_Vendors_Controller_Action
{
    public function indexAction(){
        $this->loadLayout();
        $this->_setActiveMenu('catalog')->_title(Mage::helper('pricecomparison2')->__('Assigned Products'))->_title(Mage::helper('pricecomparison2')->__('Select and Sell'));
        $this->_addBreadcrumb(Mage::helper('pricecomparison2')->__('Catalog'), Mage::helper('pricecomparison2')->__('Catalog'));
        $this->_addBreadcrumb(Mage::helper('pricecomparison2')->__('Assigned Products'), Mage::helper('pricecomparison2')->__('Assigned Products'));
        $this->renderLayout();
    }
    
    public function productListGridAction(){
        $this->getResponse()->setBody($this->getLayout()->createBlock('pricecomparison2/vendor_product_grid')->toHtml());
    }
	/**
     * Load edit form
     */
	public function selectandsellAction(){
		$this->loadLayout();
		$filter = Mage::getSingleton('adminhtml/session')->getData('selectAndSellGridproduct_filter');
		if(!$filter){
		    /*Make sure to show no products if vendor has not used search function.*/
		    $filter = base64_encode('name='.VES_VendorsPriceComparison2_Helper_Data::SPECIAL_CHAR);
		    $this->getRequest()->setParam('product_filter', $filter);
		}
		$this->_setActiveMenu('catalog')->_title(Mage::helper('pricecomparison2')->__('Assigned Products'))->_title(Mage::helper('pricecomparison2')->__('Select and Sell'));
		$this->_addBreadcrumb(Mage::helper('pricecomparison2')->__('Catalog'), Mage::helper('pricecomparison2')->__('Catalog'));
		$this->_addBreadcrumb(Mage::helper('pricecomparison2')->__('Assigned Products'), Mage::helper('pricecomparison2')->__('Assigned Products'),Mage::getUrl('vendors/pricecomparison'));
		$this->_addBreadcrumb(Mage::helper('pricecomparison2')->__('Select and Sell'), Mage::helper('pricecomparison2')->__('Select and Sell'));
		$this->renderLayout();
	}
	
	public function gridAction(){
	    $this->getResponse()->setBody($this->getLayout()->createBlock('pricecomparison2/vendor_selectandsell_grid')->toHtml());
	}
	
	public function addAction(){
	    $productId = $this->getRequest()->getParam('id');
	    $product = Mage::getModel('catalog/product')->load($productId);
	    if(!$productId || !$product->getId()){
	        $this->_getSession()->addError(Mage::helper('pricecomparison2')->__('The product is not available.'));
	        $this->_redirect('*/*');
	        return;
	    }
	    Mage::register('product', $product);
	    $this->loadLayout();
	    $this->_setActiveMenu('catalog')->_title($this->__('Assigned Products'))->_title($this->__('Select and Sell'))->_title($product->getName());
	    $this->_addBreadcrumb(Mage::helper('pricecomparison2')->__('Catalog'), Mage::helper('pricecomparison2')->__('Catalog'));
	    $this->_addBreadcrumb(Mage::helper('pricecomparison2')->__('Assigned Products'), Mage::helper('pricecomparison2')->__('Assigned Products'),Mage::getUrl('vendors/pricecomparison'));
	    $this->_addBreadcrumb(Mage::helper('pricecomparison2')->__('Select and Sell'), Mage::helper('pricecomparison2')->__('Select and Sell'),Mage::getUrl('vendors/pricecomparison/selectandsell'));
	    $this->_addBreadcrumb($product->getName(), $product->getName());
	    $this->renderLayout();
	}
	
	public function editAction(){
	    $priceId = $this->getRequest()->getParam('id');
	    $priceComparison = Mage::getModel('pricecomparison2/pricecomparison')->load($priceId);
	    if(!$priceId || !$priceComparison->getId() || ($priceComparison->getVendorId() != $this->_getSession()->getVendor()->getId())){
	        $this->_getSession()->addError(Mage::helper('pricecomparison2')->__('The product is not available.'));
	        $this->_redirect('*/*');
	        return;
	    }
	    $product = $priceComparison->getProduct();
	    Mage::register('product', $product);
	    Mage::register('price_comparison', $priceComparison);
	    $this->loadLayout();
	    $this->_setActiveMenu('catalog')->_title($this->__('Assigned Products'))->_title($this->__('Select and Sell'))->_title($product->getName());
	    $this->_addBreadcrumb(Mage::helper('pricecomparison2')->__('Catalog'), Mage::helper('pricecomparison2')->__('Catalog'));
	    $this->_addBreadcrumb(Mage::helper('pricecomparison2')->__('Assigned Products'), Mage::helper('pricecomparison2')->__('Assigned Products'),Mage::getUrl('vendors/pricecomparison'));
	    $this->_addBreadcrumb(Mage::helper('pricecomparison2')->__('Select and Sell'), Mage::helper('pricecomparison2')->__('Select and Sell'),Mage::getUrl('vendors/pricecomparison/selectandsell'));
	    $this->_addBreadcrumb($priceComparison->getProduct()->getName(), $product->getName());
	    $this->renderLayout();
	}
	
	
	public function saveAction(){
	    if ($data = $this->getRequest()->getPost()) {
	        if(isset($data['super_attribute']) && is_array($data['super_attribute'])){
	            foreach($data['super_attribute'] as $simpleProductId=>$attrData){
	                if(!isset($attrData['enabled']) || !$attrData['enabled']){
	                    unset($data['super_attribute'][$simpleProductId]);
	                }
	            }
	            
	            $data['additional_info'] = json_encode($data['super_attribute']);
	            unset($data['super_attribute']);
	        }
	        
	        $priceId = $this->getRequest()->getParam('id',null);
	        $model = Mage::getModel('pricecomparison2/pricecomparison');
	        $model->addData($data)
	        ->setId($priceId);
	        $checkOption = $priceId?Mage::helper('pricecomparison2')->updateProductApproval():Mage::helper('pricecomparison2')->newProductApproval();
	        if($checkOption){
	            $model->setStatus(VES_VendorsPriceComparison2_Model_Pricecomparison::STATUS_PENDING);
	        }else{
	            $model->setStatus(VES_VendorsPriceComparison2_Model_Pricecomparison::STATUS_APPROVED);
	        }
	        
	        try {
	            $model->setVendorId($this->_getSession()->getVendor()->getId())->save();
	            Mage::getSingleton('vendors/session')->addSuccess(Mage::helper('vendors')->__('Item was successfully saved'));
	            Mage::getSingleton('vendors/session')->setFormData(false);
	    
	            if ($this->getRequest()->getParam('back')) {
	                $this->_redirect('*/*/edit', array('id' => $model->getId()));
	                return;
	            }
	            $this->_redirect('*/*/');
	            return;
	        } catch (Exception $e) {
	            Mage::getSingleton('vendors/session')->addError($e->getMessage());
	            Mage::getSingleton('vendors/session')->setFormData($data);
	            $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
	            return;
	        }
	    }
	    Mage::getSingleton('vendors/session')->addError(Mage::helper('vendors')->__('Unable to find item to save'));
	    $this->_redirect('*/*/');
	}
	
	public function massDeleteAction(){
	    $ids = $this->getRequest()->getParam('pricecomparison');
	    if($ids){
	        $ids = explode(',', $ids);
	        try {
    	        foreach($ids as $id){
    	            $model = Mage::getModel('pricecomparison2/pricecomparison')->load($id);
    	            $model->delete();
    	        }
    	        $this->_getSession()->addSuccess(
        	        Mage::helper('pricecomparison2')->__('Total of %d record(s) were successfully deleted', count($ids))
    	        );
	        } catch (Exception $e) {
	            $this->_getSession()->addError($e->getMessage());
	        }
	        
	    }else{
	        $this->_getSession()->addError(Mage::helper('vendors')->__('Please select items.'));
	    }
	    $this->_redirect('*/*/');
	}
}