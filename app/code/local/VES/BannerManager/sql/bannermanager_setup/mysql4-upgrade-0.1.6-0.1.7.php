<?php
$installer = $this;

$installer->startSetup();

$installer->run("
    ALTER TABLE {$installer->getTable('bannermanager/banner')}
        ADD COLUMN  `nivo_options` TEXT NULL DEFAULT NULL ,
        ADD COLUMN  `duration` DECIMAL(3,1) NOT NULL default 0.5,
        ADD COLUMN  `frequency`DECIMAL(3,1) NOT NULL default 3.0,
        ADD COLUMN  `autoglide` tinyint(1) NOT NULL default 1,
        ADD COLUMN  `controls_type` enum('number', 'arrow') NOT NULL default 'number';
"
        );

$installer->endSetup();