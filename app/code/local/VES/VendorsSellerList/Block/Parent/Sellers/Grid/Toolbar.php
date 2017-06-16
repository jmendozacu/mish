<?php
class VES_VendorsSellerList_Block_Parent_Sellers_Grid_Toolbar extends Mage_Catalog_Block_Product_List_Toolbar {
    protected function _prepareLayout() {
        $this->setTemplate('ves_vendorssellerlist/parent/sellers/toolbar.phtml');
    }

    /**
     * Retrieve available limits for specified view mode
     *
     * @return array
     */
    protected function _getAvailableLimit()
    {
        $perPageConfigKey = 'vendors/sellers/grid_per_page_values';
        $perPageValues = (string)Mage::getStoreConfig($perPageConfigKey);
        $perPageValues = explode(',', $perPageValues);
        $perPageValues = array_combine($perPageValues, $perPageValues);
        if (Mage::getStoreConfigFlag('vendors/vendor_theme/list_allow_all')) {
            return ($perPageValues + array('all'=>$this->__('All')));
        } else {
            return $perPageValues;
        }
    }

    /**
     * Retrieve default per page values
     *
     * @return string (comma separated)
     */
    public function getDefaultPerPageValue()
    {
        return 5;
    }


    public function getPagerHtml()
    {
        $pagerBlock = $this->getLayout()->createBlock('page/html_pager')->setTemplate('ves_vendorsellerlist/parent/sellers/pager.phtml');

        if ($pagerBlock instanceof Varien_Object) {

            /* @var $pagerBlock Mage_Page_Block_Html_Pager */
            $pagerBlock->setAvailableLimit($this->getAvailableLimit());

            $pagerBlock->setUseContainer(false)
                ->setShowPerPage(false)
                ->setShowAmounts(false)
                ->setLimitVarName($this->getLimitVarName())
                ->setPageVarName($this->getPageVarName())
                ->setLimit($this->getLimit())
                ->setCollection($this->getCollection());
            return $pagerBlock->toHtml();
        }
        return '';
    }
}
