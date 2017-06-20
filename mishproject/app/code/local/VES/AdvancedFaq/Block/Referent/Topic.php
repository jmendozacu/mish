<?php
class OTTO_AdvancedFaq_Block_Referent_Topic extends OTTO_AdvancedFaq_Block_Abstract
{
	public function _prepareLayout(){
		$checkenable=Mage::helper('advancedfaq')->isEnabled();
		//echo $config;exit;
		$tmp = OTTO_AdvancedFaq_Model_System_Config_Source_Yesno::CONFIG_YES;
		if(Mage::helper('advancedfaq')->getEnabelBlock()==$tmp && $checkenable==$tmp){
			$this->setTemplate("otto_advancedfaq/block/topic.phtml");
		}
		return parent::_prepareLayout();
	}
	
	/**
	 * Get all categories
	 * @return OTTO_Kbase_Model_Mysql4_Category_Collection
	 */
	public function getAllCategories(){
		$storeId = Mage::app()->getStore()->getId();
		$data = array();
		$con = array(
				array('finset'=>$storeId),
				array('finset'=>0),
		);
		$category = Mage::getModel("advancedfaq/category")->getCollection()->addFieldToFilter('status',array("eq"=>OTTO_AdvancedFaq_Model_Status::STATUS_ENABLED))->addFieldToFilter('store_id',$con);
		return $category;
	}
	
}