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
class Ves_FAQ_Block_Category_View extends Mage_Core_Block_Template
{
	/**
	 * Question collection
	 *
	 * @var Ves_FAQ_Model_Mysql4_Question_Collection
	 */
	protected $_collection = null;
	private $category;
	protected $_category = '';
	public function __construct()
	{
		parent::__construct();

		if(isset($attributes['template']) && $attributes['template']) {
			$my_template = $attributes['template'];
		} elseif ($this->hasData("template")) {
			$my_template = $this->getData("template");
		}else{
			$my_template = 'ves/faq/category/view.phtml';
		}

		$categoryId = $this->getRequest()->getParam('category_id');
		$this->setCategory($categoryId);

		$collection = array();
		if($categoryId){
			$collection = $this->getQuestions($categoryId);
		}
		$this->setCollection($collection);
		$this->setTemplate($my_template);
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


	public function setCategory($categoryId){
		$cat = Mage::getModel('ves_faq/category')->load($categoryId);
		$this->_category = $cat;
		return $this;
	}

	public function getCategory(){
		return $this->_category;
	}

	public function _toHtml(){
		return parent::_toHtml();
	}

	protected function _prepareLayout()
	{
		$breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
		if ($breadcrumbs) {
			$name = $this->getCategory()->getName();

			$breadcrumbs->addCrumb('home', array( 'label' => $this->__('Home'),  'title' => $this->__('Go to Home Page'), 'link'  => Mage::getBaseUrl() ))->addCrumb('faq', array(
				'label' => $this->__("FAQ"),
				'title' => $this->__("FAQ"),
				'link'  => Mage::getUrl( Mage::getStoreConfig('ves_faq/general_setting/route') )
				));
			$breadcrumbs->addCrumb('item', array(
				'label' => $name,
				'title' => $name,
				));
		}
		if ($this->getCategory()->getTitle()) {
			$title = $this->getCategory()->getTitle();
		} else {
			$title = $this->__($this->getCategory()->getName());
		}
		$this->getLayout()->getBlock('head')->setTitle($title);

		if ($this->getCategory()->getMetaKeywords()) {
			$keywords = $this->getCategory()->getMetaKeywords();
			$this->getLayout()->getBlock('head')->setKeywords($keywords);
		}
		if ($this->getCategory()->getMetaDescription()) {
			$description = $this->getCategory()->getMetaDescription();
			$this->getLayout()->getBlock('head')->setDescription($description);
		}

		$pager = $this->getLayout()->createBlock('page/html_pager','faq.question.pager');
		$pager->setAvailableLimit(array(5=>5,10=>10,20=>20,'all'=>'all'));
		$pager->setCollection($this->getCollection());
		$this->setChild('pager', $pager);
		$this->getCollection()->load();

		return parent::_prepareLayout();
	}

	public function getPagerHtml()
	{
		return $this->getChildHtml('pager');
	}

	/**
	 * Retrive list question
	 * @param  [int] $categoryId
	 */
	public function getQuestions($categoryId){
		$collection = Mage::getModel('ves_faq/question')->getCollection()
		->addFieldToFilter('category_id',array('eq'=>$categoryId))
		->addFieldToFilter('status', array('eq'=>1))
		->addFieldToFilter('status', array('visibility'=>1))
		->addStoreFilter()
		->addVisibilityFilter()
		->setOrder('position','ASC')
		->setOrder('question_id','DESC');
		return $collection;
	}
}