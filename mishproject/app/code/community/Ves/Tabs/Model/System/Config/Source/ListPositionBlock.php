<?php

class Ves_Tabs_Model_System_Config_Source_ListPositionBlock
{	

	public function toOptionArray()
	{
		$output = array(
			array('value'=>'pull-left','label'=>Mage::helper('ves_tabs')->__('Left')),
			array('value'=>'pull-right','label'=>Mage::helper('ves_tabs')->__('Right')),
			);
		return $output ;
	}    
}
