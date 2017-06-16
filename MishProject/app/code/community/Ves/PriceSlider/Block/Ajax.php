<?php 

class Ves_PriceSlider_Block_Ajax extends Mage_Core_Block_Template{
	protected $config = array();
	protected $selector_config = array();
	public function __construct($attributes = array()){
		
		parent::__construct( $attributes );

		$this->config = Mage::getStoreConfig('ves_priceslider');

		if(!$this->config['priceslider_setting']['show']) {
            return;
        }

		$this->url = Mage::getStoreConfig('web/unsecure/base_url');
		
		$this->selector_config = isset($this->config['ves_priceslider_conf'])?$this->config['ves_priceslider_conf']:array();

		$this->ajaxSlider = $this->config['ajax_conf']['slider'];
		$this->ajaxLayered = $this->config['ajax_conf']['layered'];
		$this->ajaxToolbar = $this->config['ajax_conf']['toolbar'];
		$this->overlayColor = $this->config['ajax_conf']['overlay_color'];
		$this->overlayOpacity = $this->config['ajax_conf']['overlay_opacity'];
		$this->loadingText = $this->config['ajax_conf']['loading_text'];
		$this->loadingTextColor = $this->config['ajax_conf']['loading_text_color'];
		$this->loadingImage = isset($this->config['ajax_conf']['loading_image'])?$this->config['ajax_conf']['loading_image']:"";


		if($this->loadingImage == '' || $this->loadingImage == null){
			$this->loadingImage = $this->url.'media/ves_priceslider/default/loader_32x32.gif';
		}else{
			$this->loadingImage = $this->url.'media/ves_priceslider/'.$this->loadingImage;
		}

	}

	public function getConfigValue($key, $default = "") {
		return isset($this->selector_config[$key])?$this->selector_config[$key]: $default;
	}

	public function getCallbackJs(){
		return Mage::getStoreConfig('ves_priceslider/ajax_conf/afterAjax');
	}
}