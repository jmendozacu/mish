<?php
	
class Eadesigndev_Romcity_Block_Adminhtml_Romcity_Render_Cityname extends  Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
		public function render(Varien_Object $row) {		

			$region = Mage::getModel('directory/region')->load($row->getRegionId());
			echo $state_name = $region->getName();
		}
}
