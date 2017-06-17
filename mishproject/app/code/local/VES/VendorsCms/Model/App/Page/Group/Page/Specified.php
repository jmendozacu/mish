<?php
class VES_VendorsCms_Model_App_Page_Group_Page_Specified extends VES_VendorsCms_Model_App_Page_Group_Abstract
{
	public function isEnabled(){
		$result = new Varien_Object(array('is_enabled'=>true));
		Mage::dispatchEvent('ves_vendorscms_app_pagegroup_specified',array('result'=>$result));
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
		switch ($options['page']){
			case 'page_empty':
				if($layout->getBlock('root')->getTemplate()!='page/empty.phtml') return;
				break;
			case 'page_one_column':
				if($layout->getBlock('root')->getTemplate()!='page/1column.phtml') return;
				break;
			case 'page_three_columns':
				if($layout->getBlock('root')->getTemplate()!='page/3columns.phtml') return;
				break;
			case 'page_two_columns_left':
				if($layout->getBlock('root')->getTemplate()!='page/2columns-left.phtml') return;
				break;
			case 'page_two_columns_right':
				if($layout->getBlock('root')->getTemplate()!='page/2columns-right.phtml') return;
				break;
			case 'vendor_page':
				if(!Mage::registry('is_vendor_homepage')) return;
				break;
			case 'cms_page':
				if(!Mage::registry('vendorscms_page')) return;
				break;
			default: return;
		}
		$frontendAppTypes = Mage::helper('vendorscms')->getFrontendAppTypes();
		if(isset($options['position']) && isset($frontendAppTypes[$app->getType()])){
			$frontendAppType = $frontendAppTypes[$app->getType()];
			if(!isset($frontendAppType['class'])) throw new Mage_Core_Exception(Mage::helper('vendorscms')->__('Frontend app type class is not defined for "%s"',$frontendAppType['title']));
			$block = $layout->createBlock($frontendAppType['class'],'frontend_app_'.$app->getId());
			$block->setApp($app);
			$layout->getBlock($options['position'])->append($block,'frontend_app_alias_'.$app->getId());
		}
	}
	
	public function getAvailablePages(){
    	return array(
    		''						=> Mage::helper('vendorscms')->__('-- Please Select --'),
	    	'page_empty'			=> Mage::helper('vendorscms')->__('All Empty Layout Pages'),
    		'page_one_column'		=> Mage::helper('vendorscms')->__('All One-Column Layout Pages'),
	    	'page_three_columns'	=> Mage::helper('vendorscms')->__('All Three-Column Layout Pages'),
	    	'page_two_columns_left'	=> Mage::helper('vendorscms')->__('All Two-Column Layout Pages (Left Column)'),
	    	'page_two_columns_right'=> Mage::helper('vendorscms')->__('All Two-Column Layout Pages (Right Column)'),
	    	'vendor_page'			=> Mage::helper('vendorscms')->__('Home Page'),
	    	'cms_page'				=> Mage::helper('vendorscms')->__('CMS Pages (All)'),
    	);
    }
    
    public function getAvailablePageOptions(){
    	$html = '';
    	foreach($this->getAvailablePages() as $key=>$position){
    		$html .='<option value="'.$key.'">'.$position.'</option>';
    	}
    	return $html;
    }
	public function getHtmlTemplate(){
    	return '
    		<div id="specified_page_{{number}}">
	    		<table cellspacing="0" class="option-header">
	    			<colgroup>
		    			<col width="200" />
		    			<col width="220" />
		    			<col width="320" />
		    			<col />
	    			</colgroup>
	    			<thead>
	    				<tr>
	    					<th><label>'.Mage::helper('vendorscms')->__('Page').'</label></th>
	    					<th><label>'.Mage::helper('vendorscms')->__('Block Reference').' <span class="required">*</span></label></th>
	    					<th>&nbsp;</th>
	    				</tr>
	    			</thead>
	    			<tbody>
	    				<tr>
		    				<td>
		    					<select onchange="" title="" class="required-entry select" id="frontend_instance_{{number}}_page" name="frontend_instance[{{number}}][page]">'
		    					.$this->getAvailablePageOptions().
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
}