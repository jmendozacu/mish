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


class Mirasvit_Seo_Block_Breadcrumbs extends Mage_Page_Block_Html_Breadcrumbs
{
    public function getConfig()
    {
        return Mage::getSingleton('seo/config');
    }

    function __construct()
    {
        parent::__construct();
        if (!$this->getBreadcrumbsSeparator()) {
            return;
        }
        if (Mage::registry('current_category')
            || Mage::registry('current_product')) {
            $this->setTemplate('seo/breadcrumbs.phtml');
            $this->_prepareBreadcrumbs();
        }
    }

    protected function _prepareBreadcrumbs()
    {
        $this->addCrumb('home', array(
                'label'=>Mage::helper('catalog')->__('Home'),
                'title'=>Mage::helper('catalog')->__('Go to Home Page'),
                'link'=>Mage::getBaseUrl()
            ));
        $path  = Mage::helper('seo/breadcrumbs')->getBreadcrumbPath();
        foreach ($path as $name => $breadcrumb) {
            $this->addCrumb($name, $breadcrumb);
        }
    }

    public function getBreadcrumbsSeparator()
    {
        return $this->getConfig()->getBreadcrumbsSeparator(Mage::app()->getStore()->getId());
    }

}
