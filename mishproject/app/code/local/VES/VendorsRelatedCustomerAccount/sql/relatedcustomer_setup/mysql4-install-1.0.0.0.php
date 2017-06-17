<?php

$installer = $this;
/* Add credit amount for vendor */
$this->addAttribute('ves_vendor', 'customer_id', array(
        'type'              => 'int',
        'label'             => 'Related Customer ID',
        'input'             => 'hidden',
        'class'             => '',
        'backend'           => '',
        'frontend'          => '',
        'source'            => '',
        'required'          => false,
        'user_defined'      => false,
        'default'           => 0,
        'unique'            => false,
));
$installer->endSetup(); 