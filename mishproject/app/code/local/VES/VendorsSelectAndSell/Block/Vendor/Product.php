<?php

/**
 * Catalog manage products block
 *
 * @category   VES
 * @package    VES_Vendors
 * @author     Vnecoms Team <core@magentocommerce.com>
 */
class VES_VendorsSelectAndSell_Block_Vendor_Product extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Set template
     */
    public function __construct()
    {
        $this->_controller = 'vendor_product';
        $this->_blockGroup = 'selectandsell';
        $this->_headerText = Mage::helper('selectandsell')->__('Select and Sell');

        parent::__construct();

        $this->_removeButton('add');
    }
	/**
     * Prepare button and grid
     *
     * @return Mage_Adminhtml_Block_Catalog_Product
     */
    protected function _prepareLayout()
    {
		parent::_prepareLayout();
		$this->setChild('grid', $this->getLayout()->createBlock('selectandsell/vendor_product_grid', 'vendor.selectandsell.product.grid'));
    }
}
