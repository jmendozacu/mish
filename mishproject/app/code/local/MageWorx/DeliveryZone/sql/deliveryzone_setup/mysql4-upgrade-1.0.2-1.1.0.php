<?php
/**
 * MageWorx
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MageWorx EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.mageworx.com/LICENSE-1.0.html
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@mageworx.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.mageworx.com/ for more information
 * or send an email to sales@mageworx.com
 *
 * @category   MageWorx
 * @package    MageWorx_DeliveryZone
 * @copyright  Copyright (c) 2013 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * MageWorx DeliveryZone extension
 *
 * @category   MageWorx
 * @package    MageWorx_DeliveryZone
 * @author     MageWorx Dev Team <dev@mageworx.com>
 */
$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();
$installer->run("
CREATE TABLE IF NOT EXISTS `{$this->getTable('deliveryzone_rates')}` (
  `rate_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Rate Id',
  `name` varchar(255) DEFAULT NULL COMMENT 'Name',
  `description` text COMMENT 'Description',
  `is_active` smallint(6) NOT NULL DEFAULT '0' COMMENT 'Is Active',
  `conditions_serialized` mediumtext COMMENT 'Conditions Serialized',
  `actions_serialized` mediumtext COMMENT 'Actions Serialized',
  `simple_action` varchar(20) NOT NULL,
  `shipping_cost` decimal(12,4) DEFAULT NULL,
  `surcharge_fixed` decimal(12,4) DEFAULT NULL,
  `surcharge_percent` double NOT NULL,
  `fixed_per_product` decimal(12,4) DEFAULT NULL,
  `percent_per_product` double NOT NULL,
  `percent_per_item` double NOT NULL,
  `fixed_per_item` decimal(12,4) DEFAULT NULL,
  `percent_per_order` double NOT NULL,
  `fixed_per_weight` decimal(12,4) DEFAULT NULL,
  `sort_order` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Sort Order',
  PRIMARY KEY (`rate_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `{$this->getTable('deliveryzone_rates_customergroup')}` (
  `entity_id` int(11) NOT NULL AUTO_INCREMENT,
  `rate_id` int(10) NOT NULL,
  `customergroup_id` smallint(5) NOT NULL,
  PRIMARY KEY (`entity_id`),
  KEY `rate_id` (`rate_id`,`customergroup_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `{$this->getTable('deliveryzone_rates_store')}` (
  `entity_id` int(11) NOT NULL AUTO_INCREMENT,
  `rate_id` int(10) NOT NULL,
  `store_id` smallint(5) NOT NULL,
  PRIMARY KEY (`entity_id`),
  KEY `rate_id` (`rate_id`,`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `{$this->getTable('deliveryzone_rates_carrier')}` (
  `entity_id` int(11) NOT NULL AUTO_INCREMENT,
  `rate_id` int(5) NOT NULL,
  `carrier` varchar(50) NOT NULL,
  `method_id` varchar(100) NOT NULL,
  PRIMARY KEY (`entity_id`),
  KEY `rate_id` (`rate_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");
if (!$installer->getConnection()->tableColumnExists($this->getTable('deliveryzone_zones'), 'status')) {
    $installer->run("ALTER TABLE `{$this->getTable('deliveryzone_zones')}` ADD `status` TINYINT NOT NULL;");
}

$installer->endSetup();