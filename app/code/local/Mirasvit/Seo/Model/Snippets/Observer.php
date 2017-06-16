<?php
class Mirasvit_Seo_Model_Snippets_Observer extends Varien_Object {
    var $appliedSnippets = false;
    var $isProductPage = false;

    function __construct() {
        if(Mage::app()->getFrontController()->getRequest()->getControllerName() === "product") {
            $this->isProductPage = true;
        }
    }

    public function getConfig()
    {
        return Mage::getSingleton('seo/config');
    }

    public function addProductSnippets($e) {
        if($this->isProductPage
            && !$this->appliedSnippets
            && $e->block->getNameInLayout() == "product.info"
            && $this->getConfig()->isRichSnippetsEnabled()) {

            $html = $e->transport->getHtml();
            $product = $e->block->getProduct();

            if ($this->offerFilter($html,$product)) {
                $html = $this->offerFilter($html,$product);
            }

            if ($this->productFilter($html,$product)) {
                $html = $this->productFilter($html,$product);
            }

            if ($this->aggregateRatingFilter($html,$product)) {
                $html = $this->aggregateRatingFilter($html,$product);
            }

            $e->transport->setHtml(
                $html
            );

            $this->appliedSnippets = true;
        }
        // elseif ($e->block->getNameInLayout() == "breadcrumbs") {
        //         $html = $e->transport->getHtml();
        //         $this->breadcrumbsFilter($html);
        //         $e->transport->setHtml(
        //             $html
        //         );
        // }
    }


    public function productFilter($html, $product) {
        $html = preg_replace('/\\"product\\-name\\"/i','"product-name" itemprop="name"',$html,1);
        $html = preg_replace('/(\\"product\\-img\\-box.*?\\<img)/ims','$1 itemprop="image" ',$html,1);
        $html = preg_replace('/\\"short\\-description\\"/i','"short-description" itemprop="description"',$html,1);

        $html = '<div itemscope itemtype="http://schema.org/Product">'.$html.'</div>';
        return $html;
    }

    public function offerFilter($html, $product)
    {
        $availability = "";
            if(method_exists ($product , "isAvailable" )) {
                $check = $product->isAvailable();
            } else {
                $check = $product->isInStock();
            }
            if ($check) {
                $availability .= '<link itemprop="availability" href="http://schema.org/InStock" />';
            } else {
                $availability .= '<link itemprop="availability" href="http://schema.org/OutOfStock" />';
            }

        $price = "";
            $price .= '<meta itemprop="priceCurrency" content="'.Mage::app()->getStore()->getCurrentCurrencyCode().'" />';
            $price .= '<span class="price" itemprop="price" $1>$2</span>';

        if(preg_match('/product-view.*?(special\\-price\\".*?)\\<span class\\=\\"price\\"(.*?)>(.*?)\\<\\/span\\>.*?action-box/ims',$html)) {
        // if(preg_match('/(special\\-price\\".*?)\\<span class\\=\\"price\\"(.*?)>(.*?)\\<\\/span\\>/ims',$html)) {
            $price = '<meta itemprop="priceCurrency" content="'.Mage::app()->getStore()->getCurrentCurrencyCode().'" />'.'<span class="price" itemprop="price" $2 >$3</span>';
            $replacement = '$1<span itemprop="offers" itemscope itemtype="http://schema.org/Offer">'.$availability.$price.'</span>';
            $html = preg_replace('/(special\\-price\\".*?)\\<span class\\=\\"price\\"(.*?)>(.*?)\\<\\/span\\>/ims',$replacement,$html,1);

        } else {
            $replacement = '<span itemprop="offers" itemscope itemtype="http://schema.org/Offer">'.$availability.$price.'</span>';
            $html = preg_replace('/\\<span class\\=\\"price\\"(.*?)\\>(.*?)\\<\\/span\\>/ims',$replacement,$html,1);
        }
        return $html;
    }

    public function aggregateRatingFilter($html, $product) {
            if (!is_object($product->getRatingSummary())) {
                return false;
            }
            if ($product->getRatingSummary()->getRatingSummary()) {
                $pattern = '/<p class="rating-links">(.*?)([0-9]+\s)/ims';
                $ratingValue = number_format($product->getRatingSummary()->getRatingSummary()/100*5, 1);
                $rating = '<p class="rating-links">$1<span itemprop="ratingValue">'.$ratingValue.'</span>';
                $html = preg_replace($pattern,$rating." "."/"." <span itemprop=\"reviewCount\">$2</span>",$html,1);
                $html = preg_replace('/\\<div class\\=\\"ratings\\"(.*?)\\>/ims','<div class="ratings" itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">',$html,1);
                return $html;
            } else {
                return false;
            }
    }

    public function breadcrumbsFilter($html) {
        if(Mirasvit_Seo_Block_Breadcrumbs::getBreadcrumbsSeparator()) {
            if (strpos($html, 'class="breadcrumbs"') !== false) {
                $html = preg_replace('/\\<li/','<li typeof="v:Breadcrumb"',$html);
                $html = preg_replace('/\\<a/','<a rel="v:url" property="v:title"',$html);
            }
        }
    }

}