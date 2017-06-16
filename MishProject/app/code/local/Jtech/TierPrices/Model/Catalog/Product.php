<?php
/************************************************************************
 * 
 * jtechextensions @ J-Tech LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.jtechextensions.com/LICENSE-M1.txt
 *
 * @package    Tiered Pricing By Percent
 * @copyright  Copyright (c) 2012-2013 jtechextensions @ J-Tech LLC. (http://www.jtechextensions.com)
 * @license    http://www.jtechextensions.com/LICENSE-M1.txt
************************************************************************/
class Jtech_TierPrices_Model_Catalog_Product extends Mage_Catalog_Model_Product
{

    protected function _construct()
    {
        $this->_init('catalog/product');
    }
	
    public function getMinimalPrice()
    {
		
		$productModel = Mage::getModel('catalog/product')->load($this->getId());
        $prices = $productModel->getData('tier_price');
		
		
        if (is_null($prices))
        {
            $attribute = $productModel->getResource()->getAttribute('tier_price');
            if ($attribute)
            {
                $attribute->getBackend()->afterLoad($productModel);
                $prices = $productModel->getData('tier_price');
            }
        }
		
        if (is_null($prices) || !is_array($prices))
        {
			return max($this->_getData('minimal_price'), 0);
        }
		foreach ($prices as $i => $price)
		{	
			if ($price['value_type'] == 'discount_percent')
			{
				$price = $productModel->getPrice() - (($price['website_price'] * $productModel->getPrice()) / 100);
			}
			if ($price['value_type'] == 'fixed')
			{
				$price = max($this->_getData('minimal_price'), 0);
			}
		}
		
        return max($price, 0);
    }
}