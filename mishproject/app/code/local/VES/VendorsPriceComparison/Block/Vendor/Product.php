<?php

/**
 * Catalog manage products block
 *
 * @category   VES
 * @package    VES_Vendors
 * @author     Vnecoms Team <core@magentocommerce.com>
 */
class VES_VendorsPriceComparison_Block_Vendor_Product extends Mage_Adminhtml_Block_Catalog_Product
{
    /**
     * Set template
     */
    public function __construct()
    {
        parent::__construct();
    }
	/**
     * Prepare button and grid
     *
     * @return Mage_Adminhtml_Block_Catalog_Product
     */
    protected function _prepareLayout()
    {
		parent::_prepareLayout();
		$this->_removeButton('add_new');
		$this->setChild('grid', $this->getLayout()->createBlock('pricecomparison/vendor_product_grid', 'vendor.product.grid'));
    }
}
