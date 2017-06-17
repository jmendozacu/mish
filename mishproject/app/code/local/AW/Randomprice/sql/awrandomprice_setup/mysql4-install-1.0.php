<?php
/**
* aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Randomprice
 * @version    1.0
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */


$installer = $this;
$installer->startSetup();

try {
    $installer->run("
        CREATE TABLE IF NOT EXISTS `{$this->getTable('awrandomprice/randomprice')}` (
            `randompriceid` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `name` TINYTEXT NOT NULL,
            `is_enabled` TINYINT NOT NULL DEFAULT '0',
            `date_from` DATETIME NULL,
            `date_to` DATETIME NULL,
            `store_ids` TINYTEXT NOT NULL,
            `url` MEDIUMTEXT NOT NULL,
            `block_title` TINYTEXT NOT NULL,
            `design` TINYTEXT NOT NULL,
            `show_format` TINYTEXT NOT NULL,
            `template` MEDIUMTEXT NOT NULL,
            `status` TINYINT NOT NULL DEFAULT '0',
            `autom_display` tinyint(4) NULL,
            `conditions_serialized` MEDIUMTEXT NULL,
            `customer_group_ids` TEXT NOT NULL,
            `price_increase` INT NOT NULL ,
            `price_decrease` INT NOT NULL,
            `allow_special_price` INT NOT NULL,
            `delay_retry` INT NOT NULL
        

        ) ENGINE = MyISAM DEFAULT CHARSET=utf8;

    ");
} catch (Exception $ex) {


    echo $ex->getMessage();

    die();
    Mage::logException($ex);
}

$installer->endSetup();
