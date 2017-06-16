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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_ConfigurableSwatches
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Ves_Tempcp_Block_Catalog_Media_Js_Product
    extends Mage_ConfigurableSwatches_Block_Catalog_Media_Js_Abstract
{
    /**
     * Return array of single product -- current product
     *
     * @return array
     */
    public function getProducts() {
        $product = Mage::registry('product');

        if (!$product) {
            return array();
        }

        return array($product);
    }

    /**
     * Get image fallbacks by product as
     * array(product ID => array( product => product, image_fallback => image fallback ) )
     *
     * @return array
     */
    public function getProductImageFallbacks($keepFrame = null, $image_width = null, $image_height = null) {

        $theme = Mage::helper("ves_tempcp/framework")->getFramework( );
        $themeConfig = $theme->getConfig();
        $main_image_width = (int)$themeConfig->get("main_image_width", $image_width);
        $main_image_height = (int)$themeConfig->get("main_image_height", $image_heigh);

        /* @var $helper Mage_ConfigurableSwatches_Helper_Mediafallback */
        $helper = Mage::helper('ves_tempcp/mediafallback');

        $fallbacks = array();

        $products = $this->getProducts();
        
        if ($keepFrame === null) {
            $keepFrame = $this->isKeepFrame();
        }

        /* @var $product Mage_Catalog_Model_Product */
        foreach ($products as $product) {
            $imageFallback = $helper->getConfigurableImagesFallbackArray2($product, $this->_getImageSizes(), $keepFrame, $main_image_width , $main_image_height );

            $fallbacks[$product->getId()] = array(
                'product' => $product,
                'image_fallback' => $this->_getJsImageFallbackString($imageFallback)
            );
        }

        return $fallbacks;
    }

    /**
     * Default to base image type
     *
     * @return string
     */
    public function getImageType() {
        $type = parent::getImageType();

        if (empty($type)) {
            $type = Mage_ConfigurableSwatches_Helper_Productimg::MEDIA_IMAGE_TYPE_BASE;
        }

        return $type;
    }

    /**
     * instruct image image type to be loaded
     *
     * @return array
     */
    protected function _getImageSizes() {
        return array('image');
    }
    /**
     * Is need keep frame
     *
     * @return bool
     */
    public function isKeepFrame()
    {
        $keepFrame = false;
        if($this->_productListBlocks) {
            foreach ($this->_productListBlocks as $blockName) {
                $listBlock = $this->getLayout()->getBlock($blockName);

                if ($listBlock && $listBlock->getMode() == 'grid') {
                    $keepFrame = true;
                    break;
                }
            }
        }
        
        return $keepFrame;
    }
}
