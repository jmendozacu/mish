<?php
class OTTO_AdvancedFaq_Model_System_Config_Source_Bind
{
	const CONFIG_CL = "click";
	const CONFIG_DBL = "dblclick";
	const CONFIG_MOUSEO = "mouseover";
	const CONFIG_MOUSEE = "mouseenter";
	/**
	 * Options getter
	 *
	 * @return array
	 */
	public function toOptionArray()
	{
		return array(
				array('value' => self::CONFIG_CL, 'label'=>Mage::helper('adminhtml')->__('Click')),
				array('value' => self::CONFIG_DBL, 'label'=>Mage::helper('adminhtml')->__('DblClick')),
				array('value' => self::CONFIG_MOUSEO, 'label'=>Mage::helper('adminhtml')->__('Mouseover')),
				array('value' => self::CONFIG_MOUSEE, 'label'=>Mage::helper('adminhtml')->__('Mouseenter')),
		);
	}
}