<?php

class VES_VendorsReview_Block_Vendor_Rating_List extends Mage_Core_Block_Template
{
	public function getVendorId() {
		return Mage::registry('vendor')->getId();
	}
	
	/**
     * Preparing layout
     *
     * @return VES_VendorsCategory_Block_Breadcrumbs
     */
    protected function _prepareLayout()
    {
        if ($breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs')) {

            $vendorId = Mage::registry('vendor_id');
            $breadcrumbsBlock->addCrumb('vendor', array(
            		'label'=>Mage::registry('vendor')->getTitle(),
            		'title'=>Mage::registry('vendor')->getTitle(),
            		'link'=>Mage::helper('vendorspage')->getUrl($vendorId),
            ));

            $breadcrumbsBlock->addCrumb('vendor_reviews', array(
            		'label'=>$this->__('Reviews'),
            		'title'=>$this->__('Reviews'),
            ));

            if ($headBlock = $this->getLayout()->getBlock('head')) {
                $headBlock->setTitle('Vendor Reviews - ' .Mage::registry('vendor')->getTitle());
            }
        }
        
        $pager = $this->getLayout()->createBlock('vendorsreview/page_html_pager', 'vendor.review.pager');
		$pager->setAvailableLimit(array(3=>3,5=>5,10=>10,'all'=>'all'));
		$pager->setCollection($this->getReviewsCollection());
		$this->setChild('pager', $pager);
		$this->getCollection()->load();
		
        return parent::_prepareLayout();
    }
	public function getReviewsCollection()
	{
		if (null === $this->_reviewsCollection) {
			$collection = Mage::getModel('vendorsreview/review')->getCollection()
			->addFieldToFilter('status',VES_VendorsReview_Model_Type::APPROVED)
			->addFieldToFilter('vendor_id',$this->getVendorId());
			
			$this->_reviewsCollection = $collection;
			$this->setCollection($collection);
		}
		return $collection;
	}
	
	public function getPagerHtml()
	{
		return $this->getChildHtml('pager');
	}
	
	public function getRatingTitle($rating_id) {
		return Mage::getModel('vendorsreview/rating')->load($rating_id)->getTitle();
	}
}
