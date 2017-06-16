<?php

class VES_VendorsQuote_Block_Email_Quote_Item_Renderer_Configurable extends VES_VendorsQuote_Block_Email_Quote_Item_Renderer{
    protected $_usedAttributes = '_cache_instance_used_attributes';
    
    /**
     * Retrieves product configuration options
     *
     * @param Mage_Catalog_Model_Product_Configuration_Item_Interface $item
     * @return array
     */
    public function getProductOptions()
    {
        $item    = $this->getItem();
        $product = $item->getProduct();
        $buyRequest = json_decode($item->getBuyRequest(),true);
        $attributes = array();
        if (isset($buyRequest['super_attribute']) &&($data = $buyRequest['super_attribute'])) {
            $typeInstance = $product->getTypeInstance(true);
            $usedProductAttributeIds = $typeInstance->getUsedProductAttributeIds($product);
            $usedAttributes = $typeInstance->getProduct($product)->getData($this->_usedAttributes);
            
            foreach ($data as $attributeId => $attributeValue) {
                if (isset($usedAttributes[$attributeId])) {
                    $attribute = $usedAttributes[$attributeId];
                    $label = $attribute->getLabel();
                    $value = $attribute->getProductAttribute();
                    if ($value->getSourceModel()) {
                        $value = $value->getSource()->getOptionText($attributeValue);
                    }
                    else {
                        $value = '';
                    }

                    $attributes[] = array('label'=>$label, 'value'=>$value);
                }
            }
        }
        $options = parent::getProductOptions();
        return array_merge($attributes, $options);
    }
    
}