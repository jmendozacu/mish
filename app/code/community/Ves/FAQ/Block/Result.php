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
class Ves_FAQ_Block_Result extends Mage_Core_Block_Template
{
	protected $_collection = array();
	/**
	 * Contructor
	 */
	public function __construct($attributes = array()){
		if(!Mage::getStoreConfig('ves_faq/general_setting/enable'))
			return;

		parent::__construct();

		if(isset($attributes['template']) && $attributes['template']) {
			$my_template = $attributes['template'];
		} elseif ($this->hasData("template")) {
			$my_template = $this->getData("template");
		} else {
			$my_template = "ves/faq/result.phtml";
		}

		$this->setTemplate($my_template);

		$collection = '';
		$keywords = $this->getKeyWords();
		$collection = $this->getQuestionByKeyWords($keywords);
		$this->setCollection($collection);
	}

	//prepare layout
	protected function _prepareLayout()
	{
		$keywords = $this->getKeyWords();
		$keywords = $this->__('Search Results for: ').$keywords;
		$this->getLayout()->getBlock('head')->setTitle($keywords);

		$pager = $this->getLayout()->createBlock('page/html_pager', 'faq.question.result.pager');
		$pager->setAvailableLimit(array(5=>5,10=>10,20=>20,'all'=>'all'));
		$pager->setCollection($this->getCollection());
		$this->setChild('pager', $pager);
		$this->getCollection()->load();

		return parent::_prepareLayout();
	}

	public function getPagerHtml(){
		return $this->getChildHtml('pager');
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

	public function _toHtml(){
		return parent::_toHtml();
	}

	public function getKeyWords(){
		$keywords = $this->getRequest()->getParam('s');
		return $keywords;
	}

	public function getQuestionByKeyWords($key){
		$collection = Mage::getModel('ves_faq/question')->getCollection()
		->addFieldToFilter('status', array('eq'=>1))
		->addFieldToFilter('title',array('like' => '%'.$key.'%'))
		->addVisibilityFilter()
		->addStoreFilter()
		->setOrder('position','ASC')
		->setOrder('question_id','DESC');
		$this->_collection = $collection;
		return $collection;
	}

}