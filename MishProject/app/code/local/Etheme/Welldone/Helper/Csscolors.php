<?php

class Etheme_Welldone_Helper_Csscolors extends Mage_Core_Helper_Abstract
{
	public function getCss_Colors()
	{
		return 'css/colors/colors_' . Mage::app()->getStore()->getCode() . '.css';
	}
}
