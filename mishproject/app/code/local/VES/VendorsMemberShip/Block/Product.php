<?php
/**
 * Created by PhpStorm.
 * User: namnh_000
 * Date: 6/10/14
 * Time: 10:02 PM
 */

class VES_VendorsMemberShip_Block_Product extends Mage_Core_Block_Template {
	protected function _toHtml(){
		if(!Mage::registry('product')->getData('ves_vendor_related_group')) return '';
		return parent::_toHtml();
	}
} 