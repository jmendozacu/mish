<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Advanced SEO Suite
 * @version   1.1.0
 * @revision  551
 * @copyright Copyright (C) 2014 Mirasvit (http://mirasvit.com/)
 */


$installer = $this;
$installer->startSetup();

Mage::helper('mstcore')->copyConfigData('seo/general/trailing_slash', 'seo/url/trailing_slash');
function mst_migrate_noindex_pages($oldData) {
    $pages = $oldData;
    $pages = explode("\n", trim($pages));
    $pages = array_map('trim',$pages);
	$newPages = array();
	foreach ($pages as $url) {
		$newPages[] = array(
			'pattern' => $url,
			'options' => Mirasvit_Seo_Model_Config::NOINDEX_NOFOLLOW
		);
	}
	return serialize($newPages);
}

Mage::helper('mstcore')->copyConfigData('seo/general/noindex_pages', 'seo/general/noindex_pages2', 'mst_migrate_noindex_pages');
Mage::helper('mstcore')->copyConfigData('seo/general/product_url_format', 'seo/url/product_url_format');
Mage::helper('mstcore')->copyConfigData('seo/general/product_url_key', 'seo/url/product_url_key');

$installer->endSetup();

Mage::getSingleton('core/config')->cleanCache();