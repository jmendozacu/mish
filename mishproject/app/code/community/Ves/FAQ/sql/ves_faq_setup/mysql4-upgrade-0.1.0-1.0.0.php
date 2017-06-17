<?php

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();
if (!$installer->getConnection()->tableColumnExists($this->getTable('ves_faq/question'), "default_answer")) {
	$installer->run("
		ALTER TABLE `{$this->getTable('ves_faq/question')}` ADD COLUMN `default_answer` text NOT NULL;
	");
}
if (!$installer->getConnection()->tableColumnExists($this->getTable('ves_faq/question'), "product_id")) {
	$installer->run("
		ALTER TABLE `{$this->getTable('ves_faq/question')}` ADD COLUMN `product_id` int (5);
	");
}
if (!$installer->getConnection()->tableColumnExists($this->getTable('ves_faq/question'), "question_type")) {
	$installer->run("
		ALTER TABLE `{$this->getTable('ves_faq/question')}` ADD COLUMN `question_type` varchar(200);
	");
}
if (!$installer->getConnection()->tableColumnExists($this->getTable('ves_faq/question'), "customer_id")) {
	$installer->run("
		ALTER TABLE `{$this->getTable('ves_faq/question')}` ADD COLUMN `customer_id` int(11) NOT NULL DEFAULT '0';
	");
}
