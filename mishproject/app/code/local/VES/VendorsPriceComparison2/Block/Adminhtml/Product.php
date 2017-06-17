<?php

/**
 * Catalog manage products block
 *
 * @category   VES
 * @package    VES_Vendors
 * @author     Vnecoms Team <core@magentocommerce.com>
 */
class VES_VendorsPriceComparison2_Block_Adminhtml_Product extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Set template
     */
    public function __construct()
    {
        $this->_controller = 'adminhtml_product';
        $this->_blockGroup = 'pricecomparison2';
        $this->_headerText = Mage::helper('pricecomparison2')->__('Manage Assigned Products');

        parent::__construct();
        $this->_removeButton('add');
    }
}
