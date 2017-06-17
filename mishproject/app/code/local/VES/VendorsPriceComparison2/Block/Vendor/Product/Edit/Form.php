<?php
/**
 *  edit block
 *
 * @category   VES
 * @package    VES_VendorsPriceComparison2
 * @author     Vnecoms Team <support@vnecoms.com>
 */
class VES_VendorsPriceComparison2_Block_Vendor_Product_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{

    /**
     * Init form
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('block_form');
        $this->setTitle(Mage::helper('pricecomparison2')->__('Select and Sell'));
    }

    /**
     * Load Wysiwyg on demand and Prepare layout
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
    }

    protected function _prepareForm()
    {
        $model = Mage::registry('price_comparison');
        $product = $model->getProduct();
        
        $form = new Varien_Data_Form(
            array('id' => 'edit_form', 'action' => $this->getUrl('vendors/pricecomparison/save',array('id'=>$model->getId())), 'method' => 'post')
        );

        $form->setHtmlIdPrefix('product_');

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('pricecomparison2')->__('General Information'), 'class' => 'fieldset-wide'));
        if(Mage::helper('pricecomparison2')->isEnabledCondition()){
            $field =$fieldset->addField('condition', 'select', array(
                'name'      => 'condition',
                'label'     => Mage::helper('pricecomparison2')->__('Condition'),
                'title'     => Mage::helper('pricecomparison2')->__('Condition'),
                'required'  => true,
                'values'    => Mage::getModel('pricecomparison2/source_condition')->toOptionArray(),
            ));
        }
        if($product->getTypeId() != Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE){
            /*Do not show these fields if the product is configurable product*/
            $fieldset->addField('price', 'text', array(
                'name'      => 'price',
                'label'     => Mage::helper('pricecomparison2')->__('Price'),
                'title'     => Mage::helper('pricecomparison2')->__('Price'),
                'class'     => 'validate-number',
                'required'  => true,
            ));
            
            $fieldset->addField('qty', 'text', array(
                'name'      => 'qty',
                'label'     => Mage::helper('pricecomparison2')->__('Qty'),
                'title'     => Mage::helper('pricecomparison2')->__('Qty'),
                'class'     => 'validate-number',
                'required'  => true,
            ));
        }
        $fieldset->addField('description', 'textarea', array(
            'name'      => 'description',
            'label'     => Mage::helper('pricecomparison2')->__('Product short description'),
            'title'     => Mage::helper('pricecomparison2')->__('Product short description'),
            'required'  => true,
            'wysiwyg'   => false,
        ));
        
        
        if($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE){
            $optionFieldSet = $form->addFieldset('option_fieldset', array('legend'=>Mage::helper('pricecomparison2')->__('Product Options'), 'class' => 'fieldset-wide'));
            $optionFieldSet->addType('associated_option','VES_VendorsPriceComparison2_Block_Vendor_Selectandsell_Edit_Form_Element_Option');
            $optionFieldSet->addField('associated_products', 'associated_option', array(
                'name'      => 'qty',
                'label'     => '',
                'title'     => '',
                'class'     => 'validate-number',
                'required'  => true,
                'product' => $product,
                'pricecomparison' => $model,
            ));
        }
        
        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

}
