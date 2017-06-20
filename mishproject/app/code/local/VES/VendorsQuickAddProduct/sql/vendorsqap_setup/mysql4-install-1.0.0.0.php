<?php

$installer = $this;
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->startSetup();

$setup->addAttribute('catalog_category', 'ves_attribute_set', array(
    'group'                             => 'General Information',
    'input'                             => 'select',
    'type'                              => 'varchar',
    'label'                             => 'Attribute Set',
    'backend'                           => '',
    'source'                            => 'vendorsqap/attribute_source_attributeset',
    'visible'                           => 1,
    'required'                          => 1,
    'user_defined'                      => 1,
    'searchable'                        => 1,
    'filterable'                        => 0,
    'comparable'                        => 1,
    'visible_on_front'                  => 1,
    'visible_in_advanced_search'        => 0,
    'is_html_allowed_on_front'          => 0,
    'global'                            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));

$installer->endSetup();