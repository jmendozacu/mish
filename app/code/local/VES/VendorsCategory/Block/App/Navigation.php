<?php
class VES_VendorsCategory_Block_App_Navigation extends Mage_Core_Block_Template{
	protected function _toHtml(){
		if(!$this->isEnabled()) return '';
		return parent::_toHtml();
	}
	public function isEnabled(){
		$result = new Varien_Object(array('is_enabled'=>true));
		Mage::dispatchEvent('ves_vendorscategory_app_type_category_navigation',array('result'=>$result));
		return $result->getIsEnabled() && Mage::helper('vendorscategory')->moduleEnable();
	}
	
	public function setApp($app){
		$this->setData('app',$app);
		$options = $app->getOptionsByCode('category_option');
		if(!sizeof($options)) return;
		$optionValue = json_decode($options[0]->getValue(),true);
		/*Get navigation template*/
		$template	 = $optionValue['template'];
		$this->setTemplate($template);
		return $this;
	}
	
	public function getNavigationHtml($class=null) {
		$html = '<ul class="clearer vendor-nav'.($class?' '.$class:'').'" id="vendor-nav-'.$this->getApp()->getId().'">';
		foreach($this->getRootCategories() as $_root) {
			$selected = (Mage::registry('current_vendor_category') &&  (Mage::registry('current_vendor_category')->getId()== $_root->getId())?' selected':'');
			$html .= '<li class="level'.$_root->getLevel().$selected.'">';
			$html .= '<a href="'.$_root->getCategoryUrl($this->getVendorId()) . '"><span>'.$_root->getName().'</span></a>';
			$html .= $this->getSubCategoriesHtml($_root);
			$html .= '</li>';
		}
		$html .= '</ul>';
		
		return $html;
	}
	
	public function getSubCategoriesHtml($category) {
		$html = '';
		$subCats = $category->getChildrenCategoryCollection();
		if($subCats->count()){
			$html .='<ul>';
			foreach($subCats as $subcat) {
				$selected = (Mage::registry('current_vendor_category') &&  (Mage::registry('current_vendor_category')->getId()== $subcat->getId())?' selected':'');
				$html .= '<li class="level'.$subcat->getLevel().$selected.'">';
				$html .= '<a href="'.$subcat->getCategoryUrl($this->getVendorId()) . '"><span>'.$subcat->getName().'</span></a>';
				
				$html .= $this->getSubCategoriesHtml($subcat);
				$html .= '</li>';
			}
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