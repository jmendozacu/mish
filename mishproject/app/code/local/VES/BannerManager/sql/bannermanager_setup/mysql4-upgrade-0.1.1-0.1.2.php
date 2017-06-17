<?php

$installer = $this;
$itemCollection = Mage::getModel('bannermanager/item')->getCollection();
$installer->startSetup();
$sql ="
ALTER TABLE {$itemCollection->getTable('item')} ADD `from_date` DATETIME NULL DEFAULT NULL ,
ADD `to_date` DATETIME NULL DEFAULT NULL 
";

$installer->run($sql);
$installer->endSetup();