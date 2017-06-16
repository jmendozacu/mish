<?php
/**
 * MageWorx
 * Currency Switcher Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CurrencySwitcher
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

// 1.2.0
if ($installer->tableExists($this->getTable('currencyswitcher_relations')) && !$installer->tableExists($this->getTable('mageworx_currencyswitcher_relations'))) {
    $installer->run("RENAME TABLE {$this->getTable('currencyswitcher_relations')} TO {$this->getTable('mageworx_currencyswitcher_relations')};");
}

// 1.0.9 > 1.1.0
if (!$installer->tableExists($this->getTable('mageworx_currencyswitcher_relations'))) {
    $table = $installer->getConnection()
        ->newTable($installer->getTable('mageworx_currencyswitcher/relations'))
        ->addColumn('relation_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
        ), 'Relation ID')
        ->addColumn('currency_code', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
            'nullable'  => false,
        ), 'Currency Code')
        ->addColumn('countries', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
            'nullable'  => false,
        ), 'Countries list')
        ->setComment('Relation between country and currency');
    $installer->getConnection()->createTable($table);
}

// 1.2.0
$pathLike = 'mageworx_customers/currency_switcher/%';
$configCollection = Mage::getModel('core/config_data')->getCollection();
$configCollection->getSelect()->where('path like ?', $pathLike);

foreach ($configCollection as $conf) {
    $path = $conf->getPath();
    $path = str_replace('mageworx_customers', 'mageworx_geoip', $path);
    $conf->setPath($path)->save();
}

$pathLike = 'mageworx_geoip/currency_switcher/%';
$configCollection = Mage::getModel('core/config_data')->getCollection();
$configCollection->getSelect()->where('path like ?', $pathLike);

foreach ($configCollection as $conf) {
    $path = $conf->getPath();
    $path = str_replace('currency_switcher', 'mageworx_currencyswitcher', $path);
    $conf->setPath($path)->save();
}


$installer->endSetup();