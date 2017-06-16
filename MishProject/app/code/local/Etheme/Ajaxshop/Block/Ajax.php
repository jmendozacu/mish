<?php 

class Etheme_Ajaxshop_Block_Ajax extends Mage_Core_Block_Template{
	public function __construct(){
		
		$this->config = Mage::getStoreConfig('ajax_shop');
		$this->url = Mage::getStoreConfig('web/unsecure/base_url');
		$this->ajaxSlider = $this->config['ajax_conf']['slider'];
		$this->ajaxLayered = $this->config['ajax_conf']['layered'];
		$this->ajaxToolbar = $this->config['ajax_conf']['toolbar'];
	}
}