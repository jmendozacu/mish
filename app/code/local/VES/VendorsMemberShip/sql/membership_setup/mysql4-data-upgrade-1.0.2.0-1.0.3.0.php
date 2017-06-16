<?php
$installer = $this;

$installer->startSetup();
$this->getConnection()->addColumn($this->getTable('vendors/group'), 'priority', 'TINYINT NOT NULL DEFAULT 0');
$installer->endSetup();