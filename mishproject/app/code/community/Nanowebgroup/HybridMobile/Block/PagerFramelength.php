<?php
/**
  * 
  * Sets pager frame length to 3 for mobile theme
 */

class Nanowebgroup_HybridMobile_Block_PagerFramelength extends Mage_Catalog_Block_Product_List_Toolbar
{

	/**
     * Render pagination HTML
     *
     * @return string
     */
    public function getMobilePagerHtml()
    {
        $pagerBlock = $this->getChild('product_list_toolbar_pager');

        if ($pagerBlock instanceof Varien_Object) {

            /* @var $pagerBlock Mage_Page_Block_Html_Pager */
            $pagerBlock->setAvailableLimit($this->getAvailableLimit());

            $pagerBlock->setUseContainer(false)
                ->setShowPerPage(false)
                ->setShowAmounts(false)
                ->setLimitVarName($this->getLimitVarName())
                ->setPageVarName($this->getPageVarName())
                ->setLimit($this->getLimit())
                ->setFrameLength(3)
                ->setJump(Mage::getStoreConfig('design/pagination/pagination_frame_skip'))
                ->setCollection($this->getCollection());

            return $pagerBlock->toHtml();
        }

        return '';
    }
}

