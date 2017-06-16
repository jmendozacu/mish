<?php
$installer = $this;
$installer->startSetup();

$installer->run("
	DROP TABLE IF EXISTS {$this->getTable('vendorsrma/note')};
    CREATE TABLE {$this->getTable('vendorsrma/note')} (
      `note_id` int(11) unsigned NOT NULL auto_increment,
      `message` text NOT NULL default '',
      `attachment` text NULL,
      `type` smallint(6) NOT NULL default '0',
      `status` varchar(255) NOT NULL default '',
      `request_id` int(11) NOT NULL default '0',
      `created_time` datetime NULL,
      `update_time` datetime NULL,
      PRIMARY KEY (`note_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		");


$installer->getConnection()->addConstraint('PK_note_request',$installer->getTable('vendorsrma/note'),'request_id',
    $installer->getTable('vendorsrma/request'),'entity_id',
    'cascade',
    'cascade'

);

$installer->endSetup();


