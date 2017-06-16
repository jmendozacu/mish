<?php

class VES_VendorsReview_Model_Review extends Mage_Core_Model_Abstract
{
	const XML_PATH_REVIEW_EMAIL_TEMPLATE 		= 'vendors/vendorsreview/email_template';
	const XML_PATH_REVIEW_EMAIL_IDENTITY 		= 'vendors/vendorsreview/email_identity';
	
	public function _construct()
    {
        parent::_construct();
        $this->_init('vendorsreview/review');
    }
    
    public function getCustomer() {
    	return Mage::getSingleton('customer/session')->getCustomer();
    }
    
    public function getVendor() {
    	return Mage::getModel('vendors/vendor')->load($this->getVendorId());
    }
    
    /**
     * Retrieve store model instance
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
    	$storeId = $this->getStoreId();
    	if ($storeId) {
    		return Mage::app()->getStore($storeId);
    	}
    	return Mage::app()->getStore();
    }
    
    
    
    
    /**
     * Send email with new review
     *
     * @param string $backUrl
     * @param string $storeId
     * @throws Mage_Core_Exception
     * @return Mage_Customer_Model_Customer
     */
    public function sendNewReviewEmail($backUrl = '', $storeId = '0')
    {
    	$votesArray = array();
    	
		$votes = $this->getRatingVotes();
		$ratingCollection = Mage::getModel('vendorsreview/rating')->getCollection()->setOrder('position','asc');
		foreach($ratingCollection as $_rating) {
			foreach($votes as $_vote) {
				if($_vote['rating_id'] == $_rating->getId())
					$votesArray[$_rating->getTitle()] = $_vote['rate_value'];
			}
		}  
		
		$votesObject = new Varien_Object($votesArray);
		$votesHtml = '';
		foreach($votesArray as $label => $value) {
			$votesHtml .= '<strong>' . $label . '</strong>: ' . $value . ' stars' . '<br />';
		}
		
    	if (!$storeId) {
    		$storeId = $this->getStore()->getId();
    	}
    
    	$this->_sendEmailTemplate(self::XML_PATH_REVIEW_EMAIL_TEMPLATE,self::XML_PATH_REVIEW_EMAIL_IDENTITY,
    			array(	'review' 			=>	$this, 
						'back_url' 			=>	$backUrl, 
						'customer'			=>	$this->getCustomer(),
    					'vendor'			=>	$this->getVendor(),
    					'vote'				=>	$votesObject, 
    					'voteHtml'			=>	$votesHtml,
						'vendor_url'		=>	Mage::helper('vendorsreview')->getVendorRatingUrl($this->getVendor()->getVendorId()),
    	), $storeId);
    	return $this;
    }
    /**
     * Send corresponding email template
     *
     * @param string $emailTemplate configuration path of email template
     * @param string $emailSender configuration path of email identity
     * @param array $templateParams
     * @param int|null $storeId
     * @return Mage_Customer_Model_Customer
     */
    protected function _sendEmailTemplate($template, $sender, $templateParams = array(), $storeId = null)
    {
    	/** @var $mailer Mage_Core_Model_Email_Template_Mailer */
    	$mailer = Mage::getModel('core/email_template_mailer');
    	$emailInfo = Mage::getModel('core/email_info');
    	$emailInfo->addTo($this->getCustomer()->getEmail(), $this->getCustomer()->getName());
    	$mailer->addEmailInfo($emailInfo);
    
    	// Set all required params and send emails
    	$mailer->setSender(Mage::getStoreConfig($sender, $storeId));
    	$mailer->setStoreId($storeId);
    	$mailer->setTemplateId(Mage::getStoreConfig($template, $storeId));
    	$mailer->setTemplateParams($templateParams);
    	$mailer->send();
    	return $this;
    }
    
    protected function _afterSave() {
    	$data = $this->getData();
    	$votes = $data['ratings'];
    	$voteModel = Mage::getModel('vendorsreview/vote');    	
    	$ratingCollection = Mage::getModel('vendorsreview/rating')->getCollection()->setOrder('position','asc');
    	
    	foreach($ratingCollection as $_rating) {
    		$voteModel->setData(array('rating_id'		=> $_rating->getId(), 
    								  'review_id'		=> $data['review_id'],
    								  'rate_value'		=> $votes[$_rating->getId()],
    								  'rate_percents'	=> $votes[$_rating->getId()]*100/5,
    		));
    		
    		
    		$voteRating = $voteModel->getVoteRating($data['review_id'], $_rating->getId());
    		if($voteRating) {
     			$voteModel->setId($voteRating['entity_id']);
     		}
    		
    		$voteModel->save();
    	}
    	
    	/**
    	 * save link table to accept customer can review
    	 */
    	
    	$collection = Mage::getModel('vendorsreview/link')->getCollection()
    	->addFieldToFilter('order_id',$this->getOrderId());
		
		foreach($collection as $_link) {
			$_link->setData('can_review','0');
			$_link->setData('show_rating_link','0');
			$_link->save();
		}
    	

    	parent::_afterSave();
    }
    
    public function getRatingVotes() {
    	return Mage::getModel('vendorsreview/vote')->getVoteByReview($this->getId());
    }
    
    public function getVoteToMassaction() {
    	$result = array();
    	$collection = Mage::getModel('vendorsreview/vote')->getCollection()->addFieldToFilter('review_id',$this->getId());
    	foreach($collection as $_vote) {
    		$result[$_vote->getRatingId()] = $_vote->getRateValue();
    	}
    	
    	return $result;
    }
	
}