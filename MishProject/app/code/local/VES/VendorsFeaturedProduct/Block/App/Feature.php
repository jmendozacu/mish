<?php
class VES_VendorsFeaturedProduct_Block_App_Feature extends Mage_Core_Block_Template{
	public function isEnabled(){
		$result = new Varien_Object(array('is_enabled'=>true));
		Mage::dispatchEvent('ves_vendorsfeaturedproduct_app_type_feature',array('result'=>$result));
		return $result->getIsEnabled();
	}
	public function setApp($app){
		$this->setData('app',$app);
		$options = $app->getOptionsByCode('featureproduct_option');
		if(!sizeof($options)) return;
		$optionValue = json_decode($options[0]->getValue(),true);
		$this->setFeature($optionValue);
		return $this;
	}

	public function _beforeToHtml()
    {
    	Mage::helper('ves_core');
    	if($this->getFeature()){
    		$option = $this->getFeature();
    		$this->setTemplate($option['template']);
    		$cache_lifetime = $option['cache_time'];
	    	$cache_lifetime = ($cache_lifetime && is_numeric($cache_lifetime))?$cache_lifetime:24*60*60; 
	        $this->addData(array(
	            'cache_lifetime'    => $cache_lifetime,/* (s) */
	            'cache_tags'        => array(Mage_Catalog_Model_Product::CACHE_TAG),
	        ));
		}
    	return parent::_beforeToHtml();
    }
    
    public function getFeaturedProducts()
    {
    	Mage::helper('ves_core');
    	if($this->getFeature()){
    		$option = $this->getFeature();
    		$vender_id = Mage::registry('vendor')->getId();
    		return Mage::getModel('vendorsfeaturedproduct/featuredproduct')->getFeaturedProducts($vender_id,false,false,$option['sort_by'],$option['direct']);
    	}
    	
    }
	public function getColumnCount(){
		Mage::helper('ves_core');
		if($this->getFeature()){
			$option = $this->getFeature();
			return $option['colunms_num'];
		}
	}
}