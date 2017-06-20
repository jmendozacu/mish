<?php

/**
 * Adminhtml grid item renderer
 *
 * @category   VES
 * @package    VES_Vendors
 * @author     Vnecoms Team <support@vnecoms.com>
 */

class VES_VendorsReview_Block_Widget_Grid_Column_Renderer_Text
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
        $vendor_id = parent::render($row);
        $text = Mage::getModel('vendors/vendor')->load($vendor_id)->getUsername();
        $text = '<span style="font-weight: bold;">'.$text.'</span>';
        return $text;
    }
}
