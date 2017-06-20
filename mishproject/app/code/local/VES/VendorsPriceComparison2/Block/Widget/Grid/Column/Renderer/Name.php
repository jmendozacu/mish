<?php

/**
 * Adminhtml grid item renderer
 *
 * @category   VES
 * @package    VES_Vendors
 * @author     Vnecoms Team <support@vnecoms.com>
 */

class VES_VendorsPriceComparison2_Block_Widget_Grid_Column_Renderer_Name
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        $product_id = $row->getProductId();

        $product = Mage::getModel("catalog/product")->load($product_id);
     
        return $product->getName();
    }


}
