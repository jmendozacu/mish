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
class Ves_FAQ_Block_Category extends Mage_Core_Block_Template
{
	protected $_config = '';
	protected $_attributes = array();
	const XML_PATH_CFC_ENABLED_FAQ     = 'ves_faq/general_setting/enable';
	/**
	 * Contructor
	 */
	public function __construct($attributes = array()){
		if(!Mage::getStoreConfig(self::XML_PATH_CFC_ENABLED_FAQ))
			return;

		$this->_attributes = $attributes;
		$helper =  Mage::helper('ves_faq/data');
		$this->_config = $helper->get($attributes);
		$this->convertAttributesToConfig($this->_config);

		parent::__construct($attributes);

		if(!$this->getConfig("show_category", "faq_page", 1)) {
			return;
		}
		if(isset($attributes['template']) && $attributes['template']) {
			$my_template = $attributes['template'];
		} elseif ($this->hasData("template")) {
			$my_template = $this->getData("template");
		} else {
			$my_template = "ves/faq/category.phtml";
		}

		$this->setTemplate($my_template);

	}

	public function _toHtml(){
		if(!$this->getConfig("show_category", "faq_page", 1)) {
			return;
		}
		$categories = $this->getListCategory();
		$this->assign('title', $this->getConfig("category_block_title", "faq_page"));
		$this->assign('categories',$categories);
		return parent::_toHtml();
	}

	/**
	 * Retrive list category
	 */
	public function getListCategory(){
		$collection = Mage::getSingleton('ves_faq/category')->getCollection()
		->addFieldToFilter('status', array('eq'=>1))
		->addFieldToFilter('include_in_sidebar', array('eq'=>1))
		->addStoreFilter()
		->setOrder('position','ASC');
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
	 * @return string
	 */
	public function getConfig( $key, $panel='faq_page', $default = ""){
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
				$return = Mage::getStoreConfig("ves_faq/$panel/$key");
			}
			if($return == "" && !$default) {
				$return = $default;
			}

		}

		return $return;
	}

}