<?php
/**
 * Customer edit block
 *
 * @category   VES
 * @package    VES_Vendors
 * @author     Vnecoms Team <support@vnecoms.com>
 */
class VES_VendorsQuickAddProduct_Block_Vendor_Product_Edit_Tab_Settings extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Settings
{
    protected function _prepareLayout()
    {
        $this->setChild('continue_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('catalog')->__('Continue'),
                    'onclick'   => "vesContinue('".$this->getContinueUrl()."');",
                    'class'     => 'save',
                ))
        );
    }

    public function getContinueUrl()
    {
        return $this->getUrl('*/*/new', array(
            '_current'  => true,
            'set'       => '{{attribute_set}}',
            'type'      => '{{type}}',
            'ids' => '{{ids}}',
        ));
    }


    protected function _prepareForm(){
        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('settings', array('legend'=>Mage::helper('catalog')->__('Create Product Settings')));

        $entityType = Mage::registry('product')->getResource()->getEntityType();

        $fieldset->addField('attribute_set_id', 'hidden', array(
            'label' => Mage::helper('catalog')->__('Attribute Set'),
            'title' => Mage::helper('catalog')->__('Attribute Set'),
            'name'  => 'set',
            'value' => $entityType->getDefaultAttributeSetId(),
            'values'=> Mage::getResourceModel('eav/entity_attribute_set_collection')
                    ->setEntityTypeFilter($entityType->getId())
                    ->load()
                    ->toOptionArray()
        ));

        $fieldset->addField('ves_category_ids', 'hidden', array(
            'label' => Mage::helper('catalog')->__('ves_category_ids'),
            'title' => Mage::helper('catalog')->__('ves_category_ids'),
            'name'  => 'ids',
        ));

        $fieldset->addField('category_attribute', 'text', array(
            'label' => Mage::helper('vendorsqap')->__('Category'),
        ))->setRenderer($this->getLayout()->createBlock('vendorsqap/vendor_widget_form_renderer_fieldset_category'));



        $fieldset->addField('product_type', 'select', array(
            'label' => Mage::helper('catalog')->__('Product Type'),
            'title' => Mage::helper('catalog')->__('Product Type'),
            'name'  => 'type',
            'value' => '',
            'values'=> Mage::getModel('vendorsproduct/source_type')->getVendorOptionArray()
        ));

        $fieldset->addField('continue_button', 'note', array(
            'text' => $this->getChildHtml('continue_button'),
        ));

        $this->setForm($form);
    }
}
