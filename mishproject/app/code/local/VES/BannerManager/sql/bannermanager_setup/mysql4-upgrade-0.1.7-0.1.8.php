<?php

$installer = $this;
$installer->startSetup();
$sql ="
ALTER TABLE {$installer->getTable('bannermanager/item')} 
ADD `target_mode` tinyint(6) NOT NULL,
ADD `desc_pos` tinyint(6) NOT NULL,
ADD `background` tinyint(6) NOT NULL
;
";

$installer->run($sql);
$installer->endSetup();