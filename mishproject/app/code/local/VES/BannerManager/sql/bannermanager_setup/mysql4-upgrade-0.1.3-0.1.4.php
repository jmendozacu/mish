<?php

$installer = $this;
$itemCollection = Mage::getModel('bannermanager/item')->getCollection();
$installer->startSetup();
$sql ="
ALTER TABLE {$itemCollection->getTable('item')} CHANGE `from_date` `from_date` VARCHAR( 255 ) NOT NULL ,
CHANGE `to_date` `to_date` VARCHAR( 255 ) NOT NULL;
";

$installer->run($sql);
$installer->endSetup();