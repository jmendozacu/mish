<?php
$installer = $this;

$installer->startSetup();

$attributes = array(
    'price',
    'special_price',
    'special_from_date',
    'special_to_date',
    'minimal_price',
    'tax_class_id'
);

foreach ($attributes as $attributeCode) {
    $applyTo = explode(
        ',',
        $installer->getAttribute(
            Mage_Catalog_Model_Product::ENTITY,
            $attributeCode,
            'apply_to'
        )
    );

    if (!in_array('membership', $applyTo)) {
        $applyTo[] = 'membership';
        $installer->updateAttribute(
            Mage_Catalog_Model_Product::ENTITY,
            $attributeCode,
            'apply_to',
            join(',', $applyTo)
        );
    }
}

$installer->endSetup();