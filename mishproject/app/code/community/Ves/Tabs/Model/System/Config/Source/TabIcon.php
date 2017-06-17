<?php


class Ves_Tabs_Model_System_Config_Source_TabIcon
{
	public function toOptionArray()
	{
		return array(
			array('value'=>'', 'label'=>Mage::helper('ves_tabs')->__('Please Select')),
			array('value'=>'image', 'label'=>Mage::helper('ves_tabs')->__('Image')),
			array('value'=>'background', 'label'=>Mage::helper('ves_tabs')->__('Background'))
			);
	}    
}
