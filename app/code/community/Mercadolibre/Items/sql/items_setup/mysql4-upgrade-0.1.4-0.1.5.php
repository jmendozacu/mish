<?php

try {
$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('mercadolibre_order_feedback')};
CREATE TABLE IF NOT EXISTS {$this->getTable('mercadolibre_order_feedback')} (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `rating` varchar(100) NOT NULL,
  `fulfilled` tinyint(1) NOT NULL,
  `reason` text NOT NULL,
  `message` text NOT NULL,
  `reply` text NOT NULL,
  `rating_seller` varchar(100) NOT NULL COMMENT 'Rating By Seller when reply on feedback',
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

-- DROP TABLE IF EXISTS {$this->getTable('mercadolibre_token_details')};
CREATE TABLE IF NOT EXISTS {$this->getTable('mercadolibre_token_details')} (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `seller_id` int(11) NOT NULL,
  `seller_nickname` varchar(100) NOT NULL,
  `access_token` varchar(200) NOT NULL,
  `token_expires` varchar(50) NOT NULL,
  `store_id` smallint(5) NOT NULL COMMENT 'Magento store_id',
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;


--
-- Table structure for table `mercadolibre_master_templates`
--
-- DROP TABLE IF EXISTS {$this->getTable('mercadolibre_master_templates')};
CREATE TABLE IF NOT EXISTS {$this->getTable('mercadolibre_master_templates')} (
  `master_temp_id` int(11) NOT NULL AUTO_INCREMENT,
  `master_temp_title` varchar(100) DEFAULT NULL,
  `template_id` int(11) DEFAULT NULL COMMENT 'autoincrement  of mercadolibre_product_templates for combination of buying_mode_id , listing_type_id ,condition_id',
  `shipping_id` int(11) DEFAULT NULL,
  `profile_id` int(11) DEFAULT NULL COMMENT 'for product description from  mercadolibre_item_profile_detail',
  `payment_id` varchar(100) DEFAULT NULL,
  `date_created` datetime NOT NULL,
  `last_updated` datetime NOT NULL,
  `store_id` smallint(5) NOT NULL,
  PRIMARY KEY (`master_temp_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;


");

    $installer->endSetup();
} catch (Exception $e) {
print_r($e);
    die;
}