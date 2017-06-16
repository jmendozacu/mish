<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Lof
 * @package     Lof_Slider
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Banner base block
 *
 * @category    Lof
 * @package     Lof_Slider
 * @author    LandOfCoder (landofcoder@gmail.com)
 */

class Ves_Collection
{
	var $_items = array();
	public function __construct() {
		$this->_items = array();
	}
	public function addItem( $model = null ) {
		if($model ) {
			$this->_items[] = $model;
		}
		return $this;
	}

	public function getItems() {
		return $this->_items;
	}

	public function getSize() {
		return count($this->_items);
	}
}
class Ves_Tabs_Block_List extends Mage_Catalog_Block_Product_Abstract
{
	/**
	 * @var string $_config
	 *
	 * @access protected
	 */
	protected $_config = '';

	/**
	 * @var string $_config
	 *
	 * @access protected
	 */
	protected $_listDesc = array();

	/**
	 * @var string $_config
	 *
	 * @access protected
	 */
	protected $_theme = "";
	protected $_categories = array();
	protected $_attributes = array();


	public function __construct($attributes = array()) {

		$this->_attributes = $attributes;
		$helper =  Mage::helper('ves_tabs/data');
		$this->_config = $helper->get($attributes);
		$this->convertAttributesToConfig($this->_config);


		$show = $this->getConfig("show");
		if(!$show) return;

		parent::__construct();

		if($this->hasData("template")) {
			$this->setTemplate($this->getData("template"));
		} else {

			$theme = $this->getConfig("theme", "default");
			$template = $this->getConfig('ajax_type', "");

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
				$this->setTemplate('ves/tabs/' . $theme . '/ajaxtab-sub-carousel.phtml');
			}

		}

		

