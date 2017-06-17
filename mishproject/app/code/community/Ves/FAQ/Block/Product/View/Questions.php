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
class Ves_FAQ_Block_Product_View_Questions extends Mage_Core_Block_Template
{
	/**
	 * Question collection
	 *
	 * @var Ves_FAQ_Model_Mysql4_Question_Collection
	 */
	protected $_collection = null;
	protected $_category = null;

	public function __construct(){
		if(!$this->getConfig('enable', 'general_setting'))
			return;
		if(!$this->getConfig('enable_faq', 'product_page'))
			return;
		parent::__construct();

		if(isset($attributes['template']) && $attributes['template']) {
			$my_template = $attributes['template'];
		} elseif ($this->hasData("template")) {
			$my_template = $this->getData("template");
		} else {
			$my_template = "ves/faq/product/view/questions.phtml";
		}

		$this->setTemplate($my_template);
		/*Get filter and sorting*/
		$filter_category = $this->getRequest()->getParam("filter_category");
		$sort_order = $this->getRequest()->getParam("order");
		$sort_dir = $this->getRequest()->getParam("dir");
		/*End Get filter and sorting*/

		$product = $this->getProduct();
		$questions = $this->getQuestions($product->getId(), $filter_category, array('order'=>$sort_order,'dir'=>$sort_dir));
		$show_pager = $this->getConfig('show_pager', 'product_page');
		if(!$show_pager){
			$questions_count = (int)$this->getConfig('questions_count', 'product_page');
			if($questions_count){
				$questions->setPageSize($questions_count);
			}else{
				$questions->setPageSize(5);
			}
		}
		if($questions){
			$this->setCollection($questions);
		}

	}

	protected function _prepareLayout()
	{
		if(!$this->getConfig('enable', 'general_setting'))
			return;
		if(!$this->getConfig('enable_faq', 'product_page'))
			return;
		
		parent::_prepareLayout();

		$show_pager = $this->getConfig('show_pager', 'product_page');
		if($show_pager){
			$list_pages = array();
			$list_per_page_values = $this->getConfig('list_per_page_values', 'product_page', '');
			$list_per_page_values = trim($list_per_page_values);
			if($list_per_page_values) {
				$list_per_page_values = explode(",", $list_per_page_values);
				foreach($list_per_page_values as $val) {
					$val = trim($val);
					$val = (int)$val;
					$list_pages[$val] = $val;
				}
			} else {
				$list_pages = array(	5=>5,
										10=>10,
										15=>15,
										20=>20,
										30=>30,
										50=>50);
			}
			
			$list_pages['all'] = $this->__('All');

			$pager = $this->getLayout()->createBlock('page/html_pager', 'custom.pager');
			$pager->setAvailableLimit( $list_pages );
			$pager->setCollection($this->getCollection());
			$this->setChild('pager', $pager);
			$this->getCollection()->load();
		}
		return $this;
	}

	public function getPagerHtml()
	{
		return $this->getChildHtml('pager');
	}

	public function getProduct(){
		return Mage::registry('current_product');
	}

	/**
	 * Set collection to block
	 *
	 * @param Varien_Data_Collection $collection
	 * @return Ves_FAQ_Block_Questions
	 */
	public function setCollection($collection){
		$this->_collection = $collection;
		return $this;
	}

	/**
	 * Return question collection instance
	 *
	 * @return Ves_FAQ_Model_Mysql4_Question_Collection
	 */
	public function getCollection(){
		return $this->_collection;
	}

	/**
	 * Retrive question list
	 * @param int $product_id
	 * @return Ves_FAQ_Model_Mysql4_Question_Collection
	 */
	public function getQuestions($product_id, $filter_category = 0, $sort = array()){
		$store = Mage::app()->getStore();
		$show_all = $this->getConfig('show_all_faq', 'product_page', 0);

		$collection = Mage::getModel('ves_faq/question')->getCollection();

		if(!$show_all) {
			$collection->addFieldToFilter('product_id',array( 'eq'=> $product_id ));
		} else {
			$collection->addFieldToFilter('product_id', array( 'in'=> array("0", $product_id ) ));
		}

		if($filter_category) {
			$collection->addFieldToFilter('category_id',array( 'eq'=> $filter_category ));
		}
		$sort = 'question_id';
		$dir = 'DESC';

		if(isset($sort['order']) || isset($sort['dir'])) {
			if(isset($sort['order']) && $sort['order']) {
				$sort = isset($sort['order']);
			}
			if(isset($sort['dir']) && $sort['dir']) {
				$dir = isset($sort['dir']);
			}
		}

		$collection->addFieldToFilter('status', array( 'eq' => 1 ))
					->addVisibilityFilter()
					->addStoreFilter()
					->setOrder($sort, $dir);
		return $collection;
	}

	/**
	 * Retrive list category
	 */
	public function getListCategory(){
		if(!$this->_category) {
			$store = Mage::app()->getStore();
			$collection = Mage::getModel('ves_faq/category')->getCollection()
						->addFieldToFilter('status', array('eq'=>1))
						->addStoreFilter($store)
						->setOrder('position','ASC');
			$this->_category = $collection;
		} 
		

		return $this->_category;
	}

	public function getCategoryTitle($categoryId = 0) {
		$category_title = "";

		if($categoryId) {
			$categories = $this->getListCategory();

			if($categories) {
				foreach($categories as $item) {

					if($categoryId == $item->getId()) {
						$category_title = $item->getName();
						break;
					}
				}
			}
		}
		return $category_title;
	}

	public function getSaveUrl(){
		return Mage::getUrl('venusfaq/index/save');
	}

	/**
	 * get value of the extension's configuration
	 *
	 * @return string
	 */
	public function getConfig( $key, $panel='module_setting', $default = ""){
		$return = "";
		$value = $this->getData($key);
		if($this->hasData($key) && $value !== null) {
			return $value;
		} else {

			$return = Mage::getStoreConfig("ves_faq/$panel/$key");

			if($return == "" && $default) {
				$return = $default;
			}
		}
		return $return;
	}

	public function getReCaptcha(){
		return Mage::helper('ves_faq/recaptcha')
		->setKeys( $this->getConfig('private_key','recaptcha'), $this->getConfig('public_key','recaptcha') )
		->setTheme( $this->getConfig('theme','recaptcha') )
		->setLang( $this->getConfig('lang','recaptcha') )
		->getReCapcha();
	}
}