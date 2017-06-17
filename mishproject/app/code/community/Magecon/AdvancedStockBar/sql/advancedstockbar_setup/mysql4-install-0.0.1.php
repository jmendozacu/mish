<?php

/**
 * Open Biz Ltd
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file OPEN-BIZ-LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://mageconsult.net/terms-and-conditions
 *
 * @category   Magecon
 * @package    Magecon_Rma
 * @version    0.0.9
 * @copyright  Copyright (c) 2012 Open Biz Ltd (http://www.mageconsult.net)
 * @license    http://mageconsult.net/terms-and-conditions
 */

$this->startSetup();

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

$setup->addAttribute('catalog_product', 'disable_stock_bar', array(
    'group'         => 'General',
    'input'         => 'select',
    'default'       => '0',
    'source'        => 'eav/entity_attribute_source_boolean',
    'type'          => 'int',
    'label'         => 'Disable Stock Bar',
    'backend'       => '',
    'visible'       => 1,
    'required'      => 0,
    'user_defined' => 0,
    'searchable' => 1,
    'filterable' => 0,
    'comparable'    => 0,
    'visible_on_front' => 0,
    'visible_in_advanced_search'  => 0,
    'is_html_allowed_on_front' => 0,
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));

$this->endSetup();
?>
