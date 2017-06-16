<?php
/**
 * Adminhtml sales shipments block
 *
 * @category   	VES
 * @package    	VES_Vendors
 * @author    	Vnecoms Team <support@vnecoms.com>
 */

class VES_VendorsReport_Block_Vendor_Report_Filter_Form_Order extends Mage_Sales_Block_Adminhtml_Report_Filter_Form_Order
{
	protected function _prepareForm()
    {
        parent::_prepareForm();
        $form = $this->getForm();
        $htmlIdPrefix = $form->getHtmlIdPrefix();
        /** @var Varien_Data_Form_Element_Fieldset $fieldset */
        $fieldset = $this->getForm()->getElement('base_fieldset');

        if (is_object($fieldset) && $fieldset instanceof Varien_Data_Form_Element_Fieldset) {

            $fieldset->removeField('report_type');

        }

        return $this;
    }
}
