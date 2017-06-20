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
class Ves_FAQ_Block_Faq extends Mage_Core_Block_Template
{

	/**
	 * Question collection
	 *
	 * @var Ves_FAQ_Model_Mysql4_Question_Collection
	 */
	protected $_collection = null;
	protected $_config = '';
	protected $_attributes = array();
	protected $_iswidget = false;

	/**
     * Initialize FAQ template
     *
     */
	public function __construct($attributes = array()){
		if(!Mage::getStoreConfig('ves_faq/general_setting/enable'))
			return;
		$this->_attributes = $attributes;
		$helper =  Mage::helper('ves_faq/data');
		$this->_config = $helper->get($attributes);
		$this->convertAttributesToConfig($this->_config);
		parent::__construct($attributes);

		if(isset($attributes['template']) && $attributes['template']) {
			$my_template = $attributes['template'];
		} elseif ($this->hasData("template")) {
			$my_template = $this->getData("template");
		} else {
			$mode = $this->getConfig('mode_list');
			if($mode == 'mode1'){
				$my_template = 'ves/faq/layout/mode1.phtml';
			}
			if($mode == 'mode2'){
				$my_template = 'ves/faq/layout/mode2.phtml';
			}
			if($mode == 'mode3'){
				$my_template = 'ves/faq/layout/mode3.phtml';
			}
			if($mode == 'mode4'){
				$my_template = 'ves/faq/layout/mode4.phtml';
			}
			if( $this->getConfig( "template" ) ){
				$my_template = $this->getConfig( "template" );
			}
		}

		$this->setTemplate($my_template);
		$collection = array();

		$mode = $this->getConfig('mode_list');
		if($mode == 'mode4'){
			$collection = $this->getLatestQuestion();
		}else{

			$categories = $this->getListCategory();
			foreach ($categories as $_category) {
				$categoryId = $_category->getCategoryId();
				$questions = $this->getListQuestion($categoryId);
				$_category->setData('questions',$questions);
				$collection[] = $_category;
			}
		}
		$this->setCollection($collection);
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
	 * Retrive category
	 * @param  [int] $questionId
	 */
	public function getCategory($questionId){
		$category = Mage::getSingleton('ves_faq/category')->load($questionsId);
		return $category;
	}

	//prepare layout
	protected function _prepareLayout()
	{
		if($this->_iswidget) {
			return parent::_prepareLayout();
		}
		$page_title = Mage::getStoreConfig('ves_faq/faq_page/page_title');
		$meta_keywords = Mage::getStoreConfig('ves_faq/faq_page/meta_keywords');
		$meta_description = Mage::getStoreConfig('ves_faq/faq_page/meta_description');

		$headBlock = $this->getLayout()->getBlock('head');


		if ($page_title != NULL){
			$headBlock->setTitle($page_title);
		}
		if ($meta_description != NULL){
			$headBlock->setDescription($meta_description);
		}
		if ($meta_keywords != NULL){
			$headBlock->setKeywords($meta_keywords);
		}

		$breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
		if ($breadcrumbs) {
			$breadcrumbs->addCrumb('home', array(
				'label' => $this->__('Home'),
				'title' => $this->__('Go to Home Page'),
				'link'  => Mage::getBaseUrl()
				));
			$breadcrumbs->addCrumb('faq', array(
				'label' => $this->__("FAQ"),
				'title' => $this->__("FAQ"),
				'link'  => ''
				));
		}

		$mode = $this->getConfig('mode_list');
		$enale = Mage::getStoreConfig('ves_faq/general_setting/enable');
		if($mode == 'mode4' && $enale && $this->getCollection() && $this->getCollection()->getSize()>0){
			$list_per_page_values = explode(',',$this->getConfig('list_per_page_values'));
			$list_per_page = $this->getConfig('list_per_page');
			$arrAvai = array();
			foreach ($list_per_page_values as $k => $v) {
				$arrAvai[$v] = $v;
			}
			$arrAvai['all'] = 'All';
			$pager = $this->getLayout()->createBlock('page/html_pager','faq.question.pager');
			$pager->setAvailableLimit($arrAvai);
			$pager->setCollection($this->getCollection());
			$this->setChild('pager', $pager);
			$this->getCollection()->load();
		}


		return parent::_prepareLayout();
	}

	public function getPagerHtml(){
		return $this->getChildHtml('pager');
	}


	public function _toHtml(){
		return parent::_toHtml();
	}

	/**
	 * Retrive list category
	 */
	public function getListCategory(){
		$collection = Mage::getModel('ves_faq/category')->getCollection()
		->addFieldToFilter('status', array('eq'=>1))
		->addStoreFilter()
		->setOrder('position','ASC');
		return $collection;
	}

	/**
	 * Retrive list question
	 *
	 */
	public function getLatestQuestion(){
		$collection = Mage::getModel('ves_faq/question')->getCollection()
		->addFieldToFilter('status', array('eq'=>1))
		->addFieldToFilter('status', array('visibility'=>1))
		->addStoreFilter()
		->addVisibilityFilter()
		->setOrder('position','ASC')
		->setOrder('question_id','DESC');
		return $collection;
	}

	/**
	 * Retrive list question
	 * @param  [int] $categoryId
	 */
	public function getListQuestion($categoryId){
		$questions_count = $this->getConfig('questions_count',5);
		$collection = Mage::getModel('ves_faq/question')->getCollection()
		->addFieldToFilter('category_id',array('eq'=>$categoryId))
		->addFieldToFilter('status', array('eq'=>1))
		->addStoreFilter()
		->setPageSize($questions_count)
		->addVisibilityFilter()
		->setOrder('position','ASC')
		->setOrder('question_id','DESC');
		return $collection;
	}

	public function convertAttributesToConfig($attributes = array()) {
		if($attributes) {
			foreach($attributes as $key=>$val) {
				$this->setConfig($key, $val);
			}
		}
	}

	/**
	 * get value of the extension's configuration
	 *
	 * @return string,
	 */
	public function getConfig( $key, $default = ""){
		$return = "";
		$value = $this->getData($key);
	    //Check if has widget config data
		if($this->hasData($key) && $value !== null) {
			if($value == "true") {
				return 1;
			} elseif($value == "false") {
				return 0;
			}

			return $value;

		} else {

			if(isset($this->_config[$key])){
				$return = $this->_config[$key];
			}else{
				$return = Mage::getStoreConfig("ves_faq/faq_page/$key");
			}

			if($return == "" && $default) {
				$return = $default;
			}
		}
		return $return;
	}

	/**
     * overrde the value of the extension's configuration
     *
     * @return string
     */
	function setConfig($key, $value) {
		if($value == "true") {
			$value =  1;
		} elseif($value == "false") {
			$value = 0;
		}
		if($value != "") {
			$this->_config[$key] = $value;
		}

		return $this;
	}

}