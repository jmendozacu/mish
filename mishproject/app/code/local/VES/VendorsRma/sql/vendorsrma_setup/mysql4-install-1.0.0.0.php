<?php

$installer = $this;

$installer->startSetup();

$installer->run("


DROP TABLE IF EXISTS {$this->getTable('vendorsrma/message')};
CREATE TABLE {$this->getTable('vendorsrma/message')} (
    `message_id` int(11) unsigned NOT NULL auto_increment,
    `request_id` int(11) NOT NULL ,
    `message` text  NOT NULL,
    `attachment` text NULL,
    `type` varchar(255) NULL default '',
    `is_show` smallint(6) NOT NULL default '0',
    `from` varchar(600) NULL default '',
    `to` varchar(600) NULL default '',
    `created_time` datetime NULL,
    `update_time` datetime NULL,
PRIMARY KEY (`message_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS {$this->getTable('vendorsrma/item')};
CREATE TABLE {$this->getTable('vendorsrma/item')} (
  `item_id` int(11) unsigned NOT NULL auto_increment,
  `order_item_id` int(11) NOT NULL default '0',
  `qty` int(11) NOT NULL default '0',
  `request_id` int(11) NOT NULL default '0',
  PRIMARY KEY (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS {$this->getTable('vendorsrma/status')};
CREATE TABLE {$this->getTable('vendorsrma/status')} (
  `status_id` int(11) unsigned NOT NULL auto_increment,
  `title` text NOT NULL default '',
  `store_id` varchar(255) NOT NULL default '',
  `resolve` smallint(6) NOT NULL default '0',
  `type` smallint(6) NOT NULL default '0',
  `sort_order` smallint(6) NOT NULL default '0',
  `is_delete` smallint(6) NOT NULL default '0',
  PRIMARY KEY (`status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$this->getTable('vendorsrma/template')};
CREATE TABLE {$this->getTable('vendorsrma/template')} (
  `template_id` int(11) unsigned NOT NULL auto_increment,
  `store_id` varchar(255) NOT NULL default '',
  `title` text NOT NULL default '',
  `template_notify_customer` text NOT NULL default '',
  `template_notify_vendor` text NOT NULL default '',
  `template_notify_history` text NOT NULL default '',
  `template_notify_admin` text NOT NULL default '',
  `status_id` int(11) NOT NULL default '0',
  PRIMARY KEY (`template_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS {$this->getTable('vendorsrma/type')};
CREATE TABLE {$this->getTable('vendorsrma/type')} (
  `type_id` int(11) unsigned NOT NULL auto_increment,
  `store_id` varchar(255) NOT NULL default '',
  `title` text NOT NULL default '',
  `active` smallint(6) NOT NULL default '0',
  `sort_order` smallint(6) NOT NULL default '0',
  `is_delete` smallint(6) NOT NULL default '0',
  PRIMARY KEY (`type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$this->getTable('vendorsrma/reason')};
CREATE TABLE {$this->getTable('vendorsrma/reason')} (
  `reason_id` int(11) unsigned NOT NULL auto_increment,
  `store_id` varchar(255) NOT NULL default '',
  `title` text NOT NULL default '',
  `active` smallint(6) NOT NULL default '0',
  `sort_order` smallint(6) NOT NULL default '0',
  PRIMARY KEY (`reason_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");



//create table vendorsrma/request in eav database
$installer->addEntityType('vendor_rma', array(
    'entity_model'    => 'vendorsrma/request',
    'table'           =>'vendorsrma/request',
    'increment_model'       => 'eav/entity_increment_numeric',
    'increment_per_store'   => true
));

$installer->createEntityTables(
    'vendorsrma/request'
);

$this->addAttribute('vendor_rma', 'package_opened', array(
    'type'              => 'static',
    'label'             => 'Package Opened',
    'input'             => 'text',
    'class'             => '',
    'backend'           => '',
    'frontend'          => '',
    'source'        	=> '',
    'required'          => true,
    'user_defined'      => false,
    'default'           => '',
    'unique'            => false,
));

$this->addAttribute('vendor_rma', 'increment_id', array(
    'type'              => 'static',
    'label'             => 'Increment Id',
    'input'             => 'text',
    'class'             => '',
    'backend'           => '',
    'frontend'          => '',
    'source'        	=> '',
    'required'          => true,
    'user_defined'      => false,
    'default'           => '',
    'unique'            => false,
));

$this->addAttribute('vendor_rma', 'uid', array(
    'type'              => 'static',
    'label'             => 'Uid',
    'input'             => 'text',
    'class'             => '',
    'backend'           => '',
    'frontend'          => '',
    'source'        	=> '',
    'required'          => true,
    'user_defined'      => false,
    'default'           => '',
    'unique'            => false,
));

$this->addAttribute('vendor_rma', 'type', array(
    'type'              => 'static',
    'label'             => 'Type',
    'input'             => 'select',
    'class'             => '',
    'backend'           => '',
    'frontend'          => '',
    'source'        	=> 'vendorsrma/source_type',
    'required'          => true,
    'user_defined'      => false,
    'default'           => '',
    'unique'            => false,
));

$this->addAttribute('vendor_rma', 'reason', array(
    'type'              => 'static',
    'label'             => 'Reason',
    'input'             => 'select',
    'class'             => '',
    'backend'           => '',
    'frontend'          => '',
    'source'        	=> 'vendorsrma/source_reason',
    'required'          => true,
    'user_defined'      => false,
    'default'           => '',
    'unique'            => false,
));


$this->addAttribute('vendor_rma', 'note', array(
    'type'              => 'text',
    'label'             => 'Note',
    'input'             => 'text',
    'class'             => '',
    'backend'           => '',
    'frontend'          => '',
    'source'        	=> '',
    'required'          => true,
    'user_defined'      => false,
    'default'           => '',
    'unique'            => false,
));

$this->addAttribute('vendor_rma', 'total_replies', array(
    'type'              => 'static',
    'label'             => 'Total Replies',
    'input'             => 'text',
    'class'             => '',
    'backend'           => '',
    'frontend'          => '',
    'source'        	=> '',
    'required'          => true,
    'user_defined'      => false,
    'default'           => '',
    'unique'            => false,
));

$this->addAttribute('vendor_rma', 'addition_data', array(
    'type'              => 'text',
    'label'             => 'Addition Data',
    'input'             => 'text',
    'class'             => '',
    'backend'           => '',
    'frontend'          => '',
    'source'        	=> '',
    'required'          => true,
    'user_defined'      => false,
    'default'           => '',
    'unique'            => false,
));

$this->addAttribute('vendor_rma', 'tracking_code', array(
    'type'              => 'static',
    'label'             => 'Tracking code',
    'input'             => 'text',
    'class'             => '',
    'backend'           => '',
    'frontend'          => '',
    'required'          => true,
    'user_defined'      => false,
    'default'           => '',
    'unique'            => false,
));

$this->addAttribute('vendor_rma', 'order_incremental_id', array(
    'type'              => 'static',
    'label'             => 'Order Incremental Id',
    'input'             => 'text',
    'class'             => '',
    'backend'           => '',
    'frontend'          => '',
    'source'        	=> '',
    'required'          => false,
    'user_defined'      => false,
    'default'           => '',
    'unique'            => false,
));

$this->addAttribute('vendor_rma', 'status', array(
    'type'              => 'static',
    'label'             => 'Status',
    'input'             => 'select',
    'class'             => '',
    'backend'           => '',
    'frontend'          => '',
    'source'        	=> 'vendorsrma/source_status',
    'required'          => true,
    'user_defined'      => false,
    'default'           => '',
    'unique'            => false,
));


$installer->getConnection()->addConstraint('PK_item_request',$installer->getTable('vendorsrma/item'),'request_id',
    $installer->getTable('vendorsrma/request'),'entity_id',
    'cascade',
    'cascade'

);

$installer->getConnection()->addConstraint('PK_status_template',$installer->getTable('vendorsrma/template'),'status_id',
    $installer->getTable('vendorsrma/status'),'status_id',
    'cascade',
    'cascade'

);


$installer->getConnection()->addConstraint('PK_message_request',$installer->getTable('vendorsrma/message'),'request_id',
    $installer->getTable('vendorsrma/request'),'entity_id',
    'cascade',
    'cascade'

);

$this->getConnection()->addColumn($this->getTable('vendorsrma/request'), 'increment_id', 'text DEFAULT NULL');
$this->getConnection()->addColumn($this->getTable('vendorsrma/request'), 'package_opened', 'smallint(5) unsigned NOT NULL DEFAULT \'0\'');
$this->getConnection()->addColumn($this->getTable('vendorsrma/request'), 'uid', 'text DEFAULT NULL');
$this->getConnection()->addColumn($this->getTable('vendorsrma/request'), 'tracking_code', 'text DEFAULT NULL');
$this->getConnection()->addColumn($this->getTable('vendorsrma/request'), 'type', 'smallint(5) unsigned NOT NULL DEFAULT \'0\'');
$this->getConnection()->addColumn($this->getTable('vendorsrma/request'), 'reason', 'smallint(5) unsigned NOT NULL DEFAULT \'0\'');
$this->getConnection()->addColumn($this->getTable('vendorsrma/request'), 'order_incremental_id', 'varchar(255) DEFAULT NULL');
$this->getConnection()->addColumn($this->getTable('vendorsrma/request'), 'total_replies', 'int(11) unsigned NOT NULL DEFAULT \'0\'');
$this->getConnection()->addColumn($this->getTable('vendorsrma/request'), 'status', 'smallint(5) unsigned NOT NULL DEFAULT \'0\'');



$installer->endSetup();