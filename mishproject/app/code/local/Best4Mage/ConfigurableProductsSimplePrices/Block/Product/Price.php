<?php 

class Best4Mage_ConfigurableProductsSimplePrices_Block_Product_Price extends Mage_Catalog_Block_Product_Price {

	public function _toHtml() {
		$html = trim(parent::_toHtml());

		if(preg_match('/tier/i', $this->getTemplate())) { 
			return $html;
		}

		$_product = $this->getProduct();
		//$_loadedProduct = Mage::getModel('catalog/product')->load($_product->getId());
		$_cpspHelper = Mage::helper('configurableproductssimpleprices');
		$_isEnable = $_cpspHelper->isEnable($_product); 
        $_showLowestPrice = $_cpspHelper->isShowLowestPrice($_product); 
        $_showMaxRegularPrice = $_cpspHelper->isShowMaxRegularPrice($_product);  

		if(empty($html) || ($_product->getTypeId() != 'configurable') || !$_isEnable || (!$_showLowestPrice && !$_showMaxRegularPrice)) { 
			return $html;
		}
		
        $htmlObject = new Varien_Object();  
		if($_showMaxRegularPrice) {  
            $htmlTemplate = $this->getLayout()->createBlock('core/template')
                ->setTemplate('configurableproductssimpleprices/max-regular-price.phtml')
                ->setProduct($this->getProduct())
				->setIdSuffix($this->getIdSuffix())
                ->toHtml();
            $htmlObject->setHtml($htmlTemplate);
			$html = $htmlObject->getPrefix();
        	$html .= $htmlObject->getHtml();
		}

		if(Mage::app()->getRequest()->getModuleName() == 'wishlist') {
			$minHtmlObject = new Varien_Object();
	        $minHtmlTemplate = $this->getLayout()->createBlock('core/template')
	            ->setTemplate('configurableproductssimpleprices/wishlist_price.phtml')
	            ->setProduct($this->getProduct())
				->setIdSuffix($this->getIdSuffix())
	            ->toHtml();
	        $minHtmlObject->setHtml($minHtmlTemplate);
	        if($_showMaxRegularPrice) { 
	        	$html .= $minHtmlObject->getHtml();
	        } else {
	        	$html = $minHtmlObject->getHtml();
	        }
	        $html .= $htmlObject->getSuffix();
			return $html;
		}

		if($_showLowestPrice) { 
			$minHtmlObject = new Varien_Object();
	        $minHtmlTemplate = $this->getLayout()->createBlock('core/template')
	            ->setTemplate('configurableproductssimpleprices/min-price.phtml')
	            ->setProduct($this->getProduct())
				->setIdSuffix($this->getIdSuffix())
	            ->toHtml();
	        $minHtmlObject->setHtml($minHtmlTemplate);
	        if(in_array(Mage::app()->getRequest()->getControllerName(), array('category','result'))) {
	        	$html = $minHtmlObject->getHtml();
	        } else {
	       		$html .= $minHtmlObject->getHtml();
	       	}
	    }
	    $html .= $htmlObject->getSuffix();
		return $html;
	}
}