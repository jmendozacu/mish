<?php
/**
 * Class VES_VendorsAttributeOptions_Model_Observer
 * Varien_Data_Form_Element_Fieldset
 */
class VES_VendorsAttributeOptions_Model_Observer
{
    public function ves_vendorsproduct_prepare_form(Varien_Event_Observer $ob) {
        $helper = Mage::helper('vendorsattributeoptions');
        $fieldset = $ob->getFieldset();
        $form = $fieldset->getForm();

        $attributes = $helper->getConfigAttributes();
        foreach($attributes as $attribute_code) {
            $element = $form->getElement($attribute_code);
            if($element) {
                $attribute_detail = Mage::getSingleton('eav/config')->getAttribute('catalog_product', $attribute_code);

                $element->setRenderer(Mage::app()->getLayout()->createBlock('vendorsattributeoptions/vendor_catalog_form_renderer_fieldset_element'))
                    ->setAttributeId($attribute_detail->getData('attribute_id'))
                    ->setAttributeCode($attribute_code);
            }
        }
    }
}