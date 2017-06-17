<?php
/**
 * Product categories tab
 *
 * @category   VES
 * @package    VES_Vendors
 * @author     Vnecoms Team <support@vnecoms.com>
 */
class VES_VendorsCategory_Block_Vendor_Product_Edit_Tab_Categories1 extends VES_VendorsCategory_Block_Vendor_Category_Tree
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('ves_vendorscategory/vendor/product/edit/categories.phtml');
    }

    public function getProduct()
    {
        return Mage::registry('current_product');
    }

    public function isReadonly()
    {
        return false;
    }

    public function getIdsString()
    {
        Mage::log($this->getProduct()->getData('vendor_categories'));
        return $this->getProduct()->getData('vendor_categories');
    }
}
