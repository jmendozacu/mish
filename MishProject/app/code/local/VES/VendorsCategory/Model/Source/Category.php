<?php

class VES_VendorsCategory_Model_Source_Category
{
	public function toArray($vendorId){
		$result = array();
		$collection = Mage::getModel('vendorscategory/category')->getCollection()->addFieldToFilter('vendor_id',$vendorId);
		foreach ($collection as $category){
			$result[$category->getId()] = $category->getName();
		}
		return $result;
	}
	
	public function toTreeArray($vendorId){
		$options = $this->getTreeOptions($vendorId,false);
		$result = array();
		foreach($options as $option){
			$result[$option['value']] = $option['label'];
		}
		return $result;
	}
	
	public function toOptionArray($vendorId, $blankRow = true,$excludeIds = array()){
		$result = array();
		if($blankRow)
			$result[] = array(''=>'');
		$categoryCollection = Mage::getModel('vendorscategory/category')->getCollection()->addFieldToFilter('vendor_id',$vendorId);
		if(sizeof($excludeIds)) $categoryCollection->addFieldToFilter('category_id',array('nin'=>$excludeIds))->load();
		foreach ($categoryCollection as $category){
			$result[] = array(
				'label' => $category->getName(),
				'value'	=> $category->getId(),
			);
		}
		return $result;
	}
	
	public function getChildrenCategoryOptions(VES_VendorsCategory_Model_Category $category){
		$result = array();
		foreach($category->getChildrenCategoryCollection(false) as $subcat){
			$name = ' '.$subcat->getName();
			for($i = 0; $i < $subcat->getLevel(); $i++){
				$name= '----'.$name;
			}
			$result[] = array(
				'label' => $name,
				'value'	=> $subcat->getId(),
			);
			$result = array_merge($result,$this->getChildrenCategoryOptions($subcat));
		}
		return $result;
	}
	
	public function getTreeOptions($vendorId, $blankRow = true){
		$rootCategories = Mage::getModel('vendorscategory/category')->getCollection()->addFieldToFilter('vendor_id',$vendorId)->addFieldToFilter('level',0);
		$result = array();
		if($blankRow) $result[] = array(''=>'');
		
		foreach($rootCategories as $category){
			$result[] = array(
				'label' => $category->getName(),
				'value'	=> $category->getId(),
			);
			$result = array_merge($result,$this->getChildrenCategoryOptions($category));
		}
		return $result;
	}
	
	public function getAttributeSources($blankRow = true){
		$rootCategories = Mage::getModel('vendorscategory/category')->getCollection()->addFieldToFilter('level',0);
		$result = array();
		if($blankRow) $result[] = array(''=>'');
	
		foreach($rootCategories as $category){
			$result[] = array(
					'label' => $category->getName(),
					'value'	=> $category->getId(),
			);
			$result = array_merge($result,$this->getChildrenCategoryOptions($category));
		}
		return $result;
	}
	
	/**
	 * get array of category model for sortable collection category
	 * @param unknown $vendorId
	 * @return Ambigous <multitype:, multitype:unknown >
	 */
	public function getCatGridCollection($vendorId) {
		$rootCategories = Mage::getModel('vendorscategory/category')->getCollection()->addFieldToFilter('vendor_id',$vendorId)->addFieldToFilter('level',0);
		$result = array();
		
		foreach($rootCategories as $category) {
			$result[] = $category;
			$result = array_merge($result, $this->getSubCatGrid($category));
		}
		
		return $result;
	}
	
	public function getSubCatGrid($category) {
		$result = array();
		foreach($category->getChildrenCategoryCollection(false) as $subcat){
			$result[] = $subcat;
			$result = array_merge($result, $this->getSubCatGrid($subcat));
		}
		return $result;
	}
	
}