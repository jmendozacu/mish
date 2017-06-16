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
 * @package    AW_Deliverydate
 * @version    1.3
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */


$installer = $this;
$setup = $this;

/* $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$installer->run("

	CREATE TABLE IF NOT EXISTS {$this->getTable('deliverydate/holiday')} (
	  `id` int(5) NOT NULL auto_increment,
	  `store_id` int(5) NOT NULL,
	  `period_type` enum('single','recurrent_day','recurrent_date','period','recurrent_period') NOT NULL,
	  `period_recurrence_type` enum('monthly','yearly') NOT NULL,
	  `period_from` date NOT NULL,
	  `period_to` date NOT NULL,
	  PRIMARY KEY  (`id`)
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
");


$typeId = Mage::getModel('eav/entity_type')->loadByCode('order')->getEntityTypeId();
$setup->addAttribute($typeId, 'aw_deliverydate_notice', array(
    'backend'       => '',
	'frontend'		=> '',
    'label'         => 'Delivery Notice',
    'input'         => 'text',
	'type'			=> 'text',
    'visible'       => 1,
    'user_defined'  => false,
    'default'       => '',
	'is_required'		=> false,
    'visible_on_front' => true
));

$setup->addAttribute($typeId, 'aw_deliverydate_date', array(
    'backend'       => 'eav/entity_attribute_backend_datetime',
	'backend_type'	=> 'datetime',
    'label'         => 'Delivery Date',
    'input'         => 'date',
	'type'			=> 'datetime',
    'visible'       => 1,
    'user_defined'  => false,
    'default'       => '',
	'is_required'		=> false,
    'visible_on_front' => true
));

$installer->endSetup();