		$ajax_type = $this->getConfig('ajax_type');
		if ($ajax_type == 'default' || !$ajax_type) {
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


    /**
     * get value of the extension's configuration
     *
     * @return string
     */
    public function getConfig( $key, $default = "", $panel='ves_tabshome'){
    	$return = "";
    	$value = $this->getData($key);
      //Check if has widget config data
    	if($this->hasData($key) && $value !== null) {
    		if($key == "pretext") {
    			$value = base64_decode($value);
    		}
    		if($value == "true") {
    			return 1;
    		} elseif($value == "false") {
    			return 0;
    		}
    		return $value;

    	} else {

    		if(isset($this->_config[$key])){

    			$return = $this->_config[$key];

    			if($return == "true") {
    				$return = 1;
    			} elseif($return == "false") {
    				$return = 0;
    			}

    		}else{
    			$return = Mage::getStoreConfig("ves_tabshome/$panel/$key");
    		}
    		if($return == "" && $default) {
    			$return = $default;
    		}

    	}

    	return $return;
    }

     /**
     * overrde the value of the extension's configuration
     *
     * @return string
     */
     function setConfig($key, $value) {
     	if($value == "true") {
     		$value =  1;
     	} elseif($value == "false") {
     		$value = 0;
     	}
     	if($value != "") {
     		$this->_config[$key] = $value;
     	}
     	return $this;
     }

	/**
     * Rendering block content
     *
     * @return string
     */
	function _toHtml()
	{
		$show = $this->getConfig("show");
		if(!$show) return;

		//Override Config
		if(isset($this->_config) && $this->_config && is_array( $this->_config)) {
			foreach($this->_config as $key=>$val) {
				if($this->hasData($key)) {
					$this->setConfig($key, $this->getData($key));
				}
			}
		}

		$this->_assignBlockParams();

		return parent::_toHtml();
	}

	protected function _assignBlockParams() {
		$config = $this->_config;
		$theme = '';

		$tabClass = array();
		if($this->getConfig('class_tab')){
			$i = 0;
			$class = explode(';',$this->getConfig('class_tab'));
			foreach ($class as $k => $v) {
				$arr = explode(':',$v);
				if(count($arr)>1 && (int)$arr[0]>0){
					$tabClass[$arr[0]] = $arr[1];
					$i++;
				}
			}
		}

		$this->assign( 'tabClass', $tabClass );

		if(stristr($this->_config['catsid'], ',') === FALSE) {
			$arr_catsid =  array(0 => $this->_config['catsid']);
		}else{
			$arr_catsid = explode(",", $this->_config['catsid']);
		}

		if( !empty($arr_catsid)){
			$data = array();

			foreach ($arr_catsid as $k => $v) {
				$cat = Mage::getModel('catalog/category')->load($v);
				$data[ $k ]["mainImage"] = "";
				$data[ $k ]["link"] = $cat->getUrl();
				$data[ $k ]["title"] = $cat->getName();
				$data[ $k ]["product_count"] = $cat->getProductCount();
				$data[ $k ]["products"] = array();
				$data[ $k ]["category"] = $cat;
				$data[ $k ]['blockProducts'] = '';
				$config['catsid'] = $v;

				if( ( $this->getConfig('ajax_type') == 'carousel' || $this->getConfig('ajax_type') == 'sub-category' || $this->getConfig('ajax_type') == 'append') && $this->getConfig('is_ajax', 0)){
					$config['itemspage'] = $config['limit_item'];
					$list = Mage::getModel('ves_tabs/product')->getListProducts($config);
				} 

				if( $this->getConfig('ajax_type') == 'loadmore'  && $this->getConfig('is_ajax', 0)){

					$list = Mage::getModel('ves_tabs/product')->getListProducts($config);

				}
				
				if( $this->getConfig('ajax_type') == 'default' ){
					$config['itemspage'] = $config['limit_item'];
					$list = Mage::getModel('ves_tabs/product')->getListProducts($config);

				} 
				
				if( $this->getConfig('ajax_type') == 'append' && !$this->getConfig('enable_tab')  && $this->getConfig('is_ajax', 0)){
					$configBlock = $config;
					$list = Mage::getModel('ves_tabs/product')->getListProducts($config);
					$configBlock['source_products_mode'] = $this->getConfig('source_type');
					$configBlock['itemspage'] = $this->getConfig('source_limit_item');
					$configBlock['itemsrow'] = 1;
					$configBlock['image_width'] = $this->getConfig('thumbwidth_childblock');
					$configBlock['image_height'] = $this->getConfig('thumbheight_childblock');
					$data[ $k ]['blockProducts'] = '';
					$productsList = Mage::getModel('ves_tabs/product')->getListProducts($configBlock);
					$data[ $k ]['blockProducts'] = $productsList['products'];

				}

				if( $this->getConfig('ajax_type') == 'append' && $this->getConfig('enable_tab')  && $this->getConfig('is_ajax', 0)){
					$configBlock = $config;
					$configBlock['catsid'] = $this->getConfig['catsid'];
					$configBlock['source_products_mode'] = $this->getConfig('source_type');
					$configBlock['itemspage'] = $config['source_limit_item'];
					$blockProducts = Mage::getModel('ves_tabs/product')->getListProducts($configBlock);
					$data[ $k ]['blockProducts'] = $blockProducts['products'];
				}

				if(isset($list['products'])){
					$data[ $k ]["products"] = $list['products'];
				}

				$data[ $k ]["source"] = $this->getConfig('source_products_mode');
				$data[ $k ]["category_id"] = $v;
				$k++;
			}
		}

		$cms_block_id = $this->getConfig('cmsblock');
		$cms = "";
		if($cms_block_id){
			$cms = Mage::getSingleton('core/layout')->createBlock('cms/block')->setBlockId($cms_block_id)->toHtml();
			
		}

		$staticblockId = $this->getConfig('cmsblock_append');
		if($staticblockId != 'none'){
			$staticblock = Mage::getSingleton('core/layout')->createBlock('cms/block')->setBlockId($staticblockId)->toHtml();

			$this->assign( 'staticblock', $staticblock );
		}

		$attributes = serialize($this->_attributes);
		$theme = $this->getConfig("theme", "default");
		$this->assign( 'items', $data );
		$this->assign( 'cms', $cms );
		$this->assign( 'attributes', base64_encode($attributes));
		$this->assign('config', $this->_config);
		$this->assign("theme", $theme);
	}
	/**
     * Get Key pieces for caching block content
     *
     * @return array
     */
	public function getCacheKeyInfo()
	{
		return array(
			'VES_TABS_BLOCK_LIST',
			$this->getNameInLayout(),
			Mage::app()->getStore()->getId(),
			Mage::getDesign()->getPackageName(),
			Mage::getDesign()->getTheme('template'),
			Mage::getSingleton('customer/session')->getCustomerGroupId(),
			'template' => $this->getTemplate(),
			);
	}

	public function convertAttributesToConfig($attributes = array()) {
		if($attributes) {
			foreach($attributes as $key=>$val) {
				$this->setConfig($key, $val);
			}
		}
	}

	public function hasNextData($categoryid, $source_type){
		$config = $this->_config;
		$config['catsid'] = $categoryid;
		$lists =  Mage::getModel('ves_tabs/product')->getListProducts($config);
		if(!$lists['hasNextData']){
			return false;
		}
		return true;
	}

	public function getCategoryImage($category_id = 0, $width = 50, $height = 50)
	{
		$cur_category = Mage::getModel('catalog/category')->load($category_id);
		$_file_name = $cur_category->getThumbnail();
		$_file_name = $_file_name?$_file_name: $cur_category->getImage();
		$_media_dir = Mage::getBaseDir('media') . DS . 'catalog' . DS . 'category' . DS;
		$cache_dir = $_media_dir . 'cache' . DS;

		if($_file_name) {
			if (file_exists($cache_dir . $_file_name)) {
				return Mage::getBaseUrl('media') .'/catalog/category/cache/' . $_file_name;
			} elseif (file_exists($_media_dir . $_file_name)) {
				if (!is_dir($cache_dir)) {
					mkdir($cache_dir);
				}

				$_image = new Varien_Image($_media_dir . $_file_name);
				$_image->constrainOnly(true);
				$_image->keepAspectRatio(true);
				$_image->keepTransparency(true);
				$_image->resize((int)$width, (int)$height);
				$_image->save($cache_dir . $_file_name);

				return Mage::getBaseUrl('media') . '/catalog/category/cache/'. $_file_name;
			}
		}
		return "";
	}

	public function getLabel($key){
		$label = '';
		switch($key){
			case 'featured':
			$label = $this->getConfig('featured_label','Featured');
			break;
			case 'latest':
			$label = $this->getConfig('latest_label','Latest');
			break;
			case 'bestseller':
			$label = $this->getConfig('bestseller_label','Best Seller');
			break;
			case 'mostviewed':
			$label = $this->getConfig('mostviewed_label','Most Viewed');
			break;
			case 'special':
			$label = $this->getConfig('special_label','Special');
			break;
			case 'news':
			$label = $this->getConfig('newsarrival_label','News Arrival');
			break;
		}
		return $label;
	}
}