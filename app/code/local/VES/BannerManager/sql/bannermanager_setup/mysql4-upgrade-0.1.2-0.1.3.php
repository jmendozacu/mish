<?php

$installer = $this;
$itemCollection = Mage::getModel('bannermanager/item')->getCollection();
$installer->startSetup();
$sql ="
ALTER TABLE {$itemCollection->getTable('item')} ADD custom_html TEXT NULL AFTER url
";
$installer->run($sql);
$installer->endSetup();