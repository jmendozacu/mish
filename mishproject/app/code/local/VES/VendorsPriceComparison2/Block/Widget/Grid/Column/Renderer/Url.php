<?php

/**
 * Adminhtml grid item renderer
 *
 * @category   VES
 * @package    VES_Vendors
 * @author     Vnecoms Team <support@vnecoms.com>
 */

class VES_VendorsPriceComparison2_Block_Widget_Grid_Column_Renderer_Url
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    protected $_selling_product_ids;
    
    public function setSellingProductIds($sellingProductIds){
        $this->_selling_product_ids = $sellingProductIds;
    }
    
    public function getSellingProductIds(){
        return $this->_selling_product_ids;
    }
    
    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        $product_id = $row->getEntityId();

        $sellUrl = Mage::getUrl('vendors/pricecomparison/add',array('id'=>$product_id));
        if($this->isAlreadySoldProduct($product_id)){
            $html = '<div style="margin: 20px 0px; line-height: 35px;font-weight: bold;color: #a0a0a0;">'.$this->__('You are selling this already.').'</div>';
        }else{
            $html = '<button style="margin:20px 0;" onclick="setLocation(\''.$sellUrl.'\')" class="scalable add" type="button" title="'.$this->__('Sell This Product').'"><span><span>'.$this->__('Sell This Product').'</span></span></button>';
        }
        return $html;
    }

    public function isAlreadySoldProduct($productId){
        return in_array($productId, $this->_selling_product_ids);
    }
}
