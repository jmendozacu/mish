<?php
class OTTO_AdvancedFaq_Model_System_Config_Source_Speed
{
	const CONFIG_SLOW = "slow";
	const CONFIG_NORMAL = "normal";
	const CONFIG_FAST = "fast";
	
	/**
	 * Options getter
	 *
	 * @return array
	 */
	public function toOptionArray()
	{
		return array(
				array('value' => self::CONFIG_SLOW, 'label'=>Mage::helper('adminhtml')->__('Slow')),
				array('value' => self::CONFIG_NORMAL, 'label'=>Mage::helper('adminhtml')->__('Normal')),
				array('value' => self::CONFIG_FAST, 'label'=>Mage::helper('adminhtml')->__('Fast')),
		);
	}
}