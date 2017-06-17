<?php 
$installer = $this;
/* Add New Attributes */
$setup = new Mage_Catalog_Model_Resource_Setup();
$setup->updateAttribute('catalog_product', 'ves_vendor_related_group', 'apply_to','membership');
$setup->updateAttribute('catalog_product', 'ves_vendor_period', 'apply_to','membership');