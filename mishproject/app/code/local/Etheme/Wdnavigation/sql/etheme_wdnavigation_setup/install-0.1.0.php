<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 09.10.2015
 * Time: 20:18
 */ 
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer = Mage::getResourceModel('catalog/setup', 'catalog_setup');
$installer->startSetup();




$installer->addAttribute('catalog_category', 'wd_nav_simple', array(
    'group'             => 'Welldone Navigation',
    'label'             => 'Apply simple mode (shows only tree)',
    'type'              => 'varchar',
    'input'             => 'select',
    'source'            => 'welldone/category_attribute_source_type_yesno',
    'visible'           => true,
    'required'          => false,
    'backend'           => '',
    'frontend'          => '',
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'user_defined'      => true,
    'visible_on_front'  => true,
    'wysiwyg_enabled'   => false,
    'is_html_allowed_on_front'    => false,
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));

$installer->addAttribute('catalog_category', 'wd_nav_top', array(
    'group'         => 'Welldone Navigation',
    'input'         => 'textarea',
    'type'          => 'text',
    'label'         => 'Html block above menu (for Vertical Megamenu)',
    'backend'       => '',
    'visible'       => 1,
    'required'      => 0,
    'is_wysiwyg_enabled' => 1,
    'visible_on_front' => 1,
    'note'=>'This field is compatible only with 1st-level category (for Vertical Left-sidebar-megamenu)',
    'is_html_allowed_on_front' => 1,
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));

$installer->addAttribute('catalog_category', 'wd_nav_btm', array(
    'group'         => 'Welldone Navigation',
    'input'         => 'textarea',
    'type'          => 'text',
    'label'         => 'Html block under menu (for Vertical Megamenu)',
    'backend'       => '',
    'visible'       => 1,
    'required'      => 0,
    'is_wysiwyg_enabled' => 1,
    'visible_on_front' => 1,
    'note'=>'This field is compatible only with 1st-level category (for Vertical Left-sidebar-megamenu)',
    'is_html_allowed_on_front' => 1,
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));

$installer->addAttribute('catalog_category', 'wd_nav_right', array(
    'group'         => 'Welldone Navigation',
    'input'         => 'textarea',
    'type'          => 'text',
    'label'         => 'Megamenu Vertical Right Html',
    'backend'       => '',
    'visible'       => 1,
    'required'      => 0,
    'is_wysiwyg_enabled' => 1,
    'visible_on_front' => 1,
    'note'=>'This field is compatible only with 1st-level category (for Vertical Left-sidebar-megamenu). For ex. paste next html code to show image <br />'.htmlentities("
<img src='{{media url=wysiwyg/welldone/img-megamenu.jpg}}' alt='Megamenu Image'>"),
    'is_html_allowed_on_front' => 1,
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));

$installer->addAttribute('catalog_category', 'wd_category_lable', array(
    'group'         => 'Welldone Navigation',
    'input'         => 'text',
    'type'          => 'text',
    'label'         => 'Category label, for ex. "Hot!"',
    'backend'       => '',
    'visible'       => 1,
    'required'      => 0,
    'is_wysiwyg_enabled' => 1,
    'visible_on_front' => 1,
    'note'=>'This field is compatible only with 1st-level category megamenu',
    'is_html_allowed_on_front' => 1,
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));


$eavConfig = Mage::getSingleton('eav/config');

$attribute_2 = $eavConfig->getAttribute('catalog_category', 'wd_nav_top');
$attribute_2->setData('is_wysiwyg_enabled', 1);
$attribute_2->save();

$attribute_3 = $eavConfig->getAttribute('catalog_category', 'wd_nav_btm');
$attribute_3->setData('is_wysiwyg_enabled', 1);
$attribute_3->save();

$attribute_4 = $eavConfig->getAttribute('catalog_category', 'wd_nav_right');
$attribute_4->setData('is_wysiwyg_enabled', 1);
$attribute_4->save();
$installer->endSetup();