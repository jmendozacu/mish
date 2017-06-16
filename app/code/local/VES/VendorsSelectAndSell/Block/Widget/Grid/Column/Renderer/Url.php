<?php

/**
 * Adminhtml grid item renderer
 *
 * @category   VES
 * @package    VES_Vendors
 * @author     Vnecoms Team <support@vnecoms.com>
 */

class VES_VendorsSelectAndSell_Block_Widget_Grid_Column_Renderer_Url
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
        $product_id = $row->getEntityId();
        $product = Mage::getModel('catalog/product')->load($product_id);

        $sell_url = Mage::getUrl('vendors/vendor_selectandsell/load',array('product_id'=>$product_id));

        $text = '<a style="" href="'.$sell_url.'"><span style="border:1px solid green;background:green; padding:5px 0;
        color:white;display:inline-block;line-height:14px;font-size:12px;font-weight:bold;font-family:Arial,sans-serif;
         width:115px;text-align:center;text-shadow:0 -1px 0 rgba(0, 0, 0, 0.25);">'.$this->__('Sell This Product').'</span></a>';

        return $text;
    }

    public function isAlreadySellTheProduct($product){
		if(Mage::helper('catalog/product_flat')->isEnabled()) {
            $emulationModel = Mage::getModel('core/app_emulation');
            $init = $emulationModel->startEnvironmentEmulation(0, Mage_Core_Model_App_Area::AREA_ADMINHTML);
        }
        $products = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('vendor_related_product')
            ->addAttributeToFilter('vendor_related_product',$product->getId());
			
		if(Mage::helper('catalog/product_flat')->isEnabled()) {
            $emulationModel->stopEnvironmentEmulation($init);
        }
        $product->load();
        return $products->count();
    }
}
