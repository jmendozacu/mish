<?php
class VES_VendorsReview_ReviewController extends Mage_Core_Controller_Front_Action {
	public function postAction() {
		$vendor = $this->getRequest()->getParam('vendor');
		$data = $this->getRequest()->getPost();
		
		/****************set summary percent data *********************/
		$votes = $data['ratings'];
		$review_summary = 0;		//review summary rating
		 
		$ratingCollection = Mage::getModel('vendorsreview/rating')->getCollection()->setOrder('position','asc');
		 
		foreach($ratingCollection as $_rating) {
			//set review summary
			$rate_percents = $votes[$_rating->getId()]*100/5;
			$review_summary += $rate_percents;
		}
		 
		$review_summary = ceil($review_summary/$ratingCollection->count());
		$data['summary_percent'] = $review_summary;
		
		$model = Mage::getModel('vendorsreview/review');
		$model->setData($data)
		->setId($this->getRequest()->getParam('id'));
		
		
		try {
				$model->setCreatedTime(now());
				$model->save();
				
				Mage::dispatchEvent('vendorsreview_review_complete_after',array('review'=>$model));
				
				Mage::getSingleton('core/session')->addSuccess(Mage::helper('vendorsreview')->__('Your review has been accepted.'));
				Mage::getSingleton('core/session')->setFormData(false);
				
				$this->_redirectUrl(Mage::helper('vendorsreview')->getVendorRatingUrl($vendor));
				return;
            } catch (Exception $e) {
                Mage::getSingleton('core/session')->addError($e->getMessage());
                Mage::getSingleton('core/session')->setFormData($data);
                $this->_redirectUrl(Mage::helper('vendorsreview')->getVendorRatingUrl($vendor));
                return;
            }
	}
}