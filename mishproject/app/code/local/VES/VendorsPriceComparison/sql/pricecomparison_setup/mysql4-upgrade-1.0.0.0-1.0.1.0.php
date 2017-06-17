<?php

$installer = $this;
/* Add related attribute for product */
$catalogEavSetup = new Mage_Catalog_Model_Resource_Eav_Mysql4_Setup();
try{
$catalogEavSetup->removeAttribute(Mage_Catalog_Model_Product::ENTITY, 'vendor_parent_product');
$catalogEavSetup->removeAttribute(Mage_Catalog_Model_Product::ENTITY, 'ves_enable_comparison');
$catalogEavSetup->removeAttribute(Mage_Catalog_Model_Product::ENTITY, 'vendor_relation_key');
}catch (Exception $e){
	
}
$catalogEavSetup->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'vendor_relation_key', array(
	'group' => 'Prices',
	'sort_order' => 30,
	'type' => 'varchar',
	'backend' => '',
	'frontend' => '',
	'label' => 'Relation key',
	'note' => 'This relation key is used for price comparison.',
	'input' => 'text',
	'class' => '',
	'source' => '',
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

$catalogEavSetup->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'vendor_child_product', array(
	'group' => 'Prices',
	'sort_order' => 31,
	'type' => 'int',
	'backend' => '',
	'frontend' => '',
	'label' => 'Is Child Product',
	'note' => 'This option is used for price comparison.',
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
$installer->endSetup(); 