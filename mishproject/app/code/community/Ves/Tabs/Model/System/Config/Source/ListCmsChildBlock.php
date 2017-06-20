<?php

class Ves_Tabs_Model_System_Config_Source_listCmsChildBlock
{	

    public function toOptionArray()
    {
		$collection = Mage::getModel('cms/block')->getCollection();
		$output = array();
		$output[] = array('value'=>'none', 'label'=> Mage::helper('ves_tabs')->__("None") );
		foreach( $collection as $cms ){
			$output[] = array('value'=>$cms->getId(), 'label'=>$cms->getTitle() );
		}
        return $output ;
    }    
}
