<?php

$installer = $this;

$catalogEavSetup = new Mage_Catalog_Model_Resource_Eav_Mysql4_Setup();
$catalogEavSetup->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'vendor_categories', array(
	'group' 				=> 'General',
	'sort_order' 			=> 21,
	'type' 					=> 'text',
	'backend'	 			=> 'eav/entity_attribute_backend_array',
	'frontend' 				=> '',
	'label' 				=> 'Vendor Categories',
	'note' 					=> 'Vendor Categories',
	'input' 				=> 'multiselect',
	'class' 				=> '',
	'source' 				=> '',
	'global' 				=> Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
	'visible' 				=> true,
	'required' 				=> false,
	'user_defined' 			=> false,
	'default' 				=> '',
	'visible_on_front' 		=> false,
	'unique' 				=> false,
	'is_configurable'	 	=> false,
	'used_for_promo_rules' 	=> true
));


//$this->getConnection()->addColumn($this->getTable('catalog/product'), 'vendor_categories', 'text DEFAULT NULL AFTER type_id');