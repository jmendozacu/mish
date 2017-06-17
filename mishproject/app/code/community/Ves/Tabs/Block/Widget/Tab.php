<?php
class Ves_Tabs_Block_Widget_Tab extends Ves_Tabs_Block_List implements Mage_Widget_Block_Interface
{
	/**
	 * Contructor
	 */
	public function __construct($attributes = array())
	{
		parent::__construct($attributes);

		//Convert widget config
		$thumb_width = $this->getData("thumbwidth_childblock");
		if($thumb_width) {
			$this->setConfig("thumbwidth_childblock", $thumb_width);
		}

		$thumb_height = $this->getData("thumbheight_childblock");
		if($thumb_height) {
			$this->setConfig("thumbheight_childblock", $thumb_height);
		}

		$source_products_mode = $this->getData("source_products_mode");
		if($source_products_mode) {
			$this->setConfig("source_products_mode", $source_products_mode);
		}

		/*Copy code from construct function of parent class*/
		if($this->hasData("template")) {
			$this->setTemplate($this->getData("template"));
		} else {

			$theme = $this->getConfig("theme", "default");
			$theme = $theme?$theme:"default";
			$template = $this->getConfig('ajax_type', "");
			$enable_owl = $this->getConfig("enable_owl_carousel");

			if($enable_owl && ( $template == 'sub-category' || $template == 'default' || $template == 'carousel' ) ){
				if($template == 'sub-category'){
					$this->setTemplate('ves/tabs/' . $theme . '/owl/ajax-sub-carousel.phtml');
				}

				if($template == 'default'){
					$this->setTemplate('ves/tabs/' . $theme . '/owl/list.phtml');
				}

				if($template == 'carousel'){
					$this->setTemplate('ves/tabs/' . $theme . '/owl/ajax-carousel.phtml');
				}

			}else{
				if($template == 'default'){
					$this->setTemplate('ves/tabs/' . $theme . '/list.phtml');
				}

				if($template == 'loadmore'){
					$this->setTemplate('ves/tabs/' . $theme . '/ajax-loadmore.phtml');
				}

				if($template == 'carousel'){
					$this->setTemplate('ves/tabs/' . $theme . '/ajax-carousel.phtml');
				}

				if(!$this->getConfig('enable_tab') && $template == 'append'){
					$this->setTemplate('ves/tabs/' . $theme . '/append-carousel.phtml');
				}

				if($this->getConfig('enable_tab') && $template == 'append'){
					$this->setTemplate('ves/tabs/' . $theme . '/ajaxtab-carousel.phtml');
				}

				if($template == 'sub-category'){
					$this->setTemplate('ves/tabs/' . $theme . '/ajax-sub-carousel.phtml');
				}

			}
		}

       
        
		if ($this->getConfig('ajax_type') == 'default') {
			/*Cache Block*/
			$enable_cache = $this->getConfig("enable_cache", 0 );
			if(!$enable_cache) {
				$cache_lifetime = null;
			} else {
				$cache_lifetime = $this->getConfig("cache_lifetime", 86400 );
				$cache_lifetime = (int)$cache_lifetime>0?$cache_lifetime: 86400;
			}

			$this->addData(array('cache_lifetime' => $cache_lifetime));
			$this->addCacheTag(array(
				Mage_Core_Model_Store::CACHE_TAG,
				Mage_Cms_Model_Block::CACHE_TAG,
				Ves_Tabs_Model_Product::CACHE_BLOCK_TAG
				));

			/*End Cache Block*/
		}

	}

	public function _toHtml() {

		$this->_show = $this->getConfig("show");

		if(!$this->_show) return;

		return parent::_toHtml();
	}
}