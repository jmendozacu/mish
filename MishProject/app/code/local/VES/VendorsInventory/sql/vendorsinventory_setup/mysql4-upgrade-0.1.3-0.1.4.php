<?php

$installer = $this;
$installer->startSetup();


$installer->addAttribute('catalog_product', 'vendor_dd', array(
    'group' => 'General',
    'label' => 'Vendors',
    'note' => '',
    'type' => 'int', //backend_type
    'input' => 'select', //frontend_input
    'frontend_class' => '',
    'source' => 'vendorsinventory/attribute_source_type',
    'backend' => '',
    'frontend' => '',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'required' => true,
    'visible_on_front' => false,
    'apply_to' => 'simple',
    'is_configurable' => false,
    'used_in_product_listing' => false,
    'sort_order' => 5,
));

$installer->endSetup();
