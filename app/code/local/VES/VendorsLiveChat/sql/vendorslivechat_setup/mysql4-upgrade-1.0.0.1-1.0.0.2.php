<?php
$installer = $this;
$installer->startSetup();

$installer->run("
DROP TABLE IF EXISTS {$this->getTable('vendorslivechat/contact')};
CREATE TABLE {$this->getTable('vendorslivechat/contact')} (
  `contact_id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `email` varchar(600) NOT NULL default '',
  `question` text NOT NULL default '',
  `note` text NOT NULL default '',
  `status` smallint(6) NOT NULL default '0',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  `vendor_id` INT( 11 ) NOT NULL,
  PRIMARY KEY (`contact_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

$installer->getConnection()->addConstraint('PK_contact_vendor',$installer->getTable('vendorslivechat/contact'),'vendor_id',
    $installer->getTable('vendors/vendor'),'entity_id',
    'cascade',
    'cascade'

);

$installer->endSetup();