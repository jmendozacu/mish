<?php
/**
 * Venustheme
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Venustheme EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.venustheme.com/LICENSE-1.0.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.venustheme.com/ for more information
 *
 * @category   Ves
 * @package    Ves_FAQ
 * @copyright  Copyright (c) 2014 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */

/**
 * Ves FAQ Extension
 *
 * @category   Ves
 * @package    Ves_FAQ
 * @author     Venustheme Dev Team <venustheme@gmail.com>
 */
class Ves_FAQ_Helper_Data extends Mage_Core_Helper_Abstract
{

	public function getCatalogSearchLink() {
		$route = Mage::getStoreConfig("ves_faq/general_setting/route");
		if($route == ''){
			$route = 'vesfaq';
		}
		return Mage::getUrl().$route.'/search';
	}



	public function subString( $text, $length = 100, $replacer ='...', $is_striped=true ){
		$text = ($is_striped==true)?strip_tags($text):$text;
		if(strlen($text) <= $length){
			return $text;
		}
		$text = substr($text,0,$length);
		$pos_space = strrpos($text,' ');
		return substr($text,0,$pos_space).$replacer;
	}

	/**
	 * Retrive list category
	 */
	public function getListCategory(){
		$collection = Mage::getModel('ves_faq/category')->getCollection();
		$categories = array();
		$categories[] = Mage::helper('ves_faq')->__('-- Please select --');
		foreach ($collection as $_category) {
			$categories[$_category->getCategoryId()] = $_category->getName();
		}
		return $categories;
	}

	public function getLayoutList(){
		$output = array();
		$output['two_columns_left'] = '2 columns with left sidebar';
		$output['two_columns_right'] = '2 columns with right sidebar';
		$output['empty'] = 'Empty';
		$output['one_column'] = '1 column';
		$output['three_columns'] = '3 columns';
		return $output;
	}

	public function getCategoryLink($category){
		$route = Mage::getStoreConfig('ves_faq/general_setting/route');
		$identifier = $category->getIdentifier();

		if($route != '' && $category->getIdentifier()!=''){
			$category_link = $route.'/'.$identifier.'.html';
			return Mage::getUrl().$category_link;
		}
		return;
	}

	public function get( $attributes = NULL)
	{
		$data = array();
		$arrayParams = array('show',
			'route',
			'item_number',
			'title',
			'category_id',
			'show_answers_list',
			'mode_list',
			'questions_count'
			);
		foreach ($arrayParams as $var)
		{
			$tags = array('general_setting','faq_page');
			$var = trim( $var );
			foreach($tags as $tag){
				if(Mage::getStoreConfig("ves_faq/$tag/$var")!=""){
					$data[$var] =  Mage::getStoreConfig("ves_faq/$tag/$var");
				}
			}
			if(isset($attributes[$var]))
			{
				$data[$var] =  $attributes[$var];
			}
		}

		return $data;
	}

	public function getProductList(){
		$output = array();
		$output[] = Mage::helper('ves_faq')->__('-- Please select --');
		$collection = Mage::getResourceModel('catalog/product_collection')
		->addAttributeToSelect('*')
		->addAttributeToFilter(
			'status',
			array('eq' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
			)
		->addFieldToFilter('visibility', Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
		->setOrder ('name','ASC');
		foreach ($collection as $_product) {
			$output[$_product->getId()] = $_product->getName();
		}
		return $output;
	}

	public function getFAQSLink() {
		return Mage::getBaseUrl().Mage::getStoreConfig('ves_faq/general_setting/route').".html";
	}

	public function getAllStores() {
	 		$allStores = Mage::app()->getStores();
	 		$stores = array();
	 		foreach ($allStores as $_eachStoreId => $val)
	 		{
	 			$stores[]  = Mage::app()->getStore($_eachStoreId)->getId();
	 		}
	 		return $stores;
	 	}

	 	public function getImportPath($theme = ""){
	 		$path = Mage::getBaseDir('var') . DS . 'cache'.DS;

	 		if (is_dir_writeable($path) != true) {
	 			mkdir ($path, '0744', $recursive  = true );
        } // end

        return $path;
    }

	/**
     * Handles CSV upload
     * @return string $filepath
     */
	public function getUploadedFile() {
		$filepath = null;
		if(isset($_FILES['importfile']['name']) and (file_exists($_FILES['importfile']['tmp_name']))) {
			try {
				$uploader = new Varien_File_Uploader('importfile');
                $uploader->setAllowedExtensions(array('csv','txt', 'json', 'xml')); // or pdf or anything
                $uploader->setAllowRenameFiles(false);
                $uploader->setFilesDispersion(false);
                $path = Mage::helper('ves_faq')->getImportPath();
                $file_type = "csv";
                if(isset($_FILES['importfile']['tmp_name']['type']) && $_FILES['importfile']['tmp_name']['type'] == "application/json") {
                	$file_type = "json";
                }
                $uploader->save($path, "ves_faq_import_data.".$file_type);
                $filepath = $path . "ves_faq_import_data.".$file_type;
            } catch(Exception $e) {
            	Mage::logException($e);
            }
        }
        return $filepath;
    }
 
 	public function isAdmin()
    {
        if(Mage::app()->getStore()->isAdmin())
        {
            return true;
        }

        if(Mage::getDesign()->getArea() == 'adminhtml')
        {
            return true;
        }

        return false;
    }
	public function isShowSearchForm() {
		return Mage::getStoreConfig("ves_faq/faq_page/show_search_box");
	}

}