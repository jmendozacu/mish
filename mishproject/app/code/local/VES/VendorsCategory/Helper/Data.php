<?php

class VES_VendorsCategory_Helper_Data extends Mage_Core_Helper_Abstract
{	
	/**
	 * Is Module Enable
	 */
	public function moduleEnable(){
		$result = new Varien_Object(array('module_enable'=>true));
		Mage::dispatchEvent('ves_vendorscategory_module_enable',array('result'=>$result));
		return $result->getData('module_enable');
	}
	
	public function getVendorPageKey() {
		return Mage::getStoreConfig('vendors/vendor_page/url_key');
	}
	public function getCategoryAjaxUrl(){
		return Mage::getUrl('vendors/catalog/product_vendorcategory');
	}
	
	/**
	 * Check if a category can be shown
	 *
	 * @param  VES_VendorsCategory_Model_Category|int $category
	 * @return boolean
	 */
	public function canShow($category)
	{
		if (is_int($category)) {
			$category = Mage::getModel('vendorscategory/category')->load($category);
		}
	
		if (!$category->getId()) {
			return false;
		}
	
		if (!$category->getIsActive()) {
			return false;
		}
	
		return true;
	}
	
	/**
	 * get breadcumb array
	 * array('cat'=>array('label'=>'','link'=>'')
	 */
	public function getBreadcrumbPath() {
		$current_category = Mage::registry('current_vendor_category');
		$result = array();
// 		$result['category'.$current_category->getCategoryId()] = array('label'=>$current_category->getName(),'link'=>'','level'=>$current_category->getLevel());
// 		$result = array_merge($result,$this->getParentBreadcumb($current_category));
		
// 		return array_reverse($result);
		$category_ids = Mage::getModel('vendorscategory/category')->getAllParentCategoryIds($current_category);
		foreach($category_ids as $_cat) {
			$model = Mage::getModel('vendorscategory/category')->load($_cat);
			$result['category'.$model->getCategoryId()] = array('label'=>$model->getName(),'link'=>$model->getCategoryLink(),'level'=>$model->getLevel());
		}
		$result = array_reverse($result);
		$result['category'.$current_category->getCategoryId()] = array('label'=>$current_category->getName(),'link'=>'','level'=>$current_category->getLevel());
		return $result;
	}
	
// 	public function getParentBreadcumb($cat) {
// 		$result = array();
// 		if($cat->getLevel() == '0') return $result;
// 		$parent = $cat->getParentCategory();
// 		$result['category'.$parent->getCategoryId()] = array('label'=>$parent->getName(),'link'=>$parent->getCategoryLink(),'level'=>$parent->getLevel());
// 		$result = array_merge($result,$this->getParentBreadcumb($parent));
		
// 		return $result;
// 	}
	
	
	public function getPageLayout() {
		return Mage::registry('current_category')->getPageLayout();
	}

    public function generateRootNode() {
        $data = array('category_id'=>'0','text'=>'Root','name'=>'Root','level'=>'-1','sort_order'=>'0');

        $node = new VES_VendorsCategory_Model_Category();
        $node->setData($data);

        return $node;
    }
}