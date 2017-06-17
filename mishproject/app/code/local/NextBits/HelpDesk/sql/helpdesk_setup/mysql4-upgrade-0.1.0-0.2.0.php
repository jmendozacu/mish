<?php
$installer = $this;
$installer->startSetup();
$comment = $installer->getTable('helpdesk/comment');
$sql=<<<SQLTEXT
CREATE TABLE `$comment` (
	`comment_id` INT(11) NOT NULL AUTO_INCREMENT,
	`ticket_id`  INT(11) NULL,
	`comment` LONGTEXT NULL,
	`created_date` DATETIME NULL DEFAULT NULL,
	`comment_by` VARCHAR(255) NULL DEFAULT NULL,
	`is_customer_comment` INT(11) NULL DEFAULT NULL,
	PRIMARY KEY (`comment_id`)
)
ENGINE=InnoDB
;
SQLTEXT;

$installer->run($sql);
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
	 