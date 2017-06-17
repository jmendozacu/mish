<?php
/*
	Frontend_Base_Default_Template_QrMage_Qrcode
*/

class Eisbehr_QrMage_Block_Qrcode extends Mage_Core_Block_Template
{
	public $_helper;
	
	protected function _construct()
	{
		$this->_helper = Mage::helper('qrmage/config');
		return $this;
	}
	
	protected function getImageHtml()
	{
		$config = Mage::helper('qrmage/config');
		
		$url      = Mage::helper('core/url')->getCurrentUrl();
		$engine   = $config->getEngine();
		$size     = $config->getGoogleSize();
		$level    = $config->getLevel();
		$margin   = $config->getGoogleMargin();
		$encoding = $config->getGoogleEncoding();
		$label    = $config->getLabel();
		
		$helper = Mage::helper('qrmage');
		$html   = $helper->setUrl($url)
						 ->setEngine($engine)
						 ->setSize($size)
						 ->setLevel($level)
						 ->setMargin($margin)
						 ->setEncoding($encoding)
						 ->setLabel($label)
						 ->getQrImageHtml();
		
		echo $html;
		return;
	}
}