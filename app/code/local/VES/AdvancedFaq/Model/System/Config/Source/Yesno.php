<?php
class OTTO_AdvancedFaq_Model_System_Config_Source_Yesno
{
	const CONFIG_YES = 1;
	const CONFIG_NO = 0;

	/**
	 * Options getter
	 *
	 * @return array
	 */
	public function toOptionArray()
	{
		return array(
				array('value' => self::CONFIG_YES, 'label'=>Mage::helper('adminhtml')->__('Yes')),
				array('value' => self::CONFIG_NO, 'label'=>Mage::helper('adminhtml')->__('No')),
		);
	}

	/**
	 * Get options in "key-value" format
	 *
	 * @return array
	 */
	public function toArray()
	{
		return array(
				self::CONFIG_NO => Mage::helper('adminhtml')->__('No'),
				self::CONFIG_YES => Mage::helper('adminhtml')->__('Yes'),
		);
	}

}