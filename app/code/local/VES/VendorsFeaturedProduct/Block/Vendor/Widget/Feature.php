<?php
class VES_VendorsFeaturedProduct_Block_Vendor_Widget_Feature extends Mage_Core_Block_Template{
//	private $_vendor_id;

	public function _beforeToHtml()
    {
    	Mage::helper('ves_core');
    	if($this->getTemplate()){
    		$this->setTemplate('ves_vendorsfeaturedproduct/'.$this->getTemplate().'.phtml');
    	}else{
    		$this->setTemplate("ves_vendorsfeaturedproduct/list.phtml");
    	}
    	return parent::_beforeToHtml();
    }
    
    public function getFeaturedProducts()
    {
    	Mage::helper('ves_core');;
    	if($this->getCategoryId() == null){
    		return Mage::getModel('vendorsfeaturedproduct/featuredproduct')->getFeaturedProducts($this->getVendor()->getId(),false,false,false);
    	}else{
    		return Mage::getModel('vendorsfeaturedproduct/featuredproduct')->getFeaturedProducts($this->getVendor()->getId(),false,$this->getCategoryId(),false);
    	}
    }
	public function getColumnCount(){
		Mage::helper('ves_core');
		if($this->getColumns()){
			return $this->getColumns();
		}
	}

}