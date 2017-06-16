<?php

$installer = $this;
$bannerCollection = Mage::getModel('bannermanager/banner')->getCollection();
$installer->startSetup();
	$sql ="DROP TABLE IF EXISTS {$bannerCollection->getTable('banner_store')};
	CREATE TABLE {$bannerCollection->getTable('banner_store')} (
	  `banner_id` int(11) NOT NULL,
	  `store_id` smallint(6) NOT NULL
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
$installer->run($sql);
$installer->endSetup();