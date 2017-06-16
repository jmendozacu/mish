<?php
$installer = $this;
$installer->startSetup();
if (Mage::registry('mst_allow_drop_tables')) {
    $sql = "
       DROP TABLE IF EXISTS `{$this->getTable('kb/article')}`;
       DROP TABLE IF EXISTS `{$this->getTable('kb/article_store')}`;
       DROP TABLE IF EXISTS `{$this->getTable('kb/category')}`;
       DROP TABLE IF EXISTS `{$this->getTable('kb/article_category')}`;
       DROP TABLE IF EXISTS `{$this->getTable('kb/attachment')}`;
       DROP TABLE IF EXISTS `{$this->getTable('kb/tag')}`;
       DROP TABLE IF EXISTS `{$this->getTable('kb/article_tag')}`;
    ";
    $installer->run($sql);
}
$sql = "
CREATE TABLE `{$this->getTable('kb/article')}` (
    `article_id` int(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL DEFAULT '',
    `text` TEXT,
    `url_key` VARCHAR(255) NOT NULL DEFAULT '',
    `meta_title` VARCHAR(255) NOT NULL DEFAULT '',
    `meta_keywords` TEXT,
    `meta_description` TEXT,
    `is_active` TINYINT(1) NOT NULL DEFAULT 0,
    `user_id` ".(Mage::getVersion() >= '1.6.0.0'? 'int(10)': 'mediumint(11)')." unsigned,
    `votes_sum` FLOAT,
    `votes_num` INT(11),
    `created_at` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
    `updated_at` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
    KEY `fk_kb_article_user_id` (`user_id`),
    CONSTRAINT `mst_526560aa9a7e20fbaaf6eaaa238dfbd7` FOREIGN KEY (`user_id`) REFERENCES `{$this->getTable('admin/user')}` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE,
    PRIMARY KEY (`article_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `{$this->getTable('kb/article_store')}` (
    `article_store_id` int(11) NOT NULL AUTO_INCREMENT,
    `as_article_id` INT(11) NOT NULL,
    `as_store_id` SMALLINT(5) unsigned NOT NULL,
    KEY `fk_kb_article_store_article_id` (`as_article_id`),
    CONSTRAINT `mst_87529a866ab966b10c55b1b25251f613` FOREIGN KEY (`as_article_id`) REFERENCES `{$this->getTable('kb/article')}` (`article_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    KEY `fk_kb_article_store_store_id` (`as_store_id`),
    CONSTRAINT `mst_7ed67471b92f1645ad3c1d70ec170a25` FOREIGN KEY (`as_store_id`) REFERENCES `{$this->getTable('core/store')}` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    PRIMARY KEY (`article_store_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `{$this->getTable('kb/category')}` (
    `category_id` int(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL DEFAULT '',
    `url_key` VARCHAR(255) NOT NULL DEFAULT '',
    `meta_title` VARCHAR(255) NOT NULL DEFAULT '',
    `meta_keywords` TEXT,
    `meta_description` TEXT,
    `is_active` TINYINT(1) NOT NULL DEFAULT 0,
    `sort_order` SMALLINT(5) NOT NULL DEFAULT '0',
    `parent_id` INT(11),
    `path` VARCHAR(255) NOT NULL DEFAULT '',
    `level` INT(11),
    `position` INT(11),
    `children_count` INT(11),
    KEY `fk_kb_category_parent_id` (`parent_id`),
    CONSTRAINT `mst_85a11d5c0f66efe34d9c3380c315c440` FOREIGN KEY (`parent_id`) REFERENCES `{$this->getTable('kb/category')}` (`category_id`) ON DELETE SET NULL ON UPDATE CASCADE,
    PRIMARY KEY (`category_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `{$this->getTable('kb/article_category')}` (
    `article_category_id` int(11) NOT NULL AUTO_INCREMENT,
    `ac_article_id` INT(11) NOT NULL,
    `ac_category_id` INT(11) NOT NULL,
    KEY `fk_kb_article_category_article_id` (`ac_article_id`),
    CONSTRAINT `mst_dd65670777af6206bfe9067b11a21db5` FOREIGN KEY (`ac_article_id`) REFERENCES `{$this->getTable('kb/article')}` (`article_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    KEY `fk_kb_article_category_category_id` (`ac_category_id`),
    CONSTRAINT `mst_8530349a45f3502b3ccad568c2d1acff` FOREIGN KEY (`ac_category_id`) REFERENCES `{$this->getTable('kb/category')}` (`category_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    PRIMARY KEY (`article_category_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `{$this->getTable('kb/attachment')}` (
    `attachment_id` int(11) NOT NULL AUTO_INCREMENT,
    `article_id` INT(11) NOT NULL,
    `name` VARCHAR(255) NOT NULL DEFAULT '',
    `type` VARCHAR(255) NOT NULL DEFAULT '',
    `size` INT(11),
    `body` LONGBLOB,
    KEY `fk_kb_attachment_article_id` (`article_id`),
    CONSTRAINT `mst_aec61d99f2846621bd4c89b424e33409` FOREIGN KEY (`article_id`) REFERENCES `{$this->getTable('kb/article')}` (`article_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    PRIMARY KEY (`attachment_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `{$this->getTable('kb/tag')}` (
    `tag_id` int(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL DEFAULT '',
    `url_key` VARCHAR(255) NOT NULL DEFAULT '',
    PRIMARY KEY (`tag_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `{$this->getTable('kb/article_tag')}` (
    `article_tag_id` int(11) NOT NULL AUTO_INCREMENT,
    `at_article_id` INT(11) NOT NULL,
    `at_tag_id` INT(11) NOT NULL,
    KEY `fk_kb_article_tag_article_id` (`at_article_id`),
    CONSTRAINT `mst_e0343f67ad75d13070bcc36e1d0fe2be` FOREIGN KEY (`at_article_id`) REFERENCES `{$this->getTable('kb/article')}` (`article_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    KEY `fk_kb_article_tag_tag_id` (`at_tag_id`),
    CONSTRAINT `mst_f5573485630966638f3e2b2027c070b1` FOREIGN KEY (`at_tag_id`) REFERENCES `{$this->getTable('kb/tag')}` (`tag_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    PRIMARY KEY (`article_tag_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
ALTER TABLE `{$this->getTable('kb/article_tag')}` ADD UNIQUE INDEX `kb_article_tag` (`at_article_id`, `at_tag_id`);

";
$installer->run($sql);

/**                                    **/

$installer->endSetup();

Mage::getSingleton('kb/observer')->registerUrlRewrite();
Mage::getModel('kb/category')
    ->setData('id', 1)
    ->setName('Knowledge Base')
    ->setUrlKey('home')
    ->setIsActive(1)
    ->setPath(1)
    ->setLevel(1)
    ->save();
