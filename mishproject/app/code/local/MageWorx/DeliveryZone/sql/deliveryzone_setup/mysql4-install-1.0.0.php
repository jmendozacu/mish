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
/* @var $installer MageWorx_DeliveryZone_Model_Mysql4_Setup */

$installer->startSetup();

$installer->run("
-- DROP TABLE IF EXISTS `{$this->getTable('deliveryzone_zones')}`;
CREATE TABLE IF NOT EXISTS `{$this->getTable('deliveryzone_zones')}` (
  `zone_id` SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`zone_id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS `{$this->getTable('deliveryzone_locations')}`;
CREATE TABLE IF NOT EXISTS `{$this->getTable('deliveryzone_locations')}` (
  `entity_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `zone_id` SMALLINT(5) UNSIGNED NOT NULL,
  `country_id` VARCHAR(2) NOT NULL DEFAULT '',
  `region_ids` TEXT NOT NULL,
  PRIMARY KEY (`entity_id`),
  CONSTRAINT `FK_deliveryzone_locations` FOREIGN KEY (`zone_id`) REFERENCES {$this->getTable('deliveryzone_zones')} (`zone_id`) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS `{$this->getTable('deliveryzone_categories')}`;
CREATE TABLE IF NOT EXISTS `{$this->getTable('deliveryzone_categories')}` (
   `entity_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
   `zone_id` SMALLINT(5) UNSIGNED NOT NULL,
  `category_id` INT(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`entity_id`),
  CONSTRAINT `FK_deliveryzone_categories` FOREIGN KEY (`zone_id`) REFERENCES {$this->getTable('deliveryzone_zones')} (`zone_id`) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS `{$this->getTable('deliveryzone_shippingmethods')}`;
CREATE TABLE IF NOT EXISTS `{$this->getTable('deliveryzone_shippingmethods')}` (
  `entity_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `carrier_id` VARCHAR(255) NOT NULL DEFAULT '',
  `zone_id` SMALLINT(5) UNSIGNED NOT NULL,
  `allowed_methods` TEXT NOT NULL,
  PRIMARY KEY (`entity_id`),
  CONSTRAINT `FK_deliveryzone_shippingmethods` FOREIGN KEY (`zone_id`) REFERENCES {$this->getTable('deliveryzone_zones')} (`zone_id`) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS `{$this->getTable('deliveryzone_products')}`;
CREATE TABLE IF NOT EXISTS `{$this->getTable('deliveryzone_products')}` (
 `entity_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
 `product_id` INT(11) UNSIGNED NOT NULL,
  `zone_id` SMALLINT(5) UNSIGNED NOT NULL,
  PRIMARY KEY (`entity_id`),
    CONSTRAINT `FK_deliveryzone_products` FOREIGN KEY (`zone_id`) REFERENCES {$this->getTable('deliveryzone_zones')} (`zone_id`) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS `{$this->getTable('deliveryzone_customer_group_carriers')}`;
CREATE TABLE IF NOT EXISTS `{$this->getTable('deliveryzone_customer_group_carriers')}` (
  `entity_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `customer_group_id` SMALLINT(5) UNSIGNED NOT NULL,
  `carrier_id` VARCHAR(255) NOT NULL DEFAULT '',
  `allowed_methods` TEXT NOT NULL,
  PRIMARY KEY (`entity_id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

");
// DEPRICATED SINCE 1.1.0
/**
-- DROP TABLE IF EXISTS `{$this->getTable('deliveryzone_carriers')}`;
CREATE TABLE IF NOT EXISTS `{$this->getTable('deliveryzone_carriers')}` (
  `entity_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `carrier_id` VARCHAR(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`entity_id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS `{$this->getTable('deliveryzone_carrier_attributes')}`;
CREATE TABLE IF NOT EXISTS `{$this->getTable('deliveryzone_carrier_attributes')}` (
  `entity_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `carrier_id` VARCHAR(255) NOT NULL DEFAULT '',
  `attribute_type` VARCHAR(255) NOT NULL DEFAULT '',
  `attribute_id` VARCHAR(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`entity_id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS `{$this->getTable('deliveryzone_carrier_values')}`;
CREATE TABLE IF NOT EXISTS `{$this->getTable('deliveryzone_carrier_values')}` (
  `entity_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `attribute_type` VARCHAR(255) NOT NULL DEFAULT '',
  `carrier_id` VARCHAR(255) NOT NULL DEFAULT '',
  `method` VARCHAR(255) NOT NULL DEFAULT '',
  `attribute_id` VARCHAR(255) NOT NULL DEFAULT '',
  `attribute_value` TEXT NOT NULL,
  PRIMARY KEY (`entity_id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;
*/

$installer->endSetup();
