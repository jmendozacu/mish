<?php
$installer = $this;
$installer->startSetup();


$installer->run("
DROP TABLE IF EXISTS {$this->getTable('vendorsrma/mestemplate')};
CREATE TABLE {$this->getTable('vendorsrma/mestemplate')} (
  `mestemplate_id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `content_template` text NOT NULL default '',
  `status` smallint(6) NOT NULL default '0',
  `type` smallint(6) NOT NULL default '0',
  PRIMARY KEY (`mestemplate_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8; ");

$installer->endSetup();