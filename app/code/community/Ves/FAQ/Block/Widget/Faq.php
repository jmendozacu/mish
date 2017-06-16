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
class Ves_FAQ_Block_Widget_Faq extends Ves_FAQ_Block_Faq implements Mage_Widget_Block_Interface
{
	const XML_PATH_CFC_ENABLED_FAQ     = 'ves_faq/general_setting/enable';
	public function __construct($attributes = array())
	{
		if(!Mage::getStoreConfig(self::XML_PATH_CFC_ENABLED_FAQ))
			return;

		parent::__construct( $attributes );

		if(isset($attributes['template']) && $attributes['template']) {
			$my_template = $attributes['template'];
		} elseif ($this->hasData("template")) {
			$my_template = $this->getData("template");
		} else {
			$my_template = "ves/faq/latest.phtml";
		}

		$this->setTemplate($my_template);


	}

	public function _toHtml(){

		if(!$this->isCatalogMode() ) {
			return;
		}

		$data = array();
		$categories = $this->getConfig('category_id');
		$item_number = $this->getConfig('item_number');
		if($categories){
			$categories = explode(',', $categories);
			$questions = Mage::getModel('ves_faq/question')->getCollection()
			->addFieldToFilter('category_id', array('in'=>$categories))
			->setOrder('position','ASC')
			->setPageSize($item_number)
			->setCurPage(1);
			foreach ($questions as $_question) {
				$data[] = $_question;
			}
		}
		$this->assign('collection',$data);
		return parent::_toHtml();
	}

	protected function isCatalogMode() {
		if($catsids = $this->getConfig("catsid")) {
			$catsids = is_array($catsids)?$catsids:explode(",", $catsids);
			$category_id = array();
			$checked = false;

			if(Mage::registry('current_category')) {
				$category_id = Mage::registry('current_category')->getId();
				$category_id = array($category_id);
			} elseif($_product = Mage::registry('current_product')) {
				$categories = $_product->getCategoryCollection()
                          ->addAttributeToSelect('id');
		        foreach($categories as $category) {
		            $category_id[] = $category->getId();
		        }
			}

			if($category_id) {
				foreach($category_id as $val) {
					if(in_array($val, $catsids)) {
						$checked = true;
					}
				}
			}

			if(!$checked) 
				return false;
			
		}
		return true;
	}
}