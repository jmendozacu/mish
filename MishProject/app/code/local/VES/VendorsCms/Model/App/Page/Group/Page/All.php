<?php
class VES_VendorsCms_Model_App_Page_Group_Page_All extends VES_VendorsCms_Model_App_Page_Group_Abstract
{
	public function isEnabled(){
		$result = new Varien_Object(array('is_enabled'=>true));
		Mage::dispatchEvent('ves_vendorscms_app_pagegroup_all',array('result'=>$result));
		return $result->getIsEnabled() && Mage::helper('vendorscms')->moduleEnable();
	}
	/**
	 * Process frontend app
	 * @param VES_VendorsCms_Model_App $app
	 * @param array $options
	 * @param Mage_Core_Model_Layout $layout
	 */
	public function processApp(VES_VendorsCms_Model_App $app, $options = array(),Mage_Core_Model_Layout $layout){
		if(!$this->isEnabled()) return;
		$frontendAppTypes = Mage::helper('vendorscms')->getFrontendAppTypes();
		if(isset($options['position']) && isset($frontendAppTypes[$app->getType()])){
			$frontendAppType = $frontendAppTypes[$app->getType()];
			if(!isset($frontendAppType['class'])) throw new Mage_Core_Exception(Mage::helper('vendorscms')->__('Frontend app type class is not defined for "%s"',$frontendAppType['title']));
			$block = $layout->createBlock($frontendAppType['class'],'frontend_app_'.$app->getId());
			$block->setApp($app);
			$parentBlock = $layout->getBlock($options['position']);
			if($parentBlock) $parentBlock->insert($block,'',true);
		}
	}
	
	/**
	 * (non-PHPdoc)
	 * @see VES_VendorsCms_Model_App_Page_Group_Abstract::getHtmlTemplate()
	 */
    public function getHtmlTemplate(){
    	return '
    		<div id="all_page_{{number}}">
	    		<table cellspacing="0" class="option-header">
	    			<colgroup>
		    			<col width="200" />
		    			<col width="220" />
		    			<col width="320" />
		    			<col />
	    			</colgroup>
	    			<thead>
	    				<tr>
	    					<th><label>'.Mage::helper('vendorscms')->__('Block Reference').' <span class="required">*</span></label></th>
	    					<th>&nbsp;</th>
	    				</tr>
	    			</thead>
	    			<tbody>
	    				<tr>
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
}