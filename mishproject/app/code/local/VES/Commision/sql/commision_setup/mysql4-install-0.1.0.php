<?php

$installer = $this;

//code to add custom category attribute starts here 
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->startSetup();
 
$setup->addAttribute('catalog_category', 'set_commission', array(
    'group'         => 'Category Commission',
    'input'         => 'text',
    'type'          => 'varchar',
    'label'         => 'Set Commission',          
    'backend'       => '',
    'visible'       => 1,    
    'required'        => 1,
    'user_defined' => 1,    
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,       
));
 

//code to add custom category attribute ends here 


$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('commision')};
CREATE TABLE {$this->getTable('commision')} (
  `commision_id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `filename` varchar(255) NOT NULL default '',
  `content` text NOT NULL default '',
  `status` smallint(6) NOT NULL default '0',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`commision_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 