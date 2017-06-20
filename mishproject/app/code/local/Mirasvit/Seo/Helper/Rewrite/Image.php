<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Advanced SEO Suite
 * @version   1.1.0
 * @revision  551
 * @copyright Copyright (C) 2014 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_Seo_Helper_Rewrite_Image extends Mage_Catalog_Helper_Image
{
	public function getConfig()
	{
		return Mage::getSingleton('seo/config');
	}

    public function init(Mage_Catalog_Model_Product $product, $attributeName, $imageFile=null)
    {
        $config = $this->getConfig();
        if ($config->getIsEnableImageFriendlyUrls()) {
            if ($template = $config->getImageUrlTemplate()) {
                $urlKey = Mage::helper('mstcore/parsevariables')->parse(
                    $template,
                    array('product' => $product)
                );
            } else {
                $urlKey = $product->getName();
            }
            $urlKey = Mage::getSingleton('catalog/product_url')->formatUrlKey($urlKey);
        } else {
        	return parent::init($product, $attributeName, $imageFile);
        }

        $this->_reset();
        $this->_setModel(Mage::getModel('seo/rewrite_product_image'));
        $this->_getModel()->setDestinationSubdir($attributeName);
        $this->_getModel()->setUrlKey($urlKey);
        $this->setProduct($product);

        $this->setWatermark(Mage::getStoreConfig("design/watermark/{$this->_getModel()->getDestinationSubdir()}_image"));
        $this->setWatermarkImageOpacity(Mage::getStoreConfig("design/watermark/{$this->_getModel()->getDestinationSubdir()}_imageOpacity"));
        $this->setWatermarkPosition(Mage::getStoreConfig("design/watermark/{$this->_getModel()->getDestinationSubdir()}_position"));
        $this->setWatermarkSize(Mage::getStoreConfig("design/watermark/{$this->_getModel()->getDestinationSubdir()}_size"));

        if ($imageFile) {
            $this->setImageFile($imageFile);
        }
        else {
            // add for work original size
            $this->_getModel()->setBaseFile( $this->getProduct()->getData($this->_getModel()->getDestinationSubdir()) );
        }

        $this->setImageAlt($attributeName);
        return $this;
    }


    public function generateAlt()
    {
        if ($template = $this->getConfig()->getImageAltTemplate()) {
            $alt = Mage::helper('mstcore/parsevariables')->parse(
                $template,
                array('product' => $this->getProduct())
            );
        } else {
            $alt = $product->getName();
        }
        $alt = trim($alt);
        return $alt;
    }

    protected function setImageAlt($attributeName) {
        if (!$this->getConfig()->getIsEnableImageAlt()) {
            return;
        }
        $alt = $this->generateAlt();
        $product = $this->getProduct();
        $key = $attributeName . '_label';
        if (!$product->getData($key)) {
            $product->setData($attributeName . '_label', $alt);
            if ($gallery = $product->getMediaGalleryImages()) {
                $alt = $this->generateAlt();
                foreach ($gallery as $image) {
                    if (! $image->getLabel()) {
                        $image->setLabel($alt);
                    }
                }
            }
        }
    }

}