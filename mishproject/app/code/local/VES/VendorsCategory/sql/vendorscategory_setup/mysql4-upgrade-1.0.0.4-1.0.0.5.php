<?php
function reCursiveSetOrder($category) {
    $children = Mage::getModel('vendorscategory/category')->getCollection()
        ->addFieldToFilter('parent_category_id',$category->getId());
    $children->getSelect()->order('main_table.sort_order ASC');
    if($children->count() <= 0) return;
    $i = 1;
    foreach($children as $child) {
        $child->setData('sort_order',$i)->save();
        reCursiveSetOrder($child);
        $i++;
    }
}
$installer = $this;

$installer->startSetup();

//rebuild sort order of all category
//for upgrade only case

$collection = Mage::getModel('vendorscategory/category')->getCollection()
    ->addFieldToFilter('parent_category_id','0');
$collection->getSelect()->order('main_table.sort_order ASC');

$i = 1;
foreach($collection as $_parent) {
    $_parent->setData('sort_order',$i)->save();
    reCursiveSetOrder($_parent);
    $i++;
}


$installer->endSetup();