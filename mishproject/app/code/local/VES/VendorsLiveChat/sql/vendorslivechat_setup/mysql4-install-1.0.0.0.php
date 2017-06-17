<?php

$installer = $this;

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('vendorslivechat/livechat')};
CREATE TABLE {$this->getTable('vendorslivechat/livechat')} (
  `livechat_id` int(11) unsigned NOT NULL auto_increment,
  `vendor_id` INT( 11 ) NOT NULL,
  `status` smallint(6) NOT NULL default '0',
  PRIMARY KEY (`livechat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS {$this->getTable('vendorslivechat/session')};
CREATE TABLE {$this->getTable('vendorslivechat/session')} (
  `session_id` int(11) unsigned NOT NULL auto_increment,
  `vendor_id` INT( 11 ) NOT NULL,
  `customer_id` INT( 11 ) NOT NULL,
  `session_key` varchar(255) NOT NULL default '',
  `name` varchar(255) NOT NULL default '',
  `email` varchar(600) NOT NULL default '',
  `status` smallint(6) NOT NULL default '0',
  `show_on` smallint(6) NOT NULL default '0',
  `ip` varchar(255) NOT NULL default '',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS {$this->getTable('vendorslivechat/message')};
CREATE TABLE {$this->getTable('vendorslivechat/message')} (
  `message_id` int(11) unsigned NOT NULL auto_increment,
  `content` text NOT NULL default '',
  `type` smallint(6) NOT NULL default '0',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  `session_id` INT( 11 ) NOT NULL,
  PRIMARY KEY (`message_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 ");

$installer->getConnection()->addConstraint('PK_livechat_vendor',$installer->getTable('vendorslivechat/livechat'),'vendor_id',
    $installer->getTable('vendors/vendor'),'entity_id',
    'cascade',
    'cascade'

);

$installer->getConnection()->addConstraint('PK_livechat_box',$installer->getTable('vendorslivechat/session'),'vendor_id',
    $installer->getTable('vendors/vendor'),'entity_id',
    'cascade',
    'cascade'

);

$installer->getConnection()->addConstraint('PK_livechat_box_message',$installer->getTable('vendorslivechat/message'),'session_id',
    $installer->getTable('vendorslivechat/session'),'session_id',
    'cascade',
    'cascade'

);

$installer->endSetup(); 