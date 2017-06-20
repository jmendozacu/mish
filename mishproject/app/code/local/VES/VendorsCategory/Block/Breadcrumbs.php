<?php
 
/**
 * Catalog breadcrumbs
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class VES_VendorsCategory_Block_Breadcrumbs extends Mage_Core_Block_Template
{
    /**
     * Retrieve HTML title value separator (with space)
     *
     * @param mixed $store
     * @return string
     */
    public function getTitleSeparator($store = null)
    {
        $separator = (string)Mage::getStoreConfig('catalog/seo/title_separator', $store);
        return ' ' . $separator . ' ';
    }

    public function getCategory(){
    	return Mage::registry('current_vendor_category');
    }
    /**
     * Preparing layout
     *
     * @return VES_VendorsCategory_Block_Breadcrumbs
     */
    protected function _prepareLayout()
    {
        if ($breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs')) {
			/* $breadcrumbsBlock->addCrumb('home', array(
                'label'=>Mage::helper('catalog')->__('Home'),
                'title'=>Mage::helper('catalog')->__('Go to Home Page'),
                'link'=>Mage::getBaseUrl()
            ));*/
            //for vendor
            $vendorId = $this->getCategory()->getVendor()->getVendorId();
            $breadcrumbsBlock->addCrumb('vendor', array(
            		'label'=>Mage::registry('vendor')->getTitle(),
            		'title'=>Mage::registry('vendor')->getTitle(),
            		'link'=>Mage::helper('vendorspage')->getUrl($vendorId),
            ));

            $title = array();
            $path  = Mage::helper('vendorscategory')->getBreadcrumbPath();

            foreach ($path as $name => $breadcrumb) {
                $breadcrumbsBlock->addCrumb($name, $breadcrumb);
                $title[] = $breadcrumb['label'];
            }

            if ($headBlock = $this->getLayout()->getBlock('head')) {
                $headBlock->setTitle(join($this->getTitleSeparator(), array_reverse($title)).' - ' .Mage::registry('vendor')->getTitle());
            }
        }
        return parent::_prepareLayout();
    }
}
