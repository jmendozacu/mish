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
class Ves_FAQ_Block_Questions extends Mage_Core_Block_Template
{
	/**
	 * Question collection
	 *
	 * @var Ves_FAQ_Model_Mysql4_Question_Collection
	 */
	protected $_collection = null;

	/**
     * Initialize Questions template
     *
     */
	public function __construct(){
		parent::__construct();
		$this->setTemplate('ves/faq/template/questions.phtml');
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
	 * Get value of the extension's configuration
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
		}

		if($return == "" && $default) {
			$return = $default;
		}
		return $return;
	}
}