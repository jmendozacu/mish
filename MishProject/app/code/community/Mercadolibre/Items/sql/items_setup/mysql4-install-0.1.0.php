<?php

try {
$installer = $this;

$installer->startSetup();

$installer->run("


DROP TABLE IF EXISTS `mercadolibre_answer_template`;
DROP TABLE IF EXISTS `mercadolibre_attribute_value_mapping`;
DROP TABLE IF EXISTS `mercadolibre_buyer`;
DROP TABLE IF EXISTS `mercadolibre_categories`;
DROP TABLE IF EXISTS `mercadolibre_categories_filter`;
DROP TABLE IF EXISTS `mercadolibre_categories_mapping`;
DROP TABLE IF EXISTS `mercadolibre_category_attributes`;
DROP TABLE IF EXISTS `mercadolibre_category_attributes_temp`;
DROP TABLE IF EXISTS `mercadolibre_category_attribute_values`;
DROP TABLE IF EXISTS `mercadolibre_category_attribute_values_temp`;
DROP TABLE IF EXISTS `mercadolibre_category_update`;
DROP TABLE IF EXISTS `mercadolibre_item`;
DROP TABLE IF EXISTS `mercadolibre_item_attributes`;
DROP TABLE IF EXISTS `mercadolibre_item_price_qty`;
DROP TABLE IF EXISTS `mercadolibre_item_profile_detail`;
DROP TABLE IF EXISTS `mercadolibre_notifications`;
DROP TABLE IF EXISTS `mercadolibre_order`;
DROP TABLE IF EXISTS `mercadolibre_order_feedback`;
DROP TABLE IF EXISTS `mercadolibre_order_items`;
DROP TABLE IF EXISTS `mercadolibre_order_variation_attributes`;
DROP TABLE IF EXISTS `mercadolibre_payment_methods`;
DROP TABLE IF EXISTS `mercadolibre_product_templates`;
DROP TABLE IF EXISTS `mercadolibre_questions`;
DROP TABLE IF EXISTS `mercadolibre_shipping`;
DROP TABLE IF EXISTS `mercadolibre_shipping_custom`;
DROP TABLE IF EXISTS `mercadolibre_sites`;
DROP TABLE IF EXISTS `mercadolibre_token_details`;
DROP TABLE IF EXISTS `mercadolibre_master_templates`;







-- DROP TABLE IF EXISTS {$this->getTable('mercadolibre_category_update')};
CREATE TABLE IF NOT EXISTS {$this->getTable('mercadolibre_category_update')} (
  `update_id` int(11) NOT NULL AUTO_INCREMENT,
  `created_datetime` datetime NOT NULL,
  `run_datetime` datetime NOT NULL,
  PRIMARY KEY (`update_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;



INSERT INTO `mercadolibre_category_update` (`created_datetime`, `run_datetime`) VALUES
('2013-06-19 19:54:47', '2013-06-21 02:40:19');


-- DROP TABLE IF EXISTS {$this->getTable('mercadolibre_categories')};
CREATE TABLE IF NOT EXISTS {$this->getTable('mercadolibre_categories')} (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `meli_category_id` varchar(100) DEFAULT NULL,
  `meli_category_name` varchar(200) DEFAULT NULL,
  `site_id` varchar(100) DEFAULT NULL,
  `has_attributes` tinyint(1) NOT NULL DEFAULT '0',
  `root_id` varchar(20) NOT NULL DEFAULT '0',
  `listing_allowed` enum('1','0') NOT NULL DEFAULT '0',
  `buying_allowed` enum('1','0') NOT NULL DEFAULT '0',
  `shipping_modes` varchar(500) NOT NULL,
  PRIMARY KEY (`category_id`),
  KEY `meli_category_id` (`meli_category_id`),
  KEY `root_id` (`root_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;



-- DROP TABLE IF EXISTS {$this->getTable('mercadolibre_category_attributes')};
CREATE TABLE IF NOT EXISTS {$this->getTable('mercadolibre_category_attributes')} (
  `attribute_id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` varchar(100) NOT NULL COMMENT 'meli_category_id',
  `meli_attribute_id` varchar(200) DEFAULT NULL,
  `meli_attribute_name` varchar(100) DEFAULT NULL,
  `meli_attribute_type` varchar(200) DEFAULT NULL,
  `required` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`attribute_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;



-- DROP TABLE IF EXISTS {$this->getTable('mercadolibre_category_attribute_values')};
CREATE TABLE IF NOT EXISTS {$this->getTable('mercadolibre_category_attribute_values')} (
  `value_id` int(11) NOT NULL AUTO_INCREMENT,
  `meli_category_id` varchar(100) NOT NULL,
  `attribute_id` varchar(200) NOT NULL COMMENT 'meli_attribute_id',
  `meli_value_id` varchar(200) DEFAULT NULL,
  `meli_value_name` varchar(100) DEFAULT NULL,
  `meli_value_name_extended` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`value_id`),
  KEY `meli_category_id` (`meli_category_id`),
  KEY `attribute_id` (`attribute_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


-- DROP TABLE IF EXISTS {$this->getTable('mercadolibre_categories_filter')};
CREATE TABLE IF NOT EXISTS {$this->getTable('mercadolibre_categories_filter')}  (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `meli_category_id` varchar(100) DEFAULT NULL,
  `meli_category_name` varchar(200) DEFAULT NULL,
  `meli_category_path` longtext NOT NULL,
  `site_id` varchar(100) DEFAULT NULL,
  `has_attributes` tinyint(1) NOT NULL DEFAULT '0',
  `root_id` varchar(20) NOT NULL DEFAULT '0',
  `listing_allowed` enum('1','0') NOT NULL DEFAULT '0',
  `buying_allowed` enum('1','0') NOT NULL DEFAULT '0',
  `store_id` smallint(5) NOT NULL,
  PRIMARY KEY (`category_id`),
  KEY `meli_category_id` (`meli_category_id`),
  KEY `root_id` (`root_id`),
  KEY `store_id` (`store_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

-- DROP TABLE IF EXISTS {$this->getTable('mercadolibre_category_attributes_temp')};
CREATE TABLE IF NOT EXISTS {$this->getTable('mercadolibre_category_attributes_temp')} (
  `attribute_id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` varchar(100) NOT NULL,
  `meli_attribute_id` varchar(200) DEFAULT NULL,
  `meli_attribute_name` varchar(100) DEFAULT NULL,
  `meli_attribute_type` varchar(200) DEFAULT NULL,
  `required` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`attribute_id`),
  KEY `category_id` (`category_id`),
  KEY `meli_attribute_id` (`meli_attribute_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;



-- DROP TABLE IF EXISTS {$this->getTable('mercadolibre_category_attribute_values_temp')};
CREATE TABLE IF NOT EXISTS {$this->getTable('mercadolibre_category_attribute_values_temp')} (
  `value_id` int(11) NOT NULL AUTO_INCREMENT,
  `meli_category_id` varchar(100) NOT NULL,
  `attribute_id` varchar(200) NOT NULL COMMENT 'meli_attribute_id',
  `meli_value_id` varchar(200) DEFAULT NULL,
  `meli_value_name` varchar(100) DEFAULT NULL,
  `meli_value_name_extended` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`value_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;



");



    $installer->endSetup();
} catch (Exception $e) {
print_r($e);
    die;
}