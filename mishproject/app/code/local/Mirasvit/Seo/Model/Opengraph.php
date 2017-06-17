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


class Mirasvit_Seo_Model_Opengraph extends Mage_Core_Model_Abstract
{
    public function getConfig()
    {
        return Mage::getSingleton('seo/config');
    }

    public function modifyHtmlResponse($e)
    {
        if (strpos(Mage::helper('core/url')->getCurrentUrl(), '/checkout/onepage/')) {
            return;
        }

        if (Mage::getSingleton('admin/session')->getUser()) {
            return;
        }

        $response = $e->getResponse();
        $config   = $this->getConfig();
        $str      = '';

        if ($config->isOpenGraphEnabled()) {
            $str .=' prefix="og: http://ogp.me/ns#" ';
        }

        if ($config->isRichSnippetsEnabled()) {
            $str .=' xmlns:fb="http://www.facebook.com/2008/fbml" ';
        }

        if ($str == '') {
            return;
        }

        if (!$config->isOpenGraphEnabled()) {
            return;
        }

        $body = $response->getBody();
        if (strpos(trim($body), '<!DOCTYPE html') !== 0 && strpos($body, '<html') !== 0 && strpos(trim($body), '<?xml') !== 0) {
            return;
        }

        $label = "<!-- mirasvit block -->";
        if (strpos($body, $label) !== false) {
            return;
        }

        $body = str_replace('<html', '<html'.$str, $body);
        if ($product = Mage::registry('current_product')) {
            //$product = Mage::getModel('catalog/product')->load($product->getId());
            $tags   = array();
            if ($config->isOpenGraphEnabled()) {
                $tags[] = $label;
                $tags[] = "<!-- mirasvit open graph begin -->";
                $tags[] = $this->createMetaTag('title', $product->getName());

                $tags[] = $this->createMetaTag('description', $product->getShortDescription());
                $tags[] = $this->createMetaTag('type', 'product');
                $tags[] = $this->createMetaTag('url', $product->getProductUrl());

                if ($product->getImage()!='no_selection') {
                    $tags[] = $this->createMetaTag('image', Mage::helper('catalog/image')->init($product, 'image'));
                }

                foreach($product->getMediaGalleryImages() as $image) {
                    if ($image->getFile()) {
                        $tags[] = $this->createMetaTag('image', Mage::helper('catalog/image')->init($product, 'image', $image->getFile()));
                    }
                }

                $tags[] = "<!-- mirasvit open graph end -->";
                $tags = array_unique($tags);
            }

            $body   = str_replace('<head>', "<head>\n".implode($tags, "\n"), $body);

            // if ($config->isRichSnippetsEnabled()) {
            //     $richSnippetsTags = array();
            //     $richSnippetsTags[] = "<!-- mirasvit rich snippets begin -->";
            //     $richSnippetsTags[] = '<article itemscope itemtype="http://data-vocabulary.org/Product"/>';
            //     $richSnippetsTags[] = '<h2 itemprop="name" class="review-aggregate-hide">' . $product->getName() . '</h2>';
            //     $richSnippetsTags[] = "<!-- mirasvit rich snippets end -->";
            //     $body   = str_replace('</head>', implode($richSnippetsTags, "\n")."</head>", $body);
            // }
        }

        $response->setBody($body);
    }

    protected function createMetaTag($property, $value)
    {
        $value = Mage::helper('seo')->cleanMetaTag($value);

        return "<meta property=\"og:$property\" content=\"$value\"/>";
    }
}
