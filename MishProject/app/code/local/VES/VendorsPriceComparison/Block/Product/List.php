<?php

class VES_VendorsPriceComparison_Block_Product_List extends Mage_Catalog_Block_Product_Abstract
{
        public $_limit = 6;
        public $_pagehtml ;
        public function getVendor(){
            if(Mage::getSingleton('vendors/session')->getVendor()->getId()) return Mage::getSingleton('vendors/session')->getVendor()->getId();
            return null;
        }

        public function __construct(){

        }

        public function getProduct(){
            return Mage::registry('product');
        }

    public function getPageProductVendor(){
            if(!$this->getVendor()) return null;
            $vendor_id = $this->getVendor();
            $products = Mage::getModel("catalog/product")->getCollection()
                ->addAttributeToSelect("*")
                ->addAttributeToFilter("vendor_id",$vendor_id)
                ->addFieldToFilter("status",array("eq"=>Mage_Catalog_Model_Product_Status::STATUS_ENABLED));


            $page = $this->getPageNumber() ? $this->getPageNumber() : 1 ;


            $paging=new VES_VendorsPriceComparison_Model_Page(sizeof($products));

            $paging->findPages($this->_limit);

            $paging->setPage($page);
            $start =$paging->rowStart($this->_limit);

            $curpage = ($start/$this->_limit)+1;

            $this->setData('start_pape',$start);

            //$this->_pagehtml =
            return $paging->pagesList($curpage);
        }

        public function getPageHtml(){
            return $this->getPageProductVendor();
        }

        public function getProductVendor(){
            $vendor_id = $this->getVendor();
            $start_page = $this->getData("start_pape");
            $products = Mage::getModel("catalog/product")->getCollection()
                ->addAttributeToSelect("*")
                ->addAttributeToFilter("vendor_id",$vendor_id)
                ->addFieldToFilter("status",array("eq"=>Mage_Catalog_Model_Product_Status::STATUS_ENABLED))
                ->setPage($start_page,$this->_limit);

            return $products;
        }

        public function getThumbnailImage($product){
            $baseDir = Mage::getSingleton('catalog/product_media_config')->getBaseMediaPath();
            $imageFile = $baseDir.$product->getSmallImage();
            if(file_exists($imageFile) && is_file($imageFile)){
                $src = Mage::helper('catalog/image')->init($product, 'small_image')->resize(50);
            }else{
               $src = $this->getSkinUrl('ves_vendorspricecomparison/images/catalog/product/placeholder/thumbnail.jpg');
            }
            return $src;
        }

        public function getPriceHtml($product, $displayMinimalPrice = false, $idSuffix = '')
        {
            $type_id = $product->getTypeId();
            if (Mage::helper('catalog')->canApplyMsrp($product)) {
                $realPriceHtml = $this->_preparePriceRenderer($type_id)
                    ->setProduct($product)
                    ->setDisplayMinimalPrice($displayMinimalPrice)
                    ->setIdSuffix($idSuffix)
                    ->toHtml();
                $product->setAddToCartUrl($this->getAddToCartUrl($product));
                $product->setRealPriceHtml($realPriceHtml);
                $type_id = $this->_mapRenderer;
            }

            return $this->_preparePriceRenderer($type_id)
                ->setProduct($product)
                ->setDisplayMinimalPrice($displayMinimalPrice)
                ->setIdSuffix($idSuffix)
                ->toHtml();
        }
}