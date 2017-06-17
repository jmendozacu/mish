<?php

$installer = $this;

$installer->startSetup();

$installer->run("

ALTER TABLE {$this->getTable('core/url_rewrite')} 
ADD `is_vendors_url` smallint;
    ");

$installer->endSetup();