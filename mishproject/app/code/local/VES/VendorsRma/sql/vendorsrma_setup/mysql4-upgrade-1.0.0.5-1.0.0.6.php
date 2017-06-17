<?php
$installer = $this;
$installer->startSetup();

$installer->run("
   DROP TABLE IF EXISTS {$this->getTable('vendorsrma/history')};
   CREATE TABLE {$this->getTable('vendorsrma/history')} (
  `history_id` int(11) unsigned NOT NULL auto_increment,
  `status` smallint(6) NOT NULL default '0',
  `change_by` varchar(255) NOT NULL default '',
  `created_time` datetime NULL,
  `type` varchar(255) NULL default '',
  `request_id` int(11) NOT NULL default '0',
  PRIMARY KEY (`history_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ");

$installer->getConnection()->addConstraint('PK_history_request',$installer->getTable('vendorsrma/history'),'request_id',
    $installer->getTable('vendorsrma/request'),'entity_id',
    'cascade',
    'cascade'

);

$installer->endSetup();


