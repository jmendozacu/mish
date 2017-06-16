<?php

$installer = $this;

$installer->startSetup();

$installer->run("

 DROP TABLE IF EXISTS {$this->getTable('vendorsreview/review')};
CREATE TABLE {$this->getTable('vendorsreview/review')} (
  `review_id` int(11) unsigned NOT NULL auto_increment,
  `order_id` int(11) unsigned NOT NULL,
  `created_time` datetime null,
  `title` varchar(255) NOT NULL default '',
  `detail` text NOT NULL default '',
  `nick_name` varchar(255) NOT NULL,
  `customer_id` int(11) unsigned not null,
  `status` smallint(6) NOT NULL default '1',
  `vendor_id` varchar(255) NOT NULL,
  `summary_percent` smallint,
  PRIMARY KEY (`review_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



 DROP TABLE IF EXISTS {$this->getTable('vendorsreview/rating')};
CREATE TABLE {$this->getTable('vendorsreview/rating')} (
  `rating_id` int(11) unsigned NOT NULL auto_increment,
  `rating_code` varchar(64) NOT NULL,
  `position` int(11) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  PRIMARY KEY (`rating_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


INSERT INTO {$this->getTable('vendorsreview/rating')} (
	`rating_code`,`position`,`title`)
VALUES('quality','0','Quality');

INSERT INTO {$this->getTable('vendorsreview/rating')} (
	`rating_code`,`position`,`title`)
VALUES('price','0','Price');

INSERT INTO {$this->getTable('vendorsreview/rating')} (
	`rating_code`,`position`,`title`)
VALUES('value','0','Value');

DROP TABLE IF EXISTS {$this->getTable('vendorsreview/vote')};
CREATE TABLE {$this->getTable('vendorsreview/vote')} (
  `entity_id` int(11) unsigned NOT NULL auto_increment,
  `review_id` int(11) unsigned NOT NULL,
  `rating_id` int(11) unsigned NOT NULL,
  `rate_value` smallint unsigned,
  `rate_percents` int(11) unsigned,
  PRIMARY KEY (`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$this->getTable('vendorsreview/link')};
CREATE TABLE {$this->getTable('vendorsreview/link')} (
  `entity_id` int(11) unsigned NOT NULL auto_increment,
  `vendor_id` int(11) unsigned NOT NULL,
  `customer_id` int(11) unsigned NOT NULL,
  `order_id` int(11) unsigned NOT NULL,
  `show_rating_link` smallint unsigned,
  `can_review` smallint,
  PRIMARY KEY (`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->getConnection()->addForeignKey('FK_RATING_ID', $installer->getTable('vendorsreview/vote'), 'rating_id', $installer->getTable('vendorsreview/rating'), 'rating_id','cascade');

$installer->getConnection()->addForeignKey('FK_REVIEW_ID', $installer->getTable('vendorsreview/vote'), 'review_id', $installer->getTable('vendorsreview/review'), 'review_id','cascade');

$installer->getConnection()->addForeignKey('FK_VENDOR_LINK_ID', $installer->getTable('vendorsreview/link'), 'vendor_id', $installer->getTable('vendors/vendor'), 'entity_id','cascade');

$installer->getConnection()->addForeignKey('FK_CUSTOMER_LINK_ID', $installer->getTable('vendorsreview/link'), 'customer_id', $installer->getTable('customer/entity'), 'entity_id','cascade');

$installer->endSetup(); 