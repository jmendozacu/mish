<?php

$installer = $this;
$installer->startSetup();
$sql ="
ALTER TABLE {$installer->getTable('bannermanager/item')} ADD `file_thumbnail` VARCHAR( 255 ) NOT NULL AFTER `filename`;
";

$installer->run($sql);
$installer->endSetup();