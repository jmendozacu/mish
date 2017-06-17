<?php

$installer = $this;
$installer->startSetup();
$sql ="
ALTER TABLE {$installer->getTable('bannermanager/banner')} ADD `vendor_id` INT( 11 ) NOT NULL AFTER `banner_id`;
ALTER TABLE {$installer->getTable('bannermanager/item')} 
ADD `identifier` VARCHAR( 255 ) NOT NULL AFTER `item_id`,
ADD `vendor_id` INT UNSIGNED NOT NULL AFTER `item_id`
;
";

$installer->run($sql);
$installer->endSetup();