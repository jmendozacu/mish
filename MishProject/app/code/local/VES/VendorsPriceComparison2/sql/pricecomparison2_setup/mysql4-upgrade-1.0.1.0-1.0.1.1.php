<?php

$installer = $this;
/* Add related attribute for product */
$catalogEavSetup = new Mage_Catalog_Model_Resource_Eav_Mysql4_Setup();
try{
    $idAttribute = $installer->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'vendor_child_product' , 'attribute_id');
    $catalogEavSetup->updateAttribute(Mage_Catalog_Model_Product::ENTITY, $idAttribute, array('backend_type' => 'static'));
}catch (Exception $e){

}

$installer->getConnection()->addColumn(
    $installer->getTable('catalog/product'),
    'vendor_child_product',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'unsigned'  => true,
        'nullable'  => true,
        'comment'   => 'Vendor Child Product',
        'default'   => '0',
    )
);

$installer->endSetup(); 