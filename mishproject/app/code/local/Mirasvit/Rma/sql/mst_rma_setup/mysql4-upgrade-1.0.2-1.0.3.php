<?php
$installer = $this;
$installer->startSetup();
if (Mage::registry('mst_allow_drop_tables')) {
    $sql = "
    ";
    $installer->run($sql);
}
$sql = "
ALTER TABLE `{$this->getTable('rma/item')}` ADD COLUMN `name` VARCHAR(255) NOT NULL DEFAULT '';
ALTER TABLE `{$this->getTable('rma/item')}` ADD COLUMN `product_options` TEXT;
";
$installer->run($sql);

/**                                    **/


$installer->endSetup();