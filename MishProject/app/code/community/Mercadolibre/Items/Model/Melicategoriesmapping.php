<?php

class Mercadolibre_Items_Model_Melicategoriesmapping extends Mage_Core_Model_Abstract
{
    
	private $moduleName = "Items";
	private $fileName = "MeliCategoriesMapping.php";
	
	public function _construct()
    {
        parent::_construct();
        $this->_init('items/melicategoriesmapping');
    }
	
	
	public function  getMageCategoriesRecursive($categories) {
			try{
				$commonModel = Mage::getModel('items/common');
				$str = '';
				foreach($categories as $category) {
					$cat =  Mage::getModel('catalog/category')
							->getCollection()
							->addFieldToFilter('entity_id', array('eq'=>$category->getId()))
							->addFieldToFilter('store_id', array('eq'=>'0'))
							->addFieldToFilter('is_active', array('eq'=>'1'))
							->addAttributeToSort('position', 'ASC')
							->addAttributeToSelect('id')
							->addAttributeToSelect('name');

							//echo "<br>".$category->getName(); 
					echo "<pre>";
					print_R($cat->getData());
					die;
					if(trim($cat->getName())!='Root Catalog'){		
						$str .= $cat->getId().'##MLCATID##'.$cat->getName()."=>";		 
					}
					if($cat->hasChildren()) {
						$children = Mage::getModel('catalog/category')->getCategories($cat->getId());
						$str .= $this->getMageCategoriesRecursive($children);						 
					}
				}
				return  $str;
			}catch(Exception $e){
					$commonModel->saveLogger($this->moduleName, "Exception", $this->fileName, $e->getMessage());	
				}
		}
		
		public function getMlUpdateCategoriesMapping(){
				
				try{
					
					$commonModel = Mage::getModel('items/common');
					
					$rootcatId= Mage::app()->getStore()->getRootCategoryId(); 
					$categories = Mage::getModel('catalog/category')
								->getCollection()
								//->addFieldToFilter('parent_id', array('eq'=>$rootcatId))
								->addFieldToFilter('level', array('eq'=>'2'))
								->addFieldToFilter('is_active', array('eq'=>'1'))
								->addAttributeToSort('position', 'ASC')
								->addAttributeToSelect('id')
								->addAttributeToSelect('name');
	              
					$MageCateData = array();
					$MageCateData =  $this->getMageCategoriesRecursive($categories); 

					$arrayMageCat = explode('=>',$MageCateData);
					
					
					foreach($arrayMageCat as $key => $value){
							$valArr = explode('##MLCATID##',$value);
							if(isset($valArr['1']) && trim($valArr['1'])!=''){
								$mageCateCollection[$key] = array($valArr['0'],$valArr['1']);
								$mageCateIdCollection[] = $valArr['0'];
							}
					}
					
					/* Get Existing mage_category_id From meli_categories_mapping Table  */		
					$collection = Mage::getModel('items/melicategoriesmapping')->getCollection()
								  ->addFieldToSelect('mage_category_id');
					$mageCategoryIdMapping = $collection->getData();
					$existMageCateIdMapping = array(); 
					foreach($mageCategoryIdMapping as $row){
						$existMageCateIdMapping[] = $row['mage_category_id'];
					}
					/* Check For Exist mage_category_id In meli_categories_mapping Table */
					if(is_array($mageCateIdCollection) && count($mageCateIdCollection) > 0 && is_array($existMageCateIdMapping) && count($existMageCateIdMapping) > 0){
						$mageCateIdCollection = array_diff($mageCateIdCollection, $existMageCateIdMapping);
					}
					
					/* Insert Not Existing mage_category_id Into meli_categories_mapping Table  */	
					if(isset($mageCateIdCollection) && count($mageCateIdCollection) > 0){
						$sql_meli_mapping = '';
						foreach($mageCateIdCollection as $kay => $mage_category_id){
							$sql_meli_mapping .= "insert into `mercadolibre_categories_mapping` set mage_category_id ='".$mage_category_id."'".";";	
						}
						$write = Mage::getSingleton('core/resource')->getConnection('core_write');
						$write->multiQuery($sql_meli_mapping);
					}
				}catch(Exception $e){
					echo "<br>EXception---:::".$e->getMessage();
					$commonModel->saveLogger($this->moduleName, "Exception", $this->fileName, $e->getMessage());	
				}
		}
	
	
}