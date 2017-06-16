
<?php
$installer = $this;
$installer->startSetup();
$objCatalogEavSetup = new  VES_VendorsRma_Model_Resource_Setup();


$objCatalogEavSetup->addAttribute(VES_VendorsRma_Model_Request::ENTITY,'reason_detail', array(
    'type'              => 'static',
    'label'             => 'Reason Detail',
    'input'             => 'text',
    'class'             => '',
    'backend'           => '',
    'frontend'          => '',
    'source'        	=> '',
    'required'          => false,
    'user_defined'      => false,
    'default'           => '',
    'unique'            => false,
));


$objCatalogEavSetup->getConnection()->addColumn($this->getTable('vendorsrma/request'), 'reason_detail', 'text DEFAULT NULL');
$installer->endSetup();
