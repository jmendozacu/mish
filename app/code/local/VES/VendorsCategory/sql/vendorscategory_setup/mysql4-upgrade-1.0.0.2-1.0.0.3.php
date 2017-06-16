<?php

$installer = $this;

$installer->startSetup();

$installer->run("

ALTER TABLE {$this->getTable('vendorscategory/category')} 
ADD `sort_order` smallint;
    ");

$installer->endSetup();