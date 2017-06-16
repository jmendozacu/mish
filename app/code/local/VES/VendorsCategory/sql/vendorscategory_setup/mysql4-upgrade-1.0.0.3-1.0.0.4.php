<?php

$installer = $this;

$installer->startSetup();

$installer->run("

ALTER TABLE {$this->getTable('vendorscategory/category')} 
ADD `path` varchar(255);
    ");

$installer->endSetup();