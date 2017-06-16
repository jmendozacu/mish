<?php

$installer = $this;
/* Add related attribute for product */
$catalogEavSetup = new Mage_Catalog_Model_Resource_Eav_Mysql4_Setup();
$idAttribute = $installer->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'vendor_parent_product' , 'attribute_id');
if(!$idAttribute){
    $catalogEavSetup->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'vendor_parent_product', array(
        'group' => 'General',
        'sort_order' => 20,
        'type' => 'int',
        'backend' => '',
        'frontend' => '',
        'label' => 'Related to product',
        'note' => 'Related to product',
        'input' => 'hidden',
        'class' => '',
        'source' => '',
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        'visible' => false,
        'required' => false,
        'user_defined' => false,
        'default' => '',
        'visible_on_front' => false,
        'unique' => false,
        'is_configurable' => false,
        'used_for_promo_rules' => false
    ));
}

$idAttribute = $installer->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'ves_enable_comparison' , 'attribute_id');
if(!$idAttribute){
    $catalogEavSetup->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'ves_enable_comparison', array(
        'group' => 'General',
        'sort_order' => 20,
        'type' => 'int',
        'backend' => '',
        'frontend' => '',
        'label' => 'Enable For Price Comparison',
        'input' => 'select',
        'class' => '',
        'source' => 'eav/entity_attribute_source_boolean',
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        'visible' => true,
        'required' => false,
        'user_defined' => false,
        'default' => '',
        'visible_on_front' => false,
        'unique' => false,
        'is_configurable' => false,
        'used_for_promo_rules' => false
    ));
}


/*Pre set value for vendor_parent_product*/
$productCollection = Mage::getModel('catalog/product')->getCollection()
    ->addAttributeToSelect('vendor_parent_product');
foreach ($productCollection as $product){
    if(!$product->getData('vendor_parent_product')) $product->setData('vendor_parent_product',0)->getResource()->saveAttribute($product, 'vendor_parent_product');
}