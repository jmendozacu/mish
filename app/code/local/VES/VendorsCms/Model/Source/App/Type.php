<?php

class VES_VendorsCms_Model_Source_App_Type
{
	public function getOptions()
    {
    	$options = Mage::helper('vendorscms')->getFrontendAppTypes();
    	$newOptions = array();
    	foreach($options as $key=>$option){
    		/*Get helper*/
    		if(isset($option['@']) && isset($option['@']['module'])){
    			$helper = Mage::helper($option['@']['module']);
    		}else{
    			$helper = Mage::helper('core');
    		}
    		if(!isset($option['class'])) throw new Mage_Core_Exception(Mage::helper('vendorscms')->__('App type class is not defined for "%s"',$option['title']));
    		$model = Mage::app()->getLayout()->createBlock($option['class']);
    		if(!method_exists($model, 'isEnabled')) throw new Mage_Core_Exception(Mage::helper('vendorscms')->__('isEnabled method need to be defined for App type "%s (%s)"',$option['title'],get_class($model)));
    		if(!$model->isEnabled()) continue;
    		
    		$newOptions[$key] = $helper->__($option['title']);
    	}
    	return $newOptions;
    }
    
    public function getFormOptions(){
    	$options = $this->getOptions();
    	return array_merge(array(''=>Mage::helper('vendorscms')->__('-- Please Select --')),$options);;
    }
}