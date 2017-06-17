<?php

class VES_VendorsAttributeOptions_Block_Vendor_Catalog_Form_Renderer_Fieldset_Element extends Mage_Adminhtml_Block_Template
    implements Varien_Data_Form_Element_Renderer_Interface
{
    protected $_element;

    protected function _construct()
    {
        $this->setTemplate('ves_vendorsattributeoptions/catalog/form/renderer/fieldset/element.phtml');
    }

    protected function _prepareLayout()
    {
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'label' => Mage::helper('vendorsattributeoptions')->__('Add option'),
                'onclick' => '',
                'class' => 'add'
            ));
        $button->setName('add_attribute_option_item_button');

        $this->setChild('add_button', $button);
        return parent::_prepareLayout();
    }

    public function getElement()
    {
        return $this->_element;
    }

    public function getAttributeId() {
        return $this->getElement()->getAttributeId();
    }

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $this->_element = $element;
        return $this->toHtml();
    }

    /**
     * Retrieve data object related with form
     *
     * @return Mage_Catalog_Model_Product || Mage_Catalog_Model_Category
     */
    public function getDataObject()
    {
        return $this->getElement()->getForm()->getDataObject();
    }

    /**
     * Retireve associated with element attribute object
     *
     * @return Mage_Catalog_Model_Resource_Eav_Attribute
     */
    public function getAttribute()
    {
        return $this->getElement()->getEntityAttribute();
    }

    /**
     * Retrieve associated attribute code
     *
     * @return string
     */
    public function getAttributeCode()
    {
        return $this->getAttribute()->getAttributeCode();
    }

    /**
     * Check "Use default" checkbox display availability
     *
     * @return bool
     */
    public function canDisplayUseDefault()
    {
        if ($attribute = $this->getAttribute()) {
            if (!$attribute->isScopeGlobal()
                && $this->getDataObject()
                && $this->getDataObject()->getId()
                && $this->getDataObject()->getStoreId()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check default value usage fact
     *
     * @return bool
     */
    public function usedDefault()
    {
        $attributeCode = $this->getAttribute()->getAttributeCode();
        $defaultValue = $this->getDataObject()->getAttributeDefaultValue($attributeCode);

        if (!$this->getDataObject()->getExistsStoreValueFlag($attributeCode)) {
            return true;
        } else if ($this->getElement()->getValue() == $defaultValue &&
            $this->getDataObject()->getStoreId() != $this->_getDefaultStoreId()
        ) {
            return false;
        }
        if ($defaultValue === false && !$this->getAttribute()->getIsRequired() && $this->getElement()->getValue()) {
            return false;
        }
        return $defaultValue === false;
    }

    /**
     * Disable field in default value using case
     *
     * @return Mage_Adminhtml_Block_Catalog_Form_Renderer_Fieldset_Element
     */
    public function checkFieldDisable()
    {
        if ($this->canDisplayUseDefault() && $this->usedDefault()) {
            $this->getElement()->setDisabled(true);
        }
        return $this;
    }

    /**
     * Retrieve label of attribute scope
     *
     * GLOBAL | WEBSITE | STORE
     *
     * @return string
     */
    public function getScopeLabel()
    {
        $html = '';
        $attribute = $this->getElement()->getEntityAttribute();
        if (!$attribute || Mage::app()->isSingleStoreMode() || $attribute->getFrontendInput()=='gallery') {
            return $html;
        }

        /*
         * Check if the current attribute is a 'price' attribute. If yes, check
         * the config setting 'Catalog Price Scope' and modify the scope label.
         */
        $isGlobalPriceScope = false;
        if ($attribute->getFrontendInput() == 'price') {
            $priceScope = Mage::getStoreConfig('catalog/price/scope');
            if ($priceScope == 0) {
                $isGlobalPriceScope = true;
            }
        }

        if ($attribute->isScopeGlobal() || $isGlobalPriceScope) {
            $html .= Mage::helper('adminhtml')->__('[GLOBAL]');
        } elseif ($attribute->isScopeWebsite()) {
            $html .= Mage::helper('adminhtml')->__('[WEBSITE]');
        } elseif ($attribute->isScopeStore()) {
            $html .= Mage::helper('adminhtml')->__('[STORE VIEW]');
        }

        return $html;
    }

    /**
     * Retrieve element label html
     *
     * @return string
     */
    public function getElementLabelHtml()
    {
        $element = $this->getElement();
        $label = $element->getLabel();
        if (!empty($label)) {
            $element->setLabel($this->__($label));
        }
        return $element->getLabelHtml();
    }

    /**
     * Retrieve element html
     *
     * @return string
     */
    public function getElementHtml()
    {
        return $this->getElement()->getElementHtml();
    }

    /**
     * Default sore ID getter
     *
     * @return integer
     */
    protected function _getDefaultStoreId()
    {
        return Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID;
    }

    public function getAddButtonHtml()
    {
        return $this->getChildHtml('add_button');
    }

    public function getSaveUrl() {
        return $this->getUrl('vendors/options_ajax/save');
    }
}
