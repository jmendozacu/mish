<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (http://www.amasty.com)
 * @package Amasty_SeoSingleUrl
 */

class Amasty_SeoSingleUrl_Model_Product_Url extends Mage_Catalog_Model_Product_Url
{
    public function getUrl(Mage_Catalog_Model_Product $product, $params = array())
    {
        $url = Mage::helper('amseourl/product_url_rewrite')->getProductPath($product);

        if (!$url)
            return parent::getUrl($product, $params);

        $params['_direct'] = $url;

        return rtrim(Mage::getUrl('', $params), '/');
    }
}
