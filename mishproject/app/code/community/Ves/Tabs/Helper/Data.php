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
 * @category    Lof * @package     Lof_Slider
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Helper Data
 *
 * @author      LandOfCoder <landofcoder@gmail.com>
 */
class Ves_Tabs_Helper_Data extends Mage_Core_Helper_Abstract {

	/**
	*
	* @param $config = array()
	* return string
	*/
	public function getAjaxData( $config = array(), $productsList ){

		if(empty($productsList['products'])){
			return;
		}

		$loadScript = false;
		$size = count($productsList['products']->getData());
		$i = $count = 1;
		$result = '';

		if(!$productsList){ 
		//break; 
		}

		foreach ( $productsList['products'] as $product ) {
			if( $count == $size ){
				$loadScript = true;
			}

			if( (int)$config['itemspage'] > 0 && ($i-1) % $config['itemspage'] == 0 && $i>1 ){
				$config['page'] = $config['page'] + 1;
				$i = 1;
			}

			$result .= Mage::app()
			->getLayout()
			->createBlock("ves_tabs/item")
			->setConfigBlock($config)
			->assign('i', $i)
			->assign('small', 12)
			->assign('product', $product)
			->assign('loadScript',$loadScript)
			->toHtml();

			$i++;
			$count++;
		}

		return $result;
	}



	public function checkAvaiable( $controller_name = ""){
		$arr_controller = array(  "Mage_Cms",
			"Mage_Catalog",
			"Mage_Tag",
			"Mage_Checkout",
			"Mage_Customer",
			"Mage_Wishlist",
			"Mage_CatalogSearch");
		if( !empty($controller_name)){
			if( in_array( $controller_name, $arr_controller ) ){
				return true;
			}
		}
		return false;
	}
	public function checkMenuItem( $menu_name = "", $config = array())
	{
		if(!empty( $menu_name) && !empty( $config)){
			$menus = isset($config["menuAssignment"])?$config["menuAssignment"]:"all";
			$menus = explode(",", $menus);
			if( in_array("no", $menus) ){
				return false;
			}
			if( in_array("all", $menus) || in_array( $menu_name, $menus) ){
				return true;
			}
		}
		return false;
	}
	public function getListMenu(){
		$arrayParams = array(
			'no'=>Mage::helper('adminhtml')->__("No Page Assignment"),
			'all'=>Mage::helper('adminhtml')->__("All"),
			'Mage_Cms_index'=>Mage::helper('adminhtml')->__("Mage Cms Index"),
			'Mage_Cms_page'=>Mage::helper('adminhtml')->__("Mage Cms Page"),
			'Mage_Catalog_category'=>Mage::helper('adminhtml')->__("Mage Catalog Category"),
			'Mage_Catalog_product'=>Mage::helper('adminhtml')->__("Mage Catalog Product"),
			'Mage_Customer_account'=>Mage::helper('adminhtml')->__("Mage Customer Account"),
			'Mage_Wishlist_index'=>Mage::helper('adminhtml')->__("Mage Wishlist Index"),
			'Mage_Customer_address'=>Mage::helper('adminhtml')->__("Mage Customer Address"),
			'Mage_Checkout_cart'=>Mage::helper('adminhtml')->__("Mage Checkout Cart"),
			'Mage_Checkout_onepage'=>Mage::helper('adminhtml')->__("Mage Checkout"),
			'Mage_CatalogSearch_result'=>Mage::helper('adminhtml')->__("Mage Catalog Search"),
			'Mage_Tag_product'=>Mage::helper('adminhtml')->__("Mage Tag Product")
			);
		return $arrayParams;
	}
	function get( $attributes = array() )
	{
		$data = array();
		$arrayParams = array('show',
			'cmsblock_append',
			'enable_cache',
			'cache_lifetime',
			'show_number_tab',
			'newsarrival_label',
			'latest_label',
			'bestseller_label',
			'mostviewed_label',
			'special_label',
			'featured_label',
			'show_tab_title',
			'icon_height',
			'icon_width',
			'background_tab',
			'position_staticblock',
			'position_childblock',
			'thumbwidth_childblock',
			'thumbheight_childblock',
			'enable_tab',
			'name',
			'ajax_type',
			'source_type',
			'source_limit_item',
			'title',
			'cmsblock',
			'mode',
			'pretext',
			'desc_maxchar',
			'limit_item',
			'itemsrow',
			'itemspage',
			'image_width',
			'image_height',
			'swap_image_number',
			'popup_quickview_width',
			'popup_quickview_height',
			'show_desc',
			'show_price',
			'show_title',
			'detail',
			'show_img',
			'show_btn',
			'enable_quickview',
			'enable_sale_icon',
			'enable_new_icon',
			'enable_swap',
			'popup_quickview_height',
			'popup_quickview_width',
			'show_pzoom',
			'prefix',
			'theme',
			'blockPosition',
			'customBlockPosition',
			'blockDisplay',
			'menuAssignment',
			'source',
			'folder',
			'description',
			'imageLimit',
			'blocks',
			'sourceStatic',
			'catsid',
			'source_products_mode',
			'quanlity',
			'imagecategory',
			'mainWidth',
			'mainHeight',
			'thumbnailMode',
			'show_tab_icon',
			'show_wc',
			'thumbWidth',
			'thumbHeight',
			'show_block_desc',
			'heightDuration',
			'heightEffect',
			'autoSlide',
			'autoSlideInterval',
			'firstPanelToLoad',
			'slideEaseDuration',
			'slideEaseFunction',
			'slideType',
			'slideEvent',
			'auto_strip_tags',
			'open_target',
			'show_review',
			'width_child_block',
			'enable_owl_carousel'
			);
foreach ($arrayParams as $var)
{
	$tags = array('ves_tabs', 'slider_source','image_source_setting','staticblocks_source_setting','catalog_source_setting', 'main_slider','file_source_setting','append_block_setting');
	$var = trim( $var );
	foreach($tags as $tag){
		if(Mage::getStoreConfig("ves_tabs/$tag/$var")!=""){
			$data[$var] =  Mage::getStoreConfig("ves_tabs/$tag/$var");
		}
	}
	if(isset($attributes[$var]))
	{
		$data[$var] =  $attributes[$var];
	}
}
return $data;
}
public function getImageUrl($url = null) {
	return Mage::getSingleton('ves_tabs/config')->getBaseMediaUrl() . $url;
}

    /**
     * Encode the mixed $valueToEncode into the JSON format
     *
     * @param mixed $valueToEncode
     * @param  boolean $cycleCheck Optional; whether or not to check for object recursion; off by default
     * @param  array $options Additional options used during encoding
     * @return string
     */
    public function jsonEncode($valueToEncode, $cycleCheck = false, $options = array())
    {
    	$json = Zend_Json::encode($valueToEncode, $cycleCheck, $options);
    	/* @var $inline Mage_Core_Model_Translate_Inline */
    	$inline = Mage::getSingleton('core/translate_inline');
    	if ($inline->isAllowed()) {
    		$inline->setIsJson(true);
    		$inline->processResponseBody($json);
    		$inline->setIsJson(false);
    	}

    	return $json;
    }
}
?>