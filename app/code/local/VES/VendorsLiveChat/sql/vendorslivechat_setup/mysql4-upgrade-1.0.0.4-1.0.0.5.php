<?php
$installer = $this;
$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('vendorslivechat/command')};
CREATE TABLE {$this->getTable('vendorslivechat/command')} (
  `command_id` int(11) unsigned NOT NULL auto_increment,
  `session_id` INT( 11 ) NOT NULL,
  `command` varchar(255) NOT NULL default '',
  `type` smallint(6) NOT NULL default '0',
  `data` text NOT NULL default '',
  PRIMARY KEY (`command_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

 ");

$installer->getConnection()->addConstraint('PK_livechat_command',$installer->getTable('vendorslivechat/command'),'session_id',
    $installer->getTable('vendorslivechat/session'),'session_id',
    'cascade',
    'cascade'

);

$installer->endSetup();