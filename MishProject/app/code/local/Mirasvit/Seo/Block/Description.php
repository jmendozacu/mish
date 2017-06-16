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


/**
 * ÐÐ»Ð¾Ðº Ð´Ð»Ñ Ð²ÑÐ²Ð¾Ð´Ð° SEO Ð¾Ð¿Ð¸ÑÐ°Ð½Ð¸Ñ Ð² ÑÑÑÐµÑÐµ Ð¼Ð°Ð³Ð°Ð·Ð¸Ð½Ð°
 */
class Mirasvit_Seo_Block_Description extends Mage_Core_Block_Template
{
    public function getDescription() {
        return Mage::helper('seo')->getCurrentSeo()->getDescription();
    }

}
