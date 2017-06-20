<?php

/**
 * Adminhtml grid item renderer
 *
 * @category   VES
 * @package    VES_Vendors
 * @author     Vnecoms Team <support@vnecoms.com>
 */

class VES_VendorsPriceComparison2_Block_Widget_Grid_Column_Renderer_Preview
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    protected $_store;
    
    /**
     * Set store object
     * @param Mage_Core_Model_Store $store
     */
    public function setStore(Mage_Core_Model_Store $store){
        $this->_store = $store;
        return $this;
    }
    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        $productId = $row->getProductId();
        $product = Mage::getModel('catalog/product')->load($productId);
        $product->setStoreId($this->getStore());
        $html = Mage::helper('pricecomparison2')->__('%sPreview%s',sprintf('<a target="_blank" href="%s">',$product->getProductUrl()),'</a>');
        return $html;
    }

}
