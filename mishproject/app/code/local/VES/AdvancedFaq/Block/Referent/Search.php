<?php
class OTTO_AdvancedFaq_Block_Referent_Search extends OTTO_AdvancedFaq_Block_Search
{
	public function _prepareLayout(){
		$checkenable=Mage::helper('advancedfaq')->isEnabled();
		//echo $config;exit;
		$tmp = OTTO_AdvancedFaq_Model_System_Config_Source_Yesno::CONFIG_YES;
		if(Mage::helper('advancedfaq')->getEnableSearchBlock()==$tmp && $checkenable==$tmp){
			$this->setTemplate("otto_advancedfaq/block/search.phtml");
		}
		return parent::_prepareLayout();
	}
	

	
}