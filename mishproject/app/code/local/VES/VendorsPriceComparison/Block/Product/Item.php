<?php
class VES_VendorsPriceComparison_Block_Product_Item extends Mage_Catalog_Block_Product_Abstract{
    protected $_columns;

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
    protected function _cmpOrder($a,$b){
        $aOrder = $a['sort_order']?$a['sort_order']:1000;
        $bOrder = $b['sort_order']?$b['sort_order']:1000;
        return  $aOrder > $bOrder;
    }

    public function addColumn($name,$title,$block, $sortorder){
        $this->_columns[] = array(
            'name'	=> $name,
            'title'	=> $title,
            'block'	=> $block,
            'sort_order'	=> $sortorder,
        );
        if(sizeof($this->_columns) >1)
            usort($this->_columns, array($this,"_cmpOrder"));
    }


    public function getColumns(){
        /*
        $colums = array(
            array(
                'name'	=> "product_price",
                'title'	=> "Price",
                'block'	=> $this->getLayout()->createBlock('pricecomparison/product_price')->setTemplate('ves_pricecomparison/vendor_list/price.phtml'),
                'sort_order'	=> 10,
            ),
            array(
                'name'	=> "product_url",
                'title'	=> "Vendor",
                'block'	=> $this->getLayout()->createBlock('pricecomparison/product_vendor')->setTemplate('ves_pricecomparison/vendor_list/vendor.phtml'),
                'sort_order'	=> 30,
            ),
        );
        */
        return $this->_columns;;
    }

    public function getColumnHtml($block, VES_Vendors_Model_Vendor $vendor, Mage_Catalog_Model_Product $product){
        if(is_string($block)){
            $block = $this->getLayout()->getBlock($block);
            $block->setVendor($vendor)->setProduct($product);
            return $block->toHtml();
        }elseif($block instanceof Mage_Core_Block_Abstract){
            $block->setVendor($vendor)->setProduct($product);
            return $block->toHtml();
        }
        return '';
    }

    public function isShowSelectProduct(){
        if(!Mage::getSingleton('vendors/session')->getVendor()->getId()) return false;
        $vendor = Mage::getSingleton('vendors/session')->getVendor()->getId();
        $products = $this->getVendorRelatedProducts();
        $vendor_ids = array();
        foreach($products as $product){
            $vendor_ids[] = $product->getData("vendor_id");
        }
        if(sizeof($vendor_ids) == 0) return true;
        else{
            if(in_array($vendor,$vendor_ids)) return false;
            else return true;
        }
    }
    public function getCurrentProduct(){
        return Mage::registry("current_product_id");
    }
    public function getVendorRelatedProducts(){
        if(!$this->getData('vendor_related_products')){
            $productCollection = Mage::getModel('catalog/product')->getCollection()
                ->addAttributeToFilter('vendor_parent_product',$this->getCurrentProduct())
                ->addAttributeToFilter('vendor_id',array('gt' => 0))
                ->setOrder('price','ASC')
            ;
            $this->prepareProductCollection($productCollection);
            $this->setData('vendor_related_products',$productCollection);
        }
        return $this->getData('vendor_related_products');
    }
    /**
     * Initialize product collection
     *
     * @param Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection $collection
     * @return Mage_Catalog_Model_Layer
     */
    public function prepareProductCollection($collection)
    {
        $collection
            ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents();

        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);

        return $this;
    }

    public function getImageUrl($file){
        echo Mage::getBaseUrl('media').$file;
    }
}
