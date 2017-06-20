<?php

$installer = $this;
/* Add related attribute for product */
$catalogEavSetup = new Mage_Catalog_Model_Resource_Eav_Mysql4_Setup();
try{
    $idAttribute = $installer->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'vendor_child_product' , 'attribute_id');
    $catalogEavSetup->updateAttribute(Mage_Catalog_Model_Product::ENTITY, $idAttribute, array('backend_type' => 'int'));
}catch (Exception $e){

}
/**
 * Update default value for vemdpr_child_product
 */
$installer->getConnection()->dropColumn($installer->getTable('catalog/product'), 'vendor_child_product');
foreach(Mage::getModel('catalog/product')->getCollection()->addAttributeToSelect('vendor_child_product') as $product){
    if(!$product->getData('vendor_child_product')){
        $product->setData('vendor_child_product',0)->getResource()->saveAttribute($product, 'vendor_child_product');
    }
}
$installer->endSetup(); 