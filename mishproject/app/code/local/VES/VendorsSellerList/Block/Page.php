<?php
class VES_VendorsSellerList_Block_Page extends Mage_Core_Block_Template {
    public function _construct() {
        parent::_construct();
    }

    public function getBeforeSellersListHeaderHtml() {
        return $this->getChildHtml('before_header_static_block');
    }

    public function getSellersListHeaderHtml() {
        $identifier = $this->helper('vendorssellerlist')->getHeaderStaticBlockId();
        return $this->getLayout()->createBlock('cms/block')->setBlockId($identifier)->toHtml();
    }

    public function getAfterSellersListHeaderHtml() {
        return $this->getChildHtml('after_header_static_block');
    }

    public function getBeforeSellersListFooterHtml() {
        return $this->getChildHtml('before_footer_static_block');
    }

    public function getSellersListFooterHtml() {
        $identifier = $this->helper('vendorssellerlist')->getFooterStaticBlockId();
        return $this->getLayout()->createBlock('cms/block')->setBlockId($identifier)->toHtml();
    }

    public function getAfterSellersListFooterHtml() {
        return $this->getChildHtml('after_footer_static_block');
    }

    public function getContentHtml() {
        return $this->getChildHtml('vendorssellerlist.parent');
    }
}