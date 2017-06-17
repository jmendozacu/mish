<?php
$installer = $this;
$installer->startSetup();
if (Mage::registry('mst_allow_drop_tables')) {
    $sql = "
    ";
    $installer->run($sql);
}
$sql = "
ALTER TABLE `{$this->getTable('rma/rma')}` ADD COLUMN `ticket_id` INT(11) ;
ALTER TABLE `{$this->getTable('rma/rma')}` ADD COLUMN `user_id` INT(11) ;
ALTER TABLE `{$this->getTable('rma/comment')}` ADD COLUMN `email_id` INT(11) ;
update `{$this->getTable('rma/status')}` SET
	customer_message = REPLACE(customer_message, '[rma_guest_print_url]', '{{var rma.guest_print_url}}'),
	admin_message = REPLACE(admin_message, '[rma_guest_print_url]', '{{var rma.guest_print_url}}'),
	history_message = REPLACE(history_message, '[rma_guest_print_url]', '{{var rma.guest_print_url}}');

update `{$this->getTable('rma/status')}` SET
	customer_message = REPLACE(customer_message, '[rma_guest_url]', '{{var rma.guest_url}}'),
	admin_message = REPLACE(admin_message, '[rma_guest_url]', '{{var rma.guest_url}}'),
	history_message = REPLACE(history_message, '[rma_guest_url]', '{{var rma.guest_url}}');

update `{$this->getTable('rma/status')}` SET
	customer_message = REPLACE(customer_message, '[rma_increment_id]', '{{var rma.increment_id}}'),
	admin_message = REPLACE(admin_message, '[rma_increment_id]', '{{var rma.increment_id}}'),
	history_message = REPLACE(history_message, '[rma_increment_id]', '{{var rma.increment_id}}');

update `{$this->getTable('rma/status')}` SET
	customer_message = REPLACE(customer_message, '[customer_name]', '{{var customer.name}}'),
	admin_message = REPLACE(admin_message, '[customer_name]', '{{var customer.name}}'),
	history_message = REPLACE(history_message, '[customer_name]', '{{var customer.name}}');

update `{$this->getTable('rma/status')}` SET
	customer_message = REPLACE(customer_message, '[order_increment_id]', '{{var order.increment_id}}'),
	admin_message = REPLACE(admin_message, '[order_increment_id]', '{{var order.increment_id}}'),
	history_message = REPLACE(history_message, '[order_increment_id]', '{{var order.increment_id}}');

update `{$this->getTable('rma/status')}` SET
	customer_message = REPLACE(customer_message, '[rma_return_address]', '{{var rma.return_address_html}}'),
	admin_message = REPLACE(admin_message, '[rma_return_address]', '{{var rma.return_address_html}}'),
	history_message = REPLACE(history_message, '[rma_return_address]', '{{var rma.return_address_html}}');

update `{$this->getTable('rma/status')}` SET
	customer_message = REPLACE(customer_message, '[rma_items]', ''),
	admin_message = REPLACE(admin_message, '[rma_items]', ''),
	history_message = REPLACE(history_message, '[rma_items]', '');

";
$installer->run($sql);

/**                                    **/


$installer->endSetup();