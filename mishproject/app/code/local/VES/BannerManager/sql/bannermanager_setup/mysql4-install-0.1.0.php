<?php

$installer = $this;

$installer->startSetup();
$sql = "

DROP TABLE IF EXISTS {$installer->getTable('bannermanager/banner')};
DROP TABLE IF EXISTS {$installer->getTable('bannermanager/item')};
CREATE TABLE {$installer->getTable('bannermanager/banner')} (
  `banner_id` int(11) unsigned NOT NULL auto_increment,
  `identifier` varchar(255) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  `status` smallint(6) NOT NULL default '0',
  `template` varchar(255) NOT NULL default '',
  `easing` varchar(255) NOT NULL default '',
  `display_description` smallint(6) NOT NULL default '0',
  `width` smallint(6) NOT NULL default '0',
  `height` smallint(6) NOT NULL default '0',
  `delay` smallint(6) NOT NULL default '0',
  `description` text NOT NULL default '',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`banner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE {$installer->getTable('bannermanager/item')} (
  `item_id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `filename` varchar(255) NOT NULL default '',
  `url` varchar(255) NOT NULL default '',
  `short_description` text NOT NULL default '',
  `description` text NOT NULL default '',
  `sort_order` int(11) NOT NULL default '0',
  `status` smallint(6) NOT NULL default '0',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  `banner_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ";
$installer->run($sql);

$installer->endSetup(); 