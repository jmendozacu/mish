<?php

/**
 * Catalog manage products block
 *
 * @category   VES
 * @package    VES_Vendors
 * @author     Vnecoms Team <core@magentocommerce.com>
 */
class VES_VendorsPriceComparison2_Block_Vendor_Product extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Set template
     */
    public function __construct()
    {
        $this->_controller = 'vendor_product';
        $this->_blockGroup = 'pricecomparison2';
        $this->_headerText = Mage::helper('pricecomparison2')->__('Manage Assigned Products');

        $this->_addButtonLabel = Mage::helper('pricecomparison2')->__('Select And Sell');
        parent::__construct();
    }
    
    /**
     * Get Select And Sell URL
     */
    public function getCreateUrl(){
        return Mage::getUrl('*/pricecomparison/selectandsell');
    }
}
