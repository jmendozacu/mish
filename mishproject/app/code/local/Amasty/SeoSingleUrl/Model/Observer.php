<?php

class Amasty_SeoSingleUrl_Model_Observer
{
	/**
	 * @param $observer
	 */
	public function addUrlRewriteToSitemap(Varien_Event_Observer $observer)
	{
		$block = $observer->getBlock();
		if ($block instanceof Mage_Catalog_Block_Seo_Sitemap_Product) {
			$block->getCollection()->addUrlRewrite();
		}
	}

    /**
     * @param $observer
     */
    public function addUrlRewriteToUpSell(Varien_Event_Observer $observer)
    {
        $observer->getCollection()->addUrlRewrite();
    }
}
