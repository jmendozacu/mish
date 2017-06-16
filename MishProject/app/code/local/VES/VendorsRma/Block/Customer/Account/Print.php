<?php
class VES_VendorsRma_Block_Customer_Account_Print extends VES_VendorsRma_Block_Customer_Abstract
{

    public function _prepareLayout()
    {
        if(!$this->getRequest()->getParam('sc'))
            $this->getLayout()->getBlock('customer_account_navigation')->setActive('vesrma/rma_customer/list');
        return parent::_prepareLayout();
    }

    public function getCustomerInfor(){
        $model = Mage::getModel("vendorsrma/address")->getCollection()->addFieldToFilter("request_id",$this->getRequestRma()->getId())
            ->getFirstItem();
        return $model;
    }

    public function getCountryCollection()
    {
        $collection = $this->getData('country_collection');
        if (is_null($collection)) {
            $collection = Mage::getModel('directory/country')->getResourceCollection()
                ->loadByStore();
            $this->setData('country_collection', $collection);
        }

        return $collection;
    }


    public function getRegionName($code){
        $region = Mage::getModel('directory/region')->load($code);
        return $region->getName();
    }

    public function getCountryName($code){
        $region = Mage::getModel('directory/country')->load($code);
        return $region->getName();
    }

    public function getPrintUrl(){
        return $this->getUrl('vesrma/rma_customer/printform/',array('id'=>$this->getRequestRma()->getId()));
    }



}