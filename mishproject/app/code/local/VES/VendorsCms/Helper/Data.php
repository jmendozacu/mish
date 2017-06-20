<?php

class VES_VendorsCms_Helper_Data extends Mage_Core_Helper_Abstract{
	/**
	 * Is Module Enable
	 */
	public function moduleEnable(){
		$result = new Varien_Object(array('module_enable'=>true));
		Mage::dispatchEvent('ves_vendorscms_module_enable',array('result'=>$result));
		return $result->getData('module_enable');
	}
	
	/**
	 * Check if the module is enabled
	 */
	public function isEnabledModule(){
		return $this->moduleEnable();
	}
	
	protected function _cmpOrder($a,$b){
		$aOrder = $a['sort_order']?$a['sort_order']:1000;
		$bOrder = $b['sort_order']?$b['sort_order']:1000;
		return  $aOrder > $bOrder;
	}
	
	/**
	 * Show BreadCrumbs
	 */
	public function showBreadCrumbs(){
		return Mage::helper('vendorsconfig')->getVendorConfig('design/config/show_cms_breadcrumbs',Mage::registry('vendor')->getId());
	}
	/**
	 * Get default cms home page
	 */
	public function getDefaultCmsHomePage(){
		return Mage::helper('vendorsconfig')->getVendorConfig('design/config/cms_home_page',Mage::registry('vendor')->getId());
	}
	/**
	 * Get frontend App types
	 */
	public function getFrontendAppTypes(){
		$typeConfig = Mage::getConfig()->getNode('vendors/ves_frontend_app')->asArray();
    	$options = array();
    	foreach($typeConfig as $key => $type){
    		/*Get helper*/
    		if(isset($type['@']) && isset($type['@']['module'])){
    			$helper = Mage::helper($type['@']['module']);
    		}else{
    			$helper = Mage::helper('core');
    		}
    		$type['title'] = $helper->__($type['title']);
    		$options[$key] = $type;
    	}
    	return $options;
	}
	/**
	 * Get app page group fron configuration
	 */
	public function getAppPageGroupConfig(){
		return Mage::getConfig()->getNode('global/app_page_groups')->asArray();
	}
	/**
	 * Get all app page groups from configuration
	 * @throws Mage_Core_Exception
	 */
	public function getAppPageGroups(){
		$config = $this->getAppPageGroupConfig();
		foreach($config as $key=>$value){$config[$key]['name'] = $key;}
		/*Sort by value sort_order*/
		usort($config, array($this,"_cmpOrder"));
		
		$pageGroups = array();
		foreach($config as $key=>$group){
			/*Get helper*/
    		if(isset($group['@']) && isset($group['@']['module'])){
    			$helper = Mage::helper($group['@']['module']);
    		}else{
    			$helper = Mage::helper('core');
    		}
    		if(!isset($group['class'])) throw new Mage_Core_Exception(Mage::helper('vendorscms')->__('App class is not defined for "%s" page group',$group['title']));
    		$model = Mage::getModel($group['class']);
    		if(!$model->isEnabled()) continue;
    		$pageGroup = array(
    			'name'		=> $group['name'],
    			'title'		=> $helper->__($group['title']),
    			'template'	=> $model->getHtmlTemplate(),
    			'sort_order'=> $group['sort_order'],
    		);
    		$pageGroups[] = $pageGroup;
		}
		return $pageGroups;
	}
	
	/**
	 * Process frontend app.
	 * 
	 * @param VES_VendorsCms_Model_App $app
	 * @param Mage_Core_Model_Layout $layout
	 */
	public function processFrontendApp(VES_VendorsCms_Model_App $app, Mage_Core_Model_Layout $layout,Mage_Core_Controller_Front_Action $action){
		$frontendInstanceOptions = $app->getFrontendInstanceOption();
		$pageGroups = $this->getAppPageGroupConfig();
		foreach($frontendInstanceOptions as $option){
			$optionData = json_decode($option->getValue(),true);
			if(isset($optionData['page_group']) && isset($pageGroups[$optionData['page_group']])){
				$pageGroup = $pageGroups[$optionData['page_group']];
				if(!isset($pageGroup['class'])) throw new Mage_Core_Exception(Mage::helper('vendorscms')->__('App class is not defined on "%s" page group',$pageGroup['title']));
				$model = Mage::getModel($pageGroup['class']);
				
				if(method_exists($model,'processApp')){
					$model->processApp($app, $optionData, $layout,$action);
				}
			}
		}
	}
	
	/**
	 * Get all filter notes
	 */
	public function getFilterNotes(){
		$filters = Mage::getConfig()->getNode('global/ves_filter')->asArray();
		$notes = '';
		foreach($filters as $key=>$filter){
			if(isset($filter['@']) && isset($filter['@']['module'])){
    			$helper = Mage::helper($filter['@']['module']);
    		}else{
    			$helper = Mage::helper('core');
    		}
    		$notes .= $helper->__($filter['note']).'<br />';
		}
		return $notes;
	}
}