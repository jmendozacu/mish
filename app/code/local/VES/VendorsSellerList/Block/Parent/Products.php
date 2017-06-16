<?php
class VES_VendorsSellerList_Block_Parent_Products extends VES_VendorsSellerList_Block_Parent {
    /**
     * get latest products added by vendor(from config)
     */
    public function getLatestProducts($is_limited=true) {
        $limit = $this->getLimitNumber();
        $products = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('*')
            ->addStoreFilter(Mage::app()->getStore()->getId())
            ->addAttributeToFilter('approval',VES_VendorsProduct_Model_Source_Approval::STATUS_APPROVED)
            ->addAttributeToFilter('status','1');
        if($is_limited) {
            $products->getSelect()->order('created_at','DESC')->limit($limit);
        } else {
            $products->getSelect()->order('created_at','DESC');
        }

        return $products;
    }

    public function getProductUrl($product, $additional = array())
    {
        if ($this->hasProductUrl($product)) {
            if (!isset($additional['_escape'])) {
                $additional['_escape'] = true;
            }
            return $product->getUrlModel()->getUrl($product, $additional);
        }
        return '#';
    }

    public function hasProductUrl($product)
    {
        if ($product->getVisibleInSiteVisibilities()) {
            return true;
        }
        return false;
    }

    public function getImageLabel($product = null, $mediaAttributeCode = 'image')
    {
        if (is_null($product)) {
            $product = $this->getProduct();
        }

        $label = $product->getData($mediaAttributeCode . '_label');
        if (empty($label)) {
            $label = $product->getName();
        }

        return $label;
    }
}