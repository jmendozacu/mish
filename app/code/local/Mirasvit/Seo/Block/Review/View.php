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


class Mirasvit_Seo_Block_Review_View extends Mage_Review_Block_View
{
    public function getConfig()
    {
    	return Mage::getSingleton('seo/config');
    }

    public function getBackUrl()
    {
        if ($this->getConfig()->isEnabledReviewSeoUrls()) {
            $uri = $this->getProductData()->getUrlKey();

            return Mage::getUrl($uri.'/reviews');
        } else {
            return parent::getReviewsUrl();
        }
    }
}