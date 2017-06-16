<?php
class VES_VendorsAttributeOptions_Model_Source_Attributes {
    public function getAllOptions()
    {
        if (is_null($this->_options)) {
            $this->_options = array();
            $notAllowAttributes = Mage::helper('vendorsattributeoptions')->getNotAllowProductAttributes();
            $attributes = Mage::getResourceModel('catalog/product_attribute_collection')->getItems();

            foreach ($attributes as $attribute) {
                if(in_array($attribute->getAttributeCode(), $notAllowAttributes)) continue;

                if($attribute->getData('frontend_input') == 'select'
                 || $attribute->getData('frontend_input') == 'multiselect') {
                    $this->_options[] = array(
                        'value'         =>  $attribute->getData('attribute_code'),
                        'label'         =>  Mage::helper('vendorsattributeoptions')->__($attribute->getData('frontend_label')),
                    );
                }
            }
        }
        return $this->_options;
    }


    public function toOptionArray()
    {
        return $this->getAllOptions();
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $options = array();
        $notAllowAttributes = Mage::helper('vendorsattributeoptions')->getNotAllowProductAttributes();
        $attributes = Mage::getResourceModel('catalog/product_attribute_collection')->getItems();

        foreach ($attributes as $attribute) {
            if(in_array($attribute->getAttributeCode(), $notAllowAttributes)) continue;

            if($attribute->getData('frontend_input') == 'select'
                || $attribute->getData('frontend_input') == 'multiselect') {
                $options[$attribute->getData('attribute_code')] = Mage::helper('vendorsattributeoptions')->__($attribute->getName());
            }
        }

        return $options;
    }
}