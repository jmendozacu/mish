<?php
include_once Mage::getBaseDir('code').'/local/OTTO/AdvancedFaq/lib/recaptchalib.php';
class OTTO_AdvancedFaq_Helper_Recapcha extends Mage_Core_Helper_Abstract
{
	public function getEnableRecapchaConfig(){
		return Mage::getStoreConfig('advancedfaq/recapcha/enable_recapcha');
	}
	public function getPublickeyConfig(){
		return Mage::getStoreConfig('advancedfaq/recapcha/public_key');
	}
	public function getPrivatekeyConfig(){
		return Mage::getStoreConfig('advancedfaq/recapcha/private_key');
	}
	public function getRecapchaHtml(){
		return recaptcha_get_html ($this->getPublickeyConfig());
	}
	public function getPrivateKey(){
	 return $this->getPrivatekeyConfig();
	}
	public function getThemeOption(){
		return Mage::getStoreConfig('advancedfaq/recapcha/theme_recapcha');
	}
	public function getLangugageOption(){
		return Mage::getStoreConfig('advancedfaq/recapcha/language_recapcha');
	}
	public function getEnableRecapchar(){
		if($this->getPublickeyConfig() != "" && $this->getPrivatekeyConfig() != ""){
			if($this->getEnableRecapchaConfig()) {
				return true;
			}
			else{
					return false;
			}
		}
		return false;
	}
}
