<?php

class VES_VendorsPriceComparison_Block_Product_Search extends VES_VendorsPriceComparison_Block_Product_List
{
    public function getPageProductVendor(){
        if(!$this->getVendor()) return null;
        $vendor_id = $this->getVendor();
        $products = Mage::getModel("catalog/product")->getCollection()
            ->addAttributeToSelect("*")
            ->addAttributeToFilter("vendor_id",$vendor_id)
            ->addFieldToFilter("status",array("eq"=>Mage_Catalog_Model_Product_Status::STATUS_ENABLED))
            ->addAttributeToFilter(
                array(
                    array('attribute'=> 'name','like' => $this->getQ().'%'),
                    array('attribute'=> 'name','like' => '%'.$this->getQ().'%'),
                    array('attribute'=> 'name','like' => '%'.$this->getQ()),
                )
            );

        $page = $this->getPageNumber() ? $this->getPageNumber() : 1 ;


        $paging=new VES_VendorsPriceComparison_Model_Page(sizeof($products));

        $paging->findPages($this->_limit);

        $paging->setPage($page);
        $start =$paging->rowStart($this->_limit);

        $curpage = ($start/$this->_limit)+1;

        $this->setData('start_pape',$start);

        //$this->_pagehtml =
        return $paging->pagesListSearch($curpage);
    }

    public function getProductVendor(){
        $vendor_id = $this->getVendor();
        $start_page = $this->getData("start_pape");
        $products = Mage::getModel("catalog/product")->getCollection()
            ->addAttributeToSelect("*")
            ->addAttributeToFilter("vendor_id",$vendor_id)
            ->addFieldToFilter("status",array("eq"=>Mage_Catalog_Model_Product_Status::STATUS_ENABLED))
            ->addAttributeToFilter(  array(
                    array('attribute'=> 'name','like' => $this->getQ().'%'),
                    array('attribute'=> 'name','like' => '%'.$this->getQ().'%'),
                    array('attribute'=> 'name','like' => '%'.$this->getQ()),
                )
            )
            ->setPage($start_page,$this->_limit);

        return $products;
    }
}