<?php
/**
 * @category    Venustheme
 * @package     Ves_FAQ
 * @copyright   Copyright (c) 2015 Venustheme (http://www.venustheme.com/)
 * @license     http://www.venustheme.com/license-agreement.html
 */

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();


/**
 * create faq table
 */
$installer->run("

DROP TABLE IF EXISTS `{$this->getTable('ves_faq/category')}`;
CREATE TABLE IF NOT EXISTS `{$this->getTable('ves_faq/category')}` (
	`category_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(255) NOT NULL DEFAULT '',
	`prefix` varchar(100) NOT NULL DEFAULT '',
	`parent_id` int(11) NOT NULL,
	`identifier` varchar(255) NOT NULL DEFAULT '',
	`layout` varchar(255) NOT NULL DEFAULT '',
	`title` varchar(255) NOT NULL DEFAULT '',
	`image` varchar(255) NOT NULL DEFAULT '',
	`description` text NOT NULL,
	`status` smallint(5) NOT NULL,
	`position` int(10) NOT NULL DEFAULT 0,
	`meta_keywords` text NOT NULL,
	`meta_description` text NOT NULL,
	`include_in_sidebar` smallint(5) NOT NULL,
	PRIMARY KEY (`category_id`),
	UNIQUE KEY `identifier` (`identifier`)
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `{$this->getTable('ves_faq/category_store')}`;
CREATE TABLE `{$this->getTable('ves_faq/category_store')}` (
	`category_id` int(10) unsigned NOT NULL,
	`store_id` smallint(5) unsigned NOT NULL,
	PRIMARY KEY (`category_id`,`store_id`),
	CONSTRAINT `FK_FAQ_CATEGORY_STORE_THEME` FOREIGN KEY (`category_id`) REFERENCES `{$this->getTable('ves_faq/category')}` (`category_id`) ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT `FK_FAQ_CATEGORY_STORE_STORE` FOREIGN KEY (`store_id`) REFERENCES `{$this->getTable('core/store')}` (`store_id`) ON UPDATE CASCADE ON DELETE CASCADE
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{$this->getTable('ves_faq/question')}`;
CREATE TABLE IF NOT EXISTS `{$this->getTable('ves_faq/question')}` (
	`question_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`title` varchar(255) NOT NULL DEFAULT '',
	`description` text NOT NULL,
	`default_answer` text NOT NULL,
	`status` varchar(10) NOT NULL DEFAULT '0',
	`author_name` varchar(255) NOT NULL DEFAULT '',
	`author_email` varchar(255) NOT NULL DEFAULT '',
	`customer_id` int(11) NOT NULL DEFAULT '0',
	`category_id` int(11) NOT NULL,
	`question_type` varchar(200),
	`product_id` int (5),
	`visibility` varchar(11) NOT NULL DEFAULT '',
	`position` int(10) NOT NULL DEFAULT 0,
	`created_at` date NOT NULL,
	`updated_at` date NOT NULL,
	PRIMARY KEY (`question_id`)
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `{$this->getTable('ves_faq/question_store')}`;
CREATE TABLE `{$this->getTable('ves_faq/question_store')}` (
	`question_id` int(10) unsigned NOT NULL,
	`store_id` smallint(5) unsigned NOT NULL,
	PRIMARY KEY (`question_id`,`store_id`),
	CONSTRAINT `FK_FAQ_QUESTION_STORE_THEME` FOREIGN KEY (`question_id`) REFERENCES `{$this->getTable('ves_faq/question')}` (`question_id`) ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT `FK_FAQ_Question_STORE_STORE` FOREIGN KEY (`store_id`) REFERENCES `{$this->getTable('core/store')}` (`store_id`) ON UPDATE CASCADE ON DELETE CASCADE
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{$this->getTable('ves_faq/answer')}`;
CREATE TABLE IF NOT EXISTS `{$this->getTable('ves_faq/answer')}` (
	`answer_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`author_name` varchar(255) NOT NULL DEFAULT '',
	`author_email` varchar(255) NOT NULL DEFAULT '',
	`question_id` int(10) unsigned NOT NULL,
	`answer_content` text NOT NULL,
	`status` varchar(10) NOT NULL DEFAULT '0',
	`created_at` date NOT NULL,
	`updated_at` date NOT NULL,
	PRIMARY KEY (`answer_id`),
	CONSTRAINT `FK_FAQ_ANSWER_QUESTION` FOREIGN KEY (`question_id`) REFERENCES `{$this->getTable('ves_faq/question')}` (`question_id`) ON UPDATE CASCADE ON DELETE CASCADE
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `{$this->getTable('ves_faq/answer_store')}`;
CREATE TABLE `{$this->getTable('ves_faq/answer_store')}` (
	`answer_id` int(10) unsigned NOT NULL,
	`store_id` smallint(5) unsigned NOT NULL,
	PRIMARY KEY (`answer_id`,`store_id`),
	CONSTRAINT `FK_FAQ_ANSWER_STORE_THEME` FOREIGN KEY (`answer_id`) REFERENCES `{$this->getTable('ves_faq/answer')}` (`answer_id`) ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT `FK_FAQ_ANSWER_STORE_STORE` FOREIGN KEY (`store_id`) REFERENCES `{$this->getTable('core/store')}` (`store_id`) ON UPDATE CASCADE ON DELETE CASCADE
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

$installer->endSetup();

