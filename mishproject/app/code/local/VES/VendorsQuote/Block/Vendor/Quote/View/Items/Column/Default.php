<?php
class VES_VendorsQuote_Block_Vendor_Quote_View_Items_Column_Default extends Mage_Adminhtml_Block_Template
{
    public function getItem()
    {
        return $this->_getData('item');
    }


    public function getSku()
    {
        /*if ($this->getItem()->getProductType() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
            return $this->getItem()->getProductOptionByCode('simple_sku');
        }*/
        return $this->getItem()->getSku();
    }

}
