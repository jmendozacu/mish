<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Help Desk MX
 * @version   1.1.0
 * @build     1285
 * @copyright Copyright (C) 2015 Mirasvit (http://mirasvit.com/)
 */


$installer = $this;
$version = Mage::helper('mstcore/version')->getModuleVersionFromDb('mst_helpdesk');
if ($version == '1.0.21') {
    return;
} elseif ($version != '1.0.20') {
    die("Please, run migration Helpdesk 1.0.20");
}
$installer->startSetup();
if (Mage::registry('mst_allow_drop_tables')) {
    $sql = "
    ";
    $installer->run($sql);
}
$sql = "
ALTER TABLE `{$this->getTable('helpdesk/ticket')}` ADD COLUMN `cc` TEXT;
ALTER TABLE `{$this->getTable('helpdesk/ticket')}` ADD COLUMN `bcc` TEXT;


ALTER TABLE `{$this->getTable('helpdesk/email')}` ADD COLUMN `cc` TEXT;
ALTER TABLE `{$this->getTable('helpdesk/email')}` ADD COLUMN `bcc` TEXT;
";
$installer->run($sql);

/**                                    **/


$installer->endSetup();