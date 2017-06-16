<?php

/**
 * Adminhtml grid item renderer
 *
 * @category   VES
 * @package    VES_Vendors
 * @author     Vnecoms Team <support@vnecoms.com>
 */

class VES_VendorsSelectAndSell_Block_Widget_Grid_Column_Renderer_Text
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Text
{
    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        $vendor_id = $row->getVendorId();
        $vendor = Mage::getModel('vendors/vendor')->load($vendor_id);
        $text = '<span style="font-weight: bold;">'.$vendor->getVendorId().'</span>';
        return $text;
    }
}
