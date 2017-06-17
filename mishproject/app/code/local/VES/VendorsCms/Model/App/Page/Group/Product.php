<?php
class VES_VendorsCms_Model_App_Page_Group_Product extends VES_VendorsCms_Model_App_Page_Group_Abstract
{
	public function isEnabled(){
		$result = new Varien_Object(array('is_enabled'=>true));
		Mage::dispatchEvent('ves_vendorscms_app_pagegroup_product',array('result'=>$result));
		return $result->getIsEnabled() && Mage::helper('vendorscms')->moduleEnable();
	}
	public function getAvailablePositions(){
    	return parent::getAvailablePositions();
    }
    /**
     * Validate product page.
     * @param Mage_Core_Controller_Front_Action $action
     */
    protected function _validate(Mage_Core_Controller_Front_Action $action){
    	if(!$this->isEnabled()) return false;
		if($action->getFullActionName() != 'vendorspage_product_view') return false;
		
		if(!Mage::registry('current_product')) return false;
		return true;
    }
    
    /**
     * Process APP
     * @param unknown_type $app
     * @param unknown_type $options
     * @param unknown_type $layout
     * @param unknown_type $action
     */
	protected function _processApp(VES_VendorsCms_Model_App $app, $options = array(),Mage_Core_Model_Layout $layout,Mage_Core_Controller_Front_Action $action, Varien_Object $forceBreak){
		if(isset($options['conditions'])){
			$rule = Mage::getModel('catalogrule/rule');
			$rule->setConditionsSerialized($options['conditions']);
			if(!$rule->getConditions()->validate(Mage::registry('current_product'))) return;
		}  
		$frontendAppTypes = Mage::helper('vendorscms')->getFrontendAppTypes();
		if(isset($options['position']) && isset($frontendAppTypes[$app->getType()])){
			$frontendAppType = $frontendAppTypes[$app->getType()];
			$block = $layout->createBlock($frontendAppType['class'],'frontend_app_'.$app->getId());
			$block->setApp($app);
			$layout->getBlock($options['position'])->append($block,'frontend_app_alias_'.$app->getId());
		}
	}
	
	/**
	 * Process frontend app
	 * @param VES_VendorsCms_Model_App $app
	 * @param array $options
	 * @param Mage_Core_Model_Layout $layout
	 */
	public function processApp(VES_Vblock_Model_App $app, $options = array(),Mage_Core_Model_Layout $layout,Mage_Core_Controller_Front_Action $action, Varien_Object $forceBreak){
		if($this->_validate($action)){
			$this->_processApp($app, $options, $layout, $action, $forceBreak);
		}
	}
	
    public function getHtmlTemplate(){
    	return '
    		<div id="product_page_{{number}}">
	    		<table cellspacing="0" class="option-header">
	    			<colgroup>
		    			<col width="300" />
		    			<col width="220" />
		    			<col />
	    			</colgroup>
	    			<thead>
	    				<tr>
	    					<th style="display: none;"><label>'.Mage::helper('vendorscms')->__('Product Rule').'</label></th>
	    					<th><label>'.Mage::helper('vendorscms')->__('Block Reference').' <span class="required">*</span></label></th>
	    					<th>&nbsp;</th>
	    				</tr>
	    			</thead>
	    			<tbody>
	    				<tr>
		    				<td class="rule-container" style="display: none;">'
		    				.$this->getProductRuleHtml().	
		    				'</td>
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
    		</div>
    	';
    }
    
    public function getProductRuleHtml(){
    	$form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('rule_');

        $renderer = Mage::getBlockSingleton('adminhtml/widget_form_renderer_fieldset')
            ->setTemplate('ves_vendorscms/promo/fieldset.phtml')
            ->setNewChildUrl(Mage::helper('adminhtml')->getUrl('*/*/newConditionHtml/form/rule_conditions_fieldset_{{number}}/number/{{number}}'));

        $fieldset = $form->addFieldset('conditions_fieldset_{{number}}', array(
            'legend'=>Mage::helper('catalogrule')->__('Conditions (leave blank for all products)'))
        )->setRenderer($renderer);

        $fieldset->addField('conditions_{{number}}', 'text', array(
            'name' => 'conditions[{{number}}]',
            'label' => Mage::helper('catalogrule')->__('Conditions'),
            'title' => Mage::helper('catalogrule')->__('Conditions'),
            'required' => true,
        ))->setRule(Mage::getModel('vendorscms/rule'))->setRenderer(Mage::getBlockSingleton('rule/conditions'));
        return $form->toHtml();
    }
}