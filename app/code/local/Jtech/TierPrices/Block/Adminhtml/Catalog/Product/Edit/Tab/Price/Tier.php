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
class Jtech_TierPrices_Block_Adminhtml_Catalog_Product_Edit_Tab_Price_Tier
    extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Price_Tier
{
    public function __construct()
    {
        $this->setTemplate('tierprices/catalog/product/edit/price/tier.phtml');
    }
}
