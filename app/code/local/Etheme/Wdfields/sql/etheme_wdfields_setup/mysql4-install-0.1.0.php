<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 11.09.2015
 * Time: 12:22
 */ 
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->startSetup();
Mage::getConfig()->saveConfig('cms/wysiwyg/enabled', 'hidden');


$setup->addAttribute('catalog_product', 'wd_videobox', array(
    'group'         => 'Video',
    'input'         => 'textarea',
    'type'          => 'text',
    'label'         => 'Video youtube url',
    'backend'       => '',
    'visible'       => 1,
    'required'      => 0,
    'user_defined' => 1,
    'searchable' => 0,
    'filterable' => 0,
    'comparable'    => 0,
    'visible_on_front' => 1,
    'note'=>'ex. https://www.youtube.com/watch?v=L9szn1QQfas',
    'visible_in_advanced_search'  => 0,
    'is_html_allowed_on_front' => 0,
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));

$setup->addAttribute('catalog_product', 'wd_customtabtitle', array(
    'group'         => 'Custom Tab',
    'input'         => 'text',
    'type'          => 'text',
    'label'         => 'Custom Tab Title',
    'backend'       => '',
    'visible'       => 1,
    'required'      => 0,
    'user_defined' => 1,
    'searchable' => 0,
    'filterable' => 0,
    'comparable'    => 0,
    'visible_on_front' => 1,
    'visible_in_advanced_search'  => 0,
    'is_html_allowed_on_front' => 0,
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));

$setup->addAttribute('catalog_product', 'wd_customtab', array(
    'group'         => 'Custom Tab',
    'input'         => 'textarea',
    'type'          => 'text',
    'label'         => 'Custom Tab Content',
    'backend'       => '',
    'visible'       => 1,
    'required'      => 0,
    'user_defined' => 1,
    'searchable' => 0,
    'filterable' => 0,
    'comparable'    => 0,
    'visible_on_front' => 1,
    'visible_in_advanced_search'  => 0,
    'is_html_allowed_on_front' => 0,
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));

$setup->addAttribute('catalog_product', 'wd_customhtml', array(
    'group'         => 'Custom Html Right Column',
    'input'         => 'text',
    'type'          => 'text',
    'label'         => 'Product Custom Html',
    'backend'       => '',
    'note'=>'paste cms block identifier, for ex.  wd_product_right_column_custom_html',
    'visible'       => 1,
    'required'      => 0,
    'user_defined' => 1,
    'searchable' => 0,
    'filterable' => 0,
    'comparable'    => 0,
    'visible_on_front' => 1,
    'visible_in_advanced_search'  => 0,
    'is_html_allowed_on_front' => 0,
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));


$eavConfig = Mage::getSingleton('eav/config');


$attribute_5 = $eavConfig->getAttribute('catalog_product', 'wd_customtab');
$attribute_5->setData('is_wysiwyg_enabled', 1);
$attribute_5->save();

$attribute_6 = $eavConfig->getAttribute('catalog_product', 'wd_customhtml');
$attribute_6->setData('is_wysiwyg_enabled', 1);
$attribute_6->save();

$installer->endSetup();