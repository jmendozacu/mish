<?php
$installer = $this;
$installer->startSetup();
if (Mage::registry('mst_allow_drop_tables')) {
    $sql = "
    ";
    $installer->run($sql);
}
$sql = "
ALTER TABLE `{$this->getTable('rma/field')}` ADD COLUMN `visible_customer_status` VARCHAR(255) NOT NULL DEFAULT '';
";
$installer->run($sql);

/**                                    **/


$installer->endSetup();