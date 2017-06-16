<?php
class VES_VendorsSellerList_Block_Parent extends Mage_Core_Block_Template {

    protected $_limitNumberLayoutDepend = array();
    protected $_defaultLimitNumber = 8;

    public function _construct() {
        parent::_construct();
    }

    public function getProductsHtml() {
        return $this->getChildHtml('vendorssellerlist.parent.products');
    }

    public function getSellersListHtml() {
        return $this->getChildHtml('vendorssellerlist.parent.sellers');
    }

    public function getAllSellersUrl() {
        return $this->getUrl('*/*/all');
    }

    public function addLimitNumberLayoutDepend($pageLayout, $columnCount)
    {
        $this->_limitNumberLayoutDepend[$pageLayout] = $columnCount;
        return $this;
    }

    public function removeLimitNumberLayoutDepend($pageLayout)
    {
        if (isset($this->_limitNumberLayoutDepend[$pageLayout])) {
            unset($this->_limitNumberLayoutDepend[$pageLayout]);
        }

        return $this;
    }

    public function getLimitNumberLayoutDepend($pageLayout)
    {
        if (isset($this->_limitNumberLayoutDepend[$pageLayout])) {
            return $this->_limitNumberLayoutDepend[$pageLayout];
        }

        return false;
    }

    /**
     * get limit number sellers or products get from layout.xml
     * @return int
     */
    public function getLimitNumber()
    {
        if (!$this->_getData('limit_number')) {
            $pageLayoutCode = $this->getPageLayoutCode();
            if ($pageLayoutCode && $this->getLimitNumberLayoutDepend($pageLayoutCode)) {
                $this->setData(
                    'limit_number',
                    $this->getLimitNumberLayoutDepend($pageLayoutCode)
                );
            } else {
                $this->setData('limit_number', $this->_defaultLimitNumber);
            }
        }
        return (int) $this->_getData('limit_number');
    }

    /**
     * get page layout code from config
     * @return string
     */
    public function getPageLayoutCode() {
        $layout = Mage::getStoreConfig('vendors/sellers_list/layout');
        return array_search('1',$layout);
    }
}