<?php
/**
 *  edit block
 *
 * @category   VES
 * @package    VES_VendorsPriceComparison2
 * @author     Vnecoms Team <support@vnecoms.com>
 */
class VES_VendorsPriceComparison2_Block_Vendor_Product_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'product_id';
        $this->_controller = 'vendor_product';
        $this->_blockGroup = 'pricecomparison2';
    
        parent::__construct();
        $this->_removeButton('delete');
        $this->_updateButton('save', 'label', Mage::helper('pricecomparison2')->__('Save'));
        $this->_formScripts[] = "

        ";
    }
    
    /**
     * Get edit form container header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('product')->getId()) {
            return Mage::helper('pricecomparison2')->__("Select and Sell Product: '%s'", $this->escapeHtml(Mage::registry('product')->getName()));
        }
    }
    
    /**
     * Get URL for back (reset) button
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/*/index');
    }
}
