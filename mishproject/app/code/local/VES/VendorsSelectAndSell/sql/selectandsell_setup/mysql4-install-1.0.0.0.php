<?php

$installer = $this;
/* Add related attribute for product */
$catalogEavSetup = new Mage_Catalog_Model_Resource_Eav_Mysql4_Setup();
$catalogEavSetup->removeAttribute(Mage_Catalog_Model_Product::ENTITY, 'vendor_related_product');
$catalogEavSetup->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'vendor_related_product', array(
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