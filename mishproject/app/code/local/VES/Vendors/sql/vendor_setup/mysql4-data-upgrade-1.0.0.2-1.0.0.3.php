<?php

$installer = $this;
$installer->startSetup();
$sql = "CREATE TABLE IF NOT EXISTS `ves_vendor_additional_test` (
`vendor_additional_id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `address` varchar(255) NOT NULL,
  `city` varchar(100) NOT NULL,
  `state` varchar(100) NOT NULL,
  `postcode` varchar(20) NOT NULL,
  `country_id` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
$installer->run($sql);
$installer->endSetup();