<?php

$th =  new Mage_Catalog_Model_Resource_Setup();  
$th->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'productfrom', array(
            'group' => 'General', 
            'type' => 'text',
                        'attribute_set' =>  'Default', // Your custom Attribute set
            'backend' => '',
            'frontend' => '',
            'label' => 'Product From',
            'input' => 'text',
            'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => '1',
            'searchable' => false,
            'filterable' => true,
            'comparable' => false,
            'visible_on_front' => true,
            'visible_in_advanced_search' => true,
            'used_in_product_listing' => true,
            'unique' => false,
            'apply_to' => 'simple,configurable,bundle,grouped',  // Apply to simple product type
        ) );