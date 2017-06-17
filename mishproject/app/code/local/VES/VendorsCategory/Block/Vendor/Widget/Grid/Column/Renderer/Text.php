<?php

/**
 * Adminhtml grid item renderer
 *
 * @category   VES
 * @package    VES_Vendors
 * @author     Vnecoms Team <support@vnecoms.com>
 */

class VES_VendorsCategory_Block_Vendor_Widget_Grid_Column_Renderer_Text
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
    	$level = $row->getLevel();
        $name = parent::render($row);
        for($i=0;$i<$level;$i++) {
        	$result .= '----';
        }
        $result .= ' ' . $name;
        return $result;
    }
}
