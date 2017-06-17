<?php
class VES_VendorsSellerList_Block_Parent_Sellers extends VES_VendorsSellerList_Block_Parent {
    /**
     * get all sellers from market
     */
    public function getAllSellers($limit=true) {
        $number_sellers = $this->getLimitNumber();

        $collection = Mage::getModel('vendors/vendor')->getCollection()
        ->addAttributeToSelect('*')
            ->addFieldToFilter('status',VES_Vendors_Model_Vendor::STATUS_ACTIVATED)
            ->addFieldToFilter('website_id',Mage::app()->getStore()->getWebsiteId());
        if($limit===true) {
            $collection->getSelect()->order('created_at','DESC')->limit($number_sellers);
        } else {
            $collection->getSelect()->order('created_at','DESC');
        }
        //$collection->getSelect()->where(array('e.is_active = ?'=> '1'));

        return $collection;
    }

    public function getSellerUrl($id) {
        return Mage::helper('vendorspage')->getUrl($id,'');
    }

    /**
     * get seller avatar url
     * if seller not have avatar,get default image url
     */
    public function getSellerImageUrl($seller) {
        if($seller->getId()) {
            if($seller->getLogo() == '') {
                $url = $this->getSkinUrl('images/ves_vendorssellerlist/user_icon.png',array('_theme'=>'default','_package'=>'default'));
            } else {
                $url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).$seller->getLogo();
            }
            return $url;
        }
        return '';
    }

    public function showViewAllButton() {
        return ($this->getAllSellers(false)->count() > $this->getLimitNumber())?true:false;
    }

    public function getViewAllUrl() {
        return $this->getUrl('*/*/all');
    }

    public function isViewAll() {
        $request = Mage::app()->getRequest();
        return ($request->getControllerName()=='index' and $request->getModuleName()=='sellers' and $request->getActionName()=='all')?true:false;
    }

    public function getGridHtml() {
        return $this->getChildHtml('vendorssellerlist.parent.sellers.grid');
    }

}