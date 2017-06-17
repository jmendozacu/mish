<?php

class Ves_Tabs_Model_System_Config_Source_ListAjaxType
{

	public function toOptionArray()
	{
		$output = array(
			array('value'=>'default','label'=>Mage::helper('ves_tabs')->__('None')),
			array('value'=>'loadmore','label'=>Mage::helper('ves_tabs')->__('Load more')),
			array('value'=>'carousel','label'=>Mage::helper('ves_tabs')->__('Carousel')),
			array('value'=>'append','label'=>Mage::helper('ves_tabs')->__('Append Block')),
			array('value'=>'sub-category','label'=>Mage::helper('ves_tabs')->__('Sub Category Block')),
			);
		return $output ;
	}
}