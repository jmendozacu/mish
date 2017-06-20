<?php

/**
 * Catalog breadcrumbs
 *
 * @category   VES
 * @package    VES_Vendors
 * @author     Vnecoms Team <core@magentocommerce.com>
 */
class VES_VendorsPage_Block_Catalog_Product_Breadcrumbs extends Mage_Core_Block_Template
{
    protected function _prepareLayout()
    {
        if ($breadcrumbsBlock = $this->getLayout()->getBlock('vendor.product.breadcrumbs')) {
            $breadcrumbsBlock->addCrumb('vendor_home', array(
                'label'	=>Mage::registry('vendor')->getTitle(),
                'title'	=>Mage::registry('vendor')->getTitle(),
                'link'	=>Mage::helper('vendorspage')->getUrl(Mage::registry('vendor')),
            ));
            $breadcrumbsBlock->addCrumb('product', array(
                'label'	=>Mage::registry('product')->getName(),
                'title'	=>Mage::registry('product')->getName(),
            ));
        	if ($headBlock = $this->getLayout()->getBlock('head')) {
                $headBlock->setTitle(Mage::registry('product')->getName().' - '.Mage::registry('vendor')->getTitle());
            }
        }
        return parent::_prepareLayout();
    }
}
