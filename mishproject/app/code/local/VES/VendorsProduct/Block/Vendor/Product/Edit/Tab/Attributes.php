<?php
/**
 * Customer edit block
 *
 * @category   VES
 * @package    VES_Vendors
 * @author     Vnecoms Team <support@vnecoms.com>
 */
class VES_VendorsProduct_Block_Vendor_Product_Edit_Tab_Attributes extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Attributes
{
	public function getNotAllowedAttributes(){
		return Mage::helper('vendorsproduct')->getRestrictionProductAttribute();
	}
	
    protected function _prepareForm(){
    	parent::_prepareForm();
    	
    	$form 		= $this->getForm();
    	$group 		= $this->getGroup();

    	$fieldset	= $form->getElement('group_fields' . $group->getId());
    	/*Remove not allowed attributes from edit product page of vendor panel*/
    	$attributeCodes = $this->getNotAllowedAttributes();
    	foreach($attributeCodes as $attrCode){
    		$fieldset->removeField($attrCode);
    	}
    	
    	$tierPrice = $form->getElement('tier_price');
       	if ($tierPrice) {
        	$tierPrice->setRenderer(
        		$this->getLayout()->createBlock('vendorsproduct/vendor_product_edit_tab_price_tier')
        	);
        }
    	Mage::dispatchEvent('ves_vendorsproduct_prepare_form',array('fieldset'=>$fieldset));
    }
}
