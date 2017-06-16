<?php 
$installer = $this;
$this->getConnection()->addColumn($this->getTable('vendorsquote/quote'), 'sent_reminder_email', 'smallint(1) unsigned DEFAULT 0');
$installer->endSetup();