<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Asynchronous Cache
 * @version   1.0.0
 * @revision  125
 * @copyright Copyright (C) 2014 Mirasvit (http://mirasvit.com/)
 */


$installer = $this;
$installer->startSetup();

$installer->run("DROP TABLE IF EXISTS `{$installer->getTable('asynccache/asynccache')}`;");
$installer->run("
CREATE TABLE `{$installer->getTable('asynccache/asynccache')}` (
    `cache_id`   int(11)      NOT NULL AUTO_INCREMENT,
    `mode`       varchar(255) NULL,
    `status`     varchar(255) NULL,
    `tags`       varchar(255) NULL,
    `created_at` timestamp    NOT NULL DEFAULT '0000-00-00 00:00:00',  
    `updated_at` timestamp    NOT NULL DEFAULT '0000-00-00 00:00:00',
    PRIMARY KEY (`cache_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='AsyncCache';
");

$installer->endSetup();