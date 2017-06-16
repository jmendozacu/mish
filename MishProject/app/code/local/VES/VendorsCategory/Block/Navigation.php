<?php
class VES_VendorsCategory_Block_Navigation extends Mage_Core_Block_Template{
	
	protected function _toHtml(){
		if(!Mage::helper('vendorscategory')->moduleEnable() || !sizeof($this->getRootCategories())) return '';
		return parent::_toHtml();
	}
	
	public function getApp(){
		return new Varien_Object(array('id'=>'vendor.category'));
	}
	
	public function getNavigationHtml() {
		$html = '<ul>';
		foreach($this->getRootCategories() as $_root) {
			$html .= '<li class="level'.$_root->getLevel().'">';
			$html .= '<a href="'.$_root->getCategoryUrl($this->getVendorId()) . '">'.$_root->getName().'</a>';
			$html .= $this->getSubCategoriesHtml($_root);
			$html .= '</li>';
		}
		$html .= '</ul>';
		
		return $html;
	}
	
	public function getSubCategoriesHtml($category) {
		$html = '';
		foreach($category->getChildrenCategoryCollection() as $subcat) {
			$html .='<ul style="padding-left:15px;">';
			$html .= '<li class="level'.$subcat->getLevel().'">';
			$html .= '<a href="'.$subcat->getCategoryUrl($this->getVendorId()) . '">'.$subcat->getName().'</a>';
			
			$html .= $this->getSubCategoriesHtml($subcat);
			$html .= '</li>';
			$html .= '</ul>';
		}
		
		
		return $html;
	}
	
	public function getRootCategories() {
		return Mage::getModel('vendorscategory/category')->getCollection()->addFieldToFilter('vendor_id',$this->getVendor()->getId())
		->addFieldToFilter('level',0)
		->addFieldToFilter('is_active',1);
	}
	
	/**
	 * Retrieve child categories of current category
	 *
	 * @return Varien_Data_Tree_Node_Collection
	 */
	public function getCurrentChildCategories()
	{
		$category   = Mage::registry('current_vendor_category');
		/* @var $category Mage_Catalog_Model_Category */
		return $category->getChildrenCategoryCollection();
	}
	
	/**
	 * Get url for category data
	 *
	 * @param Mage_Catalog_Model_Category $category
	 * @return string
	 */
	public function getCategoryUrl($category)
	{
		$url = $category->getCategoryLink();
	
		return $url;
	}
	
	/**
	 * Checkin activity of category
	 *
	 * @param   Varien_Object $category
	 * @return  bool
	 */
	public function isCategoryActive($category)
	{
		return true;
	}
	public function getVendorId() {
		return Mage::registry('vendor_id');
	}
	
	public function getVendor() {
		return Mage::registry('vendor');
	}
}