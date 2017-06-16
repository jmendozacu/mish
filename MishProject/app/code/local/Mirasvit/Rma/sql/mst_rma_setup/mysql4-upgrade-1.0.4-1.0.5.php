<?php
$installer = $this;
$installer->startSetup();
if (Mage::registry('mst_allow_drop_tables')) {
    $sql = "
    ";
    $installer->run($sql);
}
$sql = "
ALTER TABLE `{$this->getTable('rma/status')}` ADD COLUMN `code` VARCHAR(255) NOT NULL DEFAULT '';
ALTER TABLE `{$this->getTable('rma/rma')}` ADD COLUMN `last_reply_name` VARCHAR(255) NOT NULL DEFAULT '';
ALTER TABLE `{$this->getTable('rma/rma')}` ADD COLUMN `last_reply_at` TIMESTAMP NULL;
";
$installer->run($sql);

$sql = "
   INSERT INTO `{$this->getTable('rma/status')}` (name,is_active,sort_order,code,is_rma_resolved,customer_message,history_message,admin_message) VALUES ('Package Sent','1','25','package_sent','0','','','Package is sent.');

";
$installer->run($sql);

/**                                    **/


$installer->endSetup();