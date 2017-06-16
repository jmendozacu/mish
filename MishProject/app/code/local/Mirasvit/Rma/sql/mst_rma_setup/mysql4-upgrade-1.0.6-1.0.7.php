<?php
$installer = $this;
$installer->startSetup();
if (Mage::registry('mst_allow_drop_tables')) {
    $sql = "
    ";
    $installer->run($sql);
}
$sql = "
ALTER TABLE `{$this->getTable('rma/rma')}` ADD COLUMN `is_gift` TINYINT(1) NOT NULL DEFAULT 0;
";
$installer->run($sql);

/**                                    **/


$installer->endSetup();