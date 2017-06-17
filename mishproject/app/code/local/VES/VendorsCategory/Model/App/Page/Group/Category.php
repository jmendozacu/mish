<?php
class VES_VendorsCategory_Model_App_Page_Group_Category extends VES_VendorsCms_Model_App_Page_Group_Abstract
{
	public function isEnabled(){
		$result = new Varien_Object(array('is_enabled'=>true));
		Mage::dispatchEvent('ves_vendorscategory_app_pagegroup_category',array('result'=>$result));
		return $result->getIsEnabled() && Mage::helper('vendorscategory')->moduleEnable();
	}
	public function getAvailablePositions(){
    	return array(
    		''							=> Mage::helper('vendorscms')->__('-- Please Select --'),
	    	'left'						=> Mage::helper('vendorscms')->__('Left Column'),
    		'content'					=> Mage::helper('vendorscms')->__('Main Content Area'),
	    	'category.top.content'		=> Mage::helper('vendorscms')->__('Category Top Main Content Area'),
    		'category.bottom.content'	=> Mage::helper('vendorscms')->__('Category Bottom Main Content Area'),
	    	'top.menu'					=> Mage::helper('vendorscms')->__('Top Navigation Bar'),
	    	'before_body_end'			=> Mage::helper('vendorscms')->__('Page Bottom'),
	    	'bottom.container'			=> Mage::helper('vendorscms')->__('Page Footer'),
	    	'top.container'				=> Mage::helper('vendorscms')->__('Page Header'),
	    	'after_body_start'			=> Mage::helper('vendorscms')->__('Page Top'),
	    	'right'						=> Mage::helper('vendorscms')->__('Right Column'),
    	);
    }
    
	/**
	 * Process frontend app
	 * @param VES_VendorsCms_Model_App $app
	 * @param array $options
	 * @param Mage_Core_Model_Layout $layout
	 */
	public function processApp(VES_VendorsCms_Model_App $app, $options = array(),Mage_Core_Model_Layout $layout){
		if(!$this->isEnabled()) return;
		
		$currentCategory = Mage::registry('current_vendor_category');
		if(!$currentCategory) return;
		
		if(!in_array('all', $options['category']) && !in_array($currentCategory->getId(), $options['category'])) return;
		
		$frontendAppTypes = Mage::helper('vendorscms')->getFrontendAppTypes();
		if(isset($options['position']) && isset($frontendAppTypes[$app->getType()])){
			$frontendAppType = $frontendAppTypes[$app->getType()];
			if(!isset($frontendAppType['class'])) throw new Mage_Core_Exception(Mage::helper('vendorscms')->__('Frontend app type class is not defined for "%s"',$frontendAppType['title']));
			$block = $layout->createBlock($frontendAppType['class'],'frontend_app_'.$app->getId());
			$block->setApp($app);
			$layout->getBlock($options['position'])->append($block,'frontend_app_alias_'.$app->getId());
		}
	}
	
    public function getHtmlTemplate(){
    	return '
    		<div id="category_page_{{number}}">
	    		<table cellspacing="0" class="option-header">
	    			<colgroup>
		    			<col width="300" />
		    			<col width="220" />
		    			<col />
	    			</colgroup>
	    			<thead>
	    				<tr>
	    					<th><label>'.Mage::helper('vendorscms')->__('Categories').'</label></th>
	    					<th><label>'.Mage::helper('vendorscms')->__('Block Reference').' <span class="required">*</span></label></th>
	    					<th>&nbsp;</th>
	    				</tr>
	    			</thead>
	    			<tbody>
	    				<tr>
		    				<td>
		    					<!--
		    					<input type="radio" checked="checked" onclick="WidgetInstance.togglePageGroupChooser(this)" value="all" name="frontend_instance[{{number}}][for]" id="all_categories_{{number}}" class="radio for_all">&nbsp;<label for="all_categories_{{number}}">'.Mage::helper('vendorscms')->__('All').'</label>&nbsp;&nbsp;&nbsp;
		    					<input type="radio" onclick="WidgetInstance.togglePageGroupChooser(this)" value="specific" name="frontend_instance[{{number}}][for]" id="specific_categories_{{number}}" class="radio for_specific">&nbsp;<label for="specific_categories_{{number}}">'.Mage::helper('vendorscms')->__('Specific Categories').'</label>
		    					-->
		    					<select id="frontend_instance_{{number}}_category" multiple="multiple" size="5" name="frontend_instance[{{number}}][category][]">'
		    					.$this->getCategoryOptions().
		    					'</select>
		    				</td>
		    				<td>
		    					<div class="block_reference_container">
			    					<div class="block_reference">
				    					<select onchange="" title="" class="required-entry select" id="frontend_instance_{{number}}_position" name="frontend_instance[{{number}}][position]">'
				    					.$this->getAvailablePositionOptions().
				    					'</select>
			    					</div>
		    					</div>
		    				</td>
	    				</tr>
	    			</tbody>
	    		</table>
	    		<div id="categories_ids_{{number}}" class="chooser_container no-display" style="display: none;">
	    		</div>
    		</div>
    	';
    }
    
    public function getCategoryOptions(){
    	$options = Mage::getModel('vendorscategory/source_category')->toTreeArray(Mage::getSingleton('vendors/session')->getVendor()->getId());
    	$html = '<option value="all">'.Mage::helper('vendorscategory')->__('-- All Categories --').'</option>';
    	foreach($options as $key=>$option){
    		$html .= '<option value="'.$key.'">'.$option.'</option>';
    	}
    	return $html;
    }
}