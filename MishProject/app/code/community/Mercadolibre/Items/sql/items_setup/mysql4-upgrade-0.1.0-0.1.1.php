<?php

try {
$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('mercadolibre_categories_mapping')};
CREATE TABLE IF NOT EXISTS {$this->getTable('mercadolibre_categories_mapping')} (
  `mapping_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `meli_category_id` varchar(100) DEFAULT NULL COMMENT 'This is meli_category_id from meli_categories_filter',
  `mage_category_id` int(11) NOT NULL DEFAULT '0' COMMENT 'This is magento''s product  category id',
  `root_id` varchar(50) NOT NULL,
  `created_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `store_id` smallint(5) NOT NULL,
  PRIMARY KEY (`mapping_id`),
  KEY `meli_category_id` (`meli_category_id`),
  KEY `root_id` (`root_id`),
  KEY `mage_category_id` (`mage_category_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


-- DROP TABLE IF EXISTS {$this->getTable('mercadolibre_item')};
CREATE TABLE IF NOT EXISTS {$this->getTable('mercadolibre_item')} (
   `item_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `meli_item_id` varchar(20) NOT NULL,
  `pictures` varchar(500) NOT NULL,
  `site_id` varchar(10) DEFAULT NULL,
  `title` varchar(300) DEFAULT NULL,
  `subtitle` varchar(300) DEFAULT NULL,
  `category_id` int(11) NOT NULL COMMENT 'Magento''s category_id',
  `price` varchar(10) DEFAULT NULL,
  `base_price` varchar(10) DEFAULT NULL,
  `initial_quantity` varchar(10) DEFAULT NULL,
  `available_quantity` varchar(10) DEFAULT NULL,
  `sold_quantity` varchar(10) DEFAULT NULL,
  `start_time` datetime DEFAULT NULL,
  `stop_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `condition_id` varchar(25) DEFAULT NULL,
  `video_id` varchar(40) DEFAULT NULL,
  `descriptions` text,
  `accepts_mercadopago` tinyint(1) DEFAULT NULL,
  `status` varchar(30) DEFAULT NULL,
  `sub_status` varchar(30) DEFAULT NULL,
  `warranty` varchar(30) DEFAULT NULL,
  `date_created` datetime DEFAULT NULL,
  `last_updated` datetime DEFAULT NULL,
  `created_datetime` datetime DEFAULT NULL,
  `sent_to_publish` enum('Published','Unpublished') NOT NULL DEFAULT 'Unpublished',
  `parent_item_id` varchar(20) NOT NULL,
  `permalink` varchar(500) NOT NULL,
  `main_image_required` enum('0','1') NOT NULL DEFAULT '0' COMMENT 'if 1 Use Main Image In Template Body',
  `main_image` varchar(500) NOT NULL COMMENT 'main product''s  Base Image  ',
  `mage_type_id` varchar(100) NOT NULL,
  `master_temp_id` int(11) NOT NULL,
  `meli_category_id` varchar(100) NOT NULL COMMENT 'once listing posted this would be use',
  `store_id` smallint(6) NOT NULL,
  PRIMARY KEY (`item_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;




-- DROP TABLE IF EXISTS {$this->getTable('mercadolibre_product_templates')};
CREATE TABLE IF NOT EXISTS {$this->getTable('mercadolibre_product_templates')} (
  `template_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT NULL,
  `buying_mode_id` varchar(25) DEFAULT NULL,
  `listing_type_id` varchar(25) DEFAULT NULL,
  `condition_id` varchar(25) DEFAULT NULL,
  `date_created` datetime NOT NULL,
  `last_updated` datetime NOT NULL,
  `store_id` smallint(5) NOT NULL,
  PRIMARY KEY (`template_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;


-- DROP TABLE IF EXISTS {$this->getTable('mercadolibre_item_attributes')};
CREATE TABLE IF NOT EXISTS {$this->getTable('mercadolibre_item_attributes')} (
  `meli_item_attribute_id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `attribute_id` varchar(200) NOT NULL COMMENT 'magento attributre',
  `value_id` varchar(200) NOT NULL COMMENT 'magento attribute value',
  `meli_attribute_id` varchar(200) DEFAULT NULL COMMENT 'autoincrement  attribute_id',
  `meli_value_id` varchar(200) DEFAULT NULL COMMENT 'autoincrement  value_id',
  `store_id` smallint(6) NOT NULL,
  PRIMARY KEY (`meli_item_attribute_id`),
  KEY `item_id` (`item_id`),
  KEY `meli_attribute_id` (`meli_attribute_id`),
  KEY `meli_value_id` (`meli_value_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;



-- DROP TABLE IF EXISTS {$this->getTable('mercadolibre_attribute_value_mapping')};
CREATE TABLE IF NOT EXISTS {$this->getTable('mercadolibre_attribute_value_mapping')} (
  `attribute_mapping_id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` varchar(100) NOT NULL COMMENT 'meli_category_id',
  `meli_attribute_id` varchar(200) NOT NULL,
  `meli_value_id` varchar(200) NOT NULL,
  `mage_attribute` varchar(100) NOT NULL,
  `mage_attribute_original` varchar(100) NOT NULL,
  `mage_attribute_value` varchar(100) NOT NULL,
  `mage_attribute_option_id` int(11) NOT NULL,
  `store_id` smallint(5) NOT NULL,
  `sort_order` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`attribute_mapping_id`),
  KEY `category_id` (`category_id`),
  KEY `mage_attribute` (`mage_attribute`),
  KEY `mage_attribute_value` (`mage_attribute_value`),
  KEY `meli_attribute_id` (`meli_attribute_id`),
  KEY `meli_value_id` (`meli_value_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;


-- DROP TABLE IF EXISTS {$this->getTable('mercadolibre_item_price_qty')};
CREATE TABLE IF NOT EXISTS {$this->getTable('mercadolibre_item_price_qty')} (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `item_id` int(15) NOT NULL,
  `meli_price` varchar(10) NOT NULL,
  `meli_qty` varchar(10) NOT NULL,
  `store_id` smallint(6) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;


-- DROP TABLE IF EXISTS {$this->getTable('mercadolibre_item_profile_detail')};
CREATE TABLE IF NOT EXISTS {$this->getTable('mercadolibre_item_profile_detail')} (
  `profile_id` int(12) NOT NULL AUTO_INCREMENT,
  `item_id` int(12) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `profile_name` varchar(100) NOT NULL,
  `description_header` text,
  `description_body` text,
  `description_footer` text,
  `date_created` date NOT NULL,
  `date_updated` date NOT NULL,
  `store_id` smallint(5) NOT NULL,
  PRIMARY KEY (`profile_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;



-- DROP TABLE IF EXISTS {$this->getTable('mercadolibre_shipping')};
CREATE TABLE IF NOT EXISTS {$this->getTable('mercadolibre_shipping')} (
  `shipping_id` int(11) NOT NULL AUTO_INCREMENT,
  `shipping_profile` varchar(100) NOT NULL,
  `shipping_mode` varchar(20) NOT NULL,
  `shipping_method` varchar(50) NOT NULL,
  `created_date` datetime NOT NULL,
  `updated_date` datetime NOT NULL,
  `store_id` smallint(5) NOT NULL,
  KEY `shipping_id` (`shipping_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;


-- DROP TABLE IF EXISTS {$this->getTable('mercadolibre_shipping_custom')};
CREATE TABLE IF NOT EXISTS {$this->getTable('mercadolibre_shipping_custom')} (
  `custom_shipping_id` int(11) NOT NULL AUTO_INCREMENT,
  `shipping_id` int(11) NOT NULL COMMENT 'autoincrement  of meli_shipping',
  `shipping_service_name` varchar(100) NOT NULL,
  `shipping_cost` decimal(10,2) NOT NULL,
  PRIMARY KEY (`custom_shipping_id`),
  KEY `shipping_id` (`shipping_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;


-- DROP TABLE IF EXISTS {$this->getTable('mercadolibre_notifications')};
CREATE TABLE IF NOT EXISTS {$this->getTable('mercadolibre_notifications')} (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `application_id` varchar(50) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `resource` varchar(50) DEFAULT NULL,
  `topic` varchar(50) DEFAULT NULL,
  `attempts` int(11) NOT NULL,
  `received` varchar(50) DEFAULT NULL,
  `sent` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;



-- DROP TABLE IF EXISTS {$this->getTable('mercadolibre_questions')};
CREATE TABLE IF NOT EXISTS {$this->getTable('mercadolibre_questions')} (
 `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Entity id',
  `question_id` varchar(255) NOT NULL,
  `question` text COMMENT 'Question',
  `itemid` varchar(255) DEFAULT NULL COMMENT 'item id',
  `question_date` varchar(63) DEFAULT NULL COMMENT 'Question Date',
  `status` varchar(100) DEFAULT NULL COMMENT 'Status',
  `buyer` varchar(100) DEFAULT NULL COMMENT 'Buyer',
  `answer` varchar(100) DEFAULT NULL COMMENT 'Answer',
  `answer_date` varchar(100) DEFAULT NULL COMMENT 'Answer Date',
  `created_at` date DEFAULT NULL COMMENT 'Creation Time',
  PRIMARY KEY (`id`),
  KEY `IDX_MERCADOLIBRE_QUESTIONS_CREATED_AT` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Questions' AUTO_INCREMENT=1 ;



-- DROP TABLE IF EXISTS {$this->getTable('mercadolibre_answer_template')};
CREATE TABLE IF NOT EXISTS {$this->getTable('mercadolibre_answer_template')} (
  `answer_template_id` int(11) NOT NULL AUTO_INCREMENT,
  `answer` text NOT NULL,
  `title` varchar(255) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `store_id` smallint(5) NOT NULL,
  PRIMARY KEY (`answer_template_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;


--
-- Table structure for table `mercadolibre_buyer`
--

-- DROP TABLE IF EXISTS {$this->getTable('mercadolibre_buyer')};
CREATE TABLE IF NOT EXISTS {$this->getTable('mercadolibre_buyer')} (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `buyer_id` int(11) NOT NULL,
  `nickname` varchar(100) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

--
-- Table structure for table `mercadolibre_order`
--
-- DROP TABLE IF EXISTS {$this->getTable('mercadolibre_order')};
CREATE TABLE IF NOT EXISTS {$this->getTable('mercadolibre_order')} (
`id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `mage_order_id` int(11) DEFAULT NULL,
  `mage_order_number` varchar(50) NOT NULL,
  `date_created` datetime DEFAULT NULL,
  `date_closed` datetime DEFAULT NULL,
  `status` varchar(30) DEFAULT NULL,
  `status_detail` varchar(300) DEFAULT NULL,
  `buyer_id` int(11) NOT NULL,
  `payment_id` varchar(100) DEFAULT NULL,
  `transaction_amount` varchar(100) DEFAULT NULL,
  `payment_currency_id` varchar(20) DEFAULT NULL,
  `payment_status` enum('0','1') DEFAULT '0',
  `shipping_id` int(15) DEFAULT NULL,
  `shipping_status` enum('0','1') DEFAULT '0',
  `shipment_type` varchar(25) NOT NULL,
  `shipment_date_created` datetime DEFAULT NULL,
  `shipping_cost` varchar(100) DEFAULT NULL,
  `shipping_currency_id` varchar(20) DEFAULT NULL,
  `receiver_address` varchar(500) DEFAULT NULL,
  `total_amount` int(15) DEFAULT NULL,
  `currency_id` varchar(20) DEFAULT NULL,
  `tags` varchar(300) DEFAULT NULL,
  `store_id` smallint(6) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

--
-- Table structure for table `mercadolibre_order_items`
--
-- DROP TABLE IF EXISTS {$this->getTable('mercadolibre_order_items')};
CREATE TABLE IF NOT EXISTS {$this->getTable('mercadolibre_order_items')} (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `item_id` varchar(20) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `variation_id` varchar(100) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `unit_price` varchar(10) DEFAULT NULL,
  `currency_id` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;



--
-- Table structure for table `mercadolibre_order_variation_attributes`
--
-- DROP TABLE IF EXISTS {$this->getTable('mercadolibre_order_variation_attributes')};
CREATE TABLE IF NOT EXISTS {$this->getTable('mercadolibre_order_variation_attributes')} (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(20) NOT NULL,
  `item_id` varchar(20) NOT NULL,
  `attribute_id` int(11) NOT NULL,
  `variation_id` varchar(100) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `value_id` varchar(100) DEFAULT NULL,
  `value_name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;



-- DROP TABLE IF EXISTS {$this->getTable('mercadolibre_payment_methods')};
CREATE TABLE IF NOT EXISTS {$this->getTable('mercadolibre_payment_methods')} (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `payment_id` varchar(100) DEFAULT NULL,
  `payment_name` varchar(100) DEFAULT NULL,
  `payment_type_id` varchar(100) DEFAULT NULL,
  `site_id` varchar(20) DEFAULT NULL,
  `thumbnail` varchar(100) DEFAULT NULL,
  `secure_thumbnail` varchar(100) DEFAULT NULL,
  `date_created` datetime DEFAULT NULL,
  `last_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- DROP TABLE IF EXISTS {$this->getTable('mercadolibre_sites')};
CREATE TABLE IF NOT EXISTS {$this->getTable('mercadolibre_sites')} (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` varchar(20) DEFAULT NULL,
  `site_name` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

INSERT INTO `mercadolibre_sites` (`site_id`, `site_name`) VALUES
('MLA', 'Argentina'),
('MLB', 'Brasil'),
('MCO', 'Colombia'),
('MCR', 'Costa Rica'),
('MEC', 'Ecuador'),
('MLC', 'Chile'),
('MLM', 'Mexico'),
('MLU', 'Uruguay'),
('MLV', 'Venezuela'),
('MPA', 'Panamá'),
('MPE', 'Perú'),
('MPT', 'Portugal'),
('MRD', 'Dominicana');

ALTER TABLE `sales_flat_order` ADD `meli_order_id` INT(10) NULL AFTER `customer_id`;

");

   $installer->endSetup();
} catch (Exception $e) {
print_r($e);
    die;
}