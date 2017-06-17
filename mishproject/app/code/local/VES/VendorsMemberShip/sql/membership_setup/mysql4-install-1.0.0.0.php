<?php

$installer = $this;
/* Add credit amount for vendor */

$this->addAttribute('ves_vendor', 'expiry_date', array(
        'type'              => 'static',
        'label'             => 'Expiry Date',
        'input'             => 'date',
		'frontend_input'	=> 'date',
        'class'             => '',
        'backend'           => '',
        'frontend'          => '',
        'source'            => '',
        'required'          => false,
        'user_defined'      => false,
        'unique'            => false,
));
$this->getConnection()->addColumn($this->getTable('vendors/vendor'), 'expiry_date', 'timestamp NULL AFTER updated_at');

/* Add New Attributes */
$setup = new Mage_Catalog_Model_Resource_Setup();

$setup->addAttribute('catalog_product', 'ves_vendor_related_group', array(
        'type'              => 'int',
        'label'             => 'Related Vendor Group',
        'input'             => 'select',
		'frontend_input'	=> 'select',
        'class'             => '',
        'backend'           => '',
        'frontend'          => '',
        'source'            => 'vendors/source_group',
        'required'          => true,
        'user_defined'      => false,
        'unique'            => false,
		'apply_to' 			=> 'virtual',
));


$setup->addAttribute('catalog_product', 'ves_vendor_period', array(
        'type'              => 'int',
        'label'             => 'Period',
		'note'				=> '12 => 12 months',
        'input'             => 'text',
		'frontend_input'	=> 'text',
        'class'             => '',
        'backend'           => '',
        'frontend'          => '',
        'source'            => '',
        'required'          => true,
        'user_defined'      => false,
        'unique'            => false,
		'apply_to' 			=> 'virtual',
));


/*Add new attribute set*/
$entityTypeId = Mage::getModel('catalog/product')->getResource()->getTypeId();
$model  = Mage::getModel('eav/entity_attribute_set')
            ->setEntityTypeId($entityTypeId);
$model->setAttributeSetName('Vendor Package');
$model->validate();
$model->save();


/*DO not add these attribute to new attribute set*/
$removeAttributeCodes = array('weight','news_from_date','news_to_date','vendor_sku','vendor_id','vendor_related_product','vendor_categories'
,'country_of_manufacture','approval','ves_vendor_featured','color');
$removeAttributes = Mage::getResourceModel('catalog/product_attribute_collection')->addFieldToFilter('attribute_code',array('in'=>$removeAttributeCodes));
$removeAttributeIds = $removeAttributes->getAllIds();


/*Copy group and attributes from default attribute set*/
$attributeSet = Mage::getModel("eav/entity_attribute_set")->getCollection()->addFieldToFilter('entity_type_id',$entityTypeId)->setOrder('attribute_set_id','asc')->getFirstItem();

$groups = Mage::getModel('eav/entity_attribute_group')
	->getResourceCollection()
	->setAttributeSetFilter($attributeSet->getId())
	->load();

$newGroups = array();
foreach ($groups as $group) {
	$newGroup = Mage::getModel('eav/entity_attribute_group');
	$newGroup->setAttributeSetId($model->getId())
			->setAttributeGroupName($group->getAttributeGroupName())
            ->setSortOrder($group->getSortOrder())
			->setDefaultId($group->getDefaultId());

	$groupAttributesCollection = Mage::getModel('eav/entity_attribute')
		->getResourceCollection()
		->setAttributeGroupFilter($group->getId())
		->load();

	$newAttributes = array();
	foreach ($groupAttributesCollection as $attribute) {
		if(in_array($attribute->getAttributeId(),$removeAttributeIds)) continue;
		$newAttribute = Mage::getModel('eav/entity_attribute')
			->setId($attribute->getId())
			//->setAttributeGroupId($newGroup->getId())
			->setAttributeSetId($model->getId())
			->setEntityTypeId($model->getEntityTypeId())
			->setSortOrder($attribute->getSortOrder());
		$newAttributes[] = $newAttribute;
	}
	$newGroup->setAttributes($newAttributes);
	$newGroups[] = $newGroup;
}

$model->setGroups($newGroups);
$model->save();

/*Add new Group */
$i = 1;
$newAttributes = array();

/*Add attributes to new group*/
foreach(array('ves_vendor_related_group','ves_vendor_period') as $attributeCode){
	$attribute = $setup->getAttribute($entityTypeId, $attributeCode);
	$newAttribute = Mage::getModel('eav/entity_attribute')
			->setId($attribute['attribute_id'])
			//->setAttributeGroupId($newGroup->getId())
			->setAttributeSetId($model->getId())
			->setEntityTypeId($model->getEntityTypeId())
			->setSortOrder($i++);
	$newAttributes[] = $newAttribute;
}

$modelGroup = Mage::getModel('eav/entity_attribute_group')
            ->setAttributeGroupName('Vendor Package Info')
            ->setAttributeSetId($model->getId())
            ->setSortOrder(1)
            ->setAttributes($newAttributes)
            ->save();

$installer->endSetup(); 