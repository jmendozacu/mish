<?php
/**
 *
 * @author		VnEcoms Team <support@vnecoms.com>
 * @website		http://www.vnecoms.com
 */
class VES_VendorsCategory_Model_Observer
{
	/**
	 * Add category block tab on app edit page.
	 * @param Varien_Event_Observer $observer
	 */
	public function ves_vendorscms_cms_app_add_tab_category_navigation(Varien_Event_Observer $observer){
		$tabsBlock = $observer->getEvent()->getTabs();
		$optionBlock = $tabsBlock->getLayout()->createBlock('vendorscategory/app_edit_tab_category','cms_app_edit_tab_category');
		$tabsBlock->addTab('frontend_app_options', $optionBlock);
	}
	/**
	 * Save Block app option 
	 * @param Varien_Event_Observer $observer
	 */
	public function vendorscms_app_save_after(Varien_Event_Observer $observer){
		$app 		= $observer->getEvent()->getApp();
		$request 	= $observer->getEvent()->getRequest();
		if($data = $request->getPost()){
			/*Save frontend instances*/
			if(isset($data['category_option'])){
				$blockAppOptionData = $data['category_option'];
				
				$option = Mage::getModel('vendorscms/appoption');
				$data = array(
					'app_id'	=> $app->getId(),
					'code'		=> 'category_option',
				);
				$option->setData($data);
				if($blockAppOptionData['option_id']){
					$option->setId($blockAppOptionData['option_id']);
				}
				unset($blockAppOptionData['option_id']);
								
				$option->setData('value',json_encode($blockAppOptionData));
				$option->save();
			}
		}
	}
	
	public function ves_vendorsproduct_product_edit_tabs_prepare_after(Varien_Event_Observer $ob) {
		if(!Mage::helper('vendorscategory')->moduleEnable()) return;
		
		$tabBlock 	= $ob->getTabsblock();
		$layout 	= Mage::app()->getLayout();
		$request 	= Mage::app()->getFrontController()->getRequest();
		if(Mage::registry('product')->getId() || ($request->getParam('set') && $request->getParam('type'))){
			/*$tabBlock->addTabAfter('vendor_categories', array(
					'label'     => Mage::helper('vendorsproduct')->__('Categories'),
					'content'   => $layout->createBlock('vendorscategory/vendor_product_edit_tab_categories')->toHtml(),
			),'categories');*/

            $tabBlock->addTabAfter('vendor_categories', array(
                'label'     => Mage::helper('vendorsproduct')->__('Categories'),
                'content'   => $layout->createBlock('vendorscategory/vendor_product_edit_tab_categories1')->toHtml(),
            ),'categories');
		}
	}
	
    public function ves_vendorsproduct_prepare_form(Varien_Event_Observer $ob) {
    	$fieldset = $ob->getFieldset();
    	$fieldset->removeField('vendor_categories');
    }
    
    public function ves_vendorsproduct_before_save(Varien_Event_Observer $ob) {
    	$product = $ob->getModel();
    	$data = Mage::app()->getFrontController()->getRequest()->getParams();
    	//$product->setData('vendor_categories',array('1,2,3'));
    }
    
    public function adminhtml_catalog_product_edit_prepare_form(Varien_Event_Observer $observer){
    	$form = $observer->getForm();
    	//$vendor_id = $form->getElement('vendor_id')->getValue();
		if($form->getElement('vendor_id')) {
			$vendor = $form->getElement('vendor_id')->getValue();
		}
		if($form->getElement('vendor_categories')) {
			$source = new VES_VendorsCategory_Model_Source_Category();
			$values = $source->getTreeOptions($vendor);
			$form->getElement('vendor_categories')->setValues($values);
		}

    }
    
    
    /**
     * observer when system config vendors_section saved
     */
    public function admin_system_config_changed_section_vendors(Varien_Event_Observer $ob) {
    	$old_url_key = Mage::getSingleton('core/session')->getData('current_url_key');
    	$current_url_key = Mage::getStoreConfig('vendors/vendor_page/url_key');

    	if($old_url_key != $current_url_key) {
    		$process = Mage::getSingleton('index/indexer')->getProcessByCode('vendor_catalog_indexer');
    		$process->setStatus(Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX);
    		$process->save();
    	}
    }
    
    /**
     * event before go to controller
     * @param Varien_Event_Observer $ob
     */
    public function controller_action_predispatch_adminhtml_system_config_edit(Varien_Event_Observer $ob) {
    	$controller = $ob->getControllerAction();
    	if(Mage::app()->getRequest()->getParam('section') == 'vendors') {
    		$session = Mage::getSingleton('core/session');
    		$session->setData('current_url_key', Mage::getStoreConfig('vendors/vendor_page/url_key'));
    	}
    }
    /**
     * Check if vendor has permission to see category menu or not.
     * @param Varien_Event_Observer $observer
     */
    public function ves_vendor_menu_check_acl(Varien_Event_Observer $observer){
    	$resource 	= $observer->getResource();
    	$result 	= $observer->getResult();
    	if($resource == 'vendors/catalog/category' && !Mage::helper('vendorscategory')->moduleEnable()){
    		$result->setIsAllowed(false);
    	}
    }
}