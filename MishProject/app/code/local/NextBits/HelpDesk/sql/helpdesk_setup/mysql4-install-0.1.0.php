<?php
$installer = $this;
$installer->startSetup();
$ticket = $installer->getTable('helpdesk/ticket');
$sql=<<<SQLTEXT
CREATE TABLE `$ticket` (
	`ticket_id` INT(11) NOT NULL AUTO_INCREMENT,
	`subject` VARCHAR(255) NULL DEFAULT NULL,
	`message` LONGTEXT NULL,
	`status` VARCHAR(255) NULL DEFAULT NULL,
	`priority` VARCHAR(255) NULL DEFAULT NULL,
	`created_date` DATETIME NULL DEFAULT NULL,
	`updated_date` DATETIME NULL DEFAULT NULL,
	`customer_id` INT(11) NULL DEFAULT NULL,
	`order` VARCHAR(255) NULL DEFAULT NULL,
	PRIMARY KEY (`ticket_id`)
)
ENGINE=InnoDB
;
SQLTEXT;

$installer->run($sql);
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
	 