<?php
class OTTO_AdvancedFaq_NewController extends Mage_Core_Controller_Front_Action
{
	/**
     * Action predispatch
     *
     * Check customer authentication for some actions
     
    public function preDispatch()
    {
        parent::preDispatch();
		if(!Mage::helper("advancedfaq")->getAllowAccess()) return;
        if (!$this->getRequest()->isDispatched()) {
            return;
        }

        if (! Mage::getSingleton('customer/session')->authenticate($this)) {
        	$this->setFlag('', 'no-dispatch', true);
        }
    }
    */
    public function indexAction()
    {	
		$this->loadLayout();     
		$this->renderLayout();
    }
    public function saveAction(){
    	$seller =  Mage::registry("current_seller");
    	if ($data = $this->getRequest()->getPost()) {
    		$model = Mage::getModel('advancedfaq/faq');
    		$data['show_on'] = OTTO_AdvancedFaq_Model_Status::STATUS_DISABLED;
    		$data['status'] = OTTO_AdvancedFaq_Model_Status::STATUS_DISABLED;
    		$data['sort_order'] = 1;
    		$data['seller_id'] = $seller->getId();
    		$model->setData($data)
    		->setId($this->getRequest()->getParam('id'));
    			
    		try {
				if ($model->getCreatedTime() == NULL || $model->getUpdatedTime() == NULL) {
						$model->setCreatedTime(now())
						->setUpdatedTime(now());
					} else {
						$model->setUpdatedTime(now());
				}
				if(Mage::helper('advancedfaq/recapcha')->getEnableRecapchar()) {
						$resp = recaptcha_check_answer (Mage::helper('advancedfaq/recapcha')->getPrivateKey(),
												$_SERVER["REMOTE_ADDR"],
											   $this->getRequest()->getPost("recaptcha_challenge_field"),
											   $this->getRequest()->getPost("recaptcha_response_field")
											   );

						if (!$resp->is_valid) {
								$error = $resp->error;
								Mage::getSingleton('core/session')->addError(Mage::helper('advancedfaq')->__('Your submitted captcha is incorrect. Please try again.'));
								Mage::getSingleton('core/session')->setNewDataFaq($data);

				    				$this->_redirectUrl(Mage::helper("sellerspage")->getUrl($seller,Mage::getStoreConfig('advancedfaq/config/url_key').'/new/'));
				    		
								return;
						}
						else{
								$model->save();
								Mage::getSingleton('core/session')->addSuccess(Mage::helper('advancedfaq')->__('Your question has been submited.'));
								Mage::getSingleton('core/session')->setFormData(false);

				    			$this->_redirectUrl(Mage::helper("sellerspage")->getUrl($seller,Mage::getStoreConfig('advancedfaq/config/url_key').'/new/'));
				    				//$this->_redirect($seller->getData("seller_id")."/".Mage::getStoreConfig('advancedfaq/config/url_key').'/new/');
				    	
								return;
						}
				}
				else{
					$model->save();
					Mage::getSingleton('core/session')->addSuccess(Mage::helper('advancedfaq')->__('Your question has been submited.'));
					Mage::getSingleton('core/session')->setFormData(false);
				}

				$this->_redirectUrl(Mage::helper("sellerspage")->getUrl($seller,Mage::getStoreConfig('advancedfaq/config/url_key').'/new/'));
    				//$this->_redirect($seller->getData("seller_id")."/".Mage::getStoreConfig('advancedfaq/config/url_key').'/new/');
    		
    			
    			return;
    		} catch (Exception $e) {
    			Mage::getSingleton('core/session')->addError($e->getMessage());
    			Mage::getSingleton('core/session')->setFormData($data);
				$this->_redirectUrl(Mage::helper("sellerspage")->getUrl($seller,Mage::getStoreConfig('advancedfaq/config/url_key').'/new/'));
    			return;
    		}
    	}
    	Mage::getSingleton('core/session')->addError(Mage::helper('advancedfaq')->__('Unable to find item to save'));
    	$this->_redirectUrl(Mage::helper("sellerspage")->getUrl($seller,Mage::getStoreConfig('advancedfaq/config/url_key').'/new/'));

    }
}