<?php
class OTTO_AdvancedFaq_HomeController extends Mage_Core_Controller_Front_Action
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
    public function voteAction(){
    
    	$id = $this->getRequest()->getParam('id');

    	$url= "faq/home";

    	$faq = Mage::getModel("advancedfaq/faq")->load($id);
    	$votes = $faq->getData('votes');
    	$rating = $faq->getData('rating');
    	$url .= $faq->getData('url_key').Mage::getStoreConfig('advancedfaq/config/question_suffix');
    	$new_rate = ($votes*$rating)+$this->getRequest()->getPost('rating');
    	$new_rate = ($new_rate)/($votes+1);
    	 
    	try {
    		$model = Mage::getModel('advancedfaq/faq');
    			
    		$model->setData(array('rating'=>$new_rate,'votes'=>$votes+1))->setId($id)->save();
    
    		$cookie = Mage::getSingleton('core/cookie');
    		$ratingIds = explode(',',$cookie->get('otto_advancedfaq_rating_ids'));
    		if(!in_array($model->getId(), $ratingIds)){
    			$ratingIds[] = $model->getId();
    			$cookie->set('otto_advancedfaq_rating_ids',implode(',', $ratingIds) ,true);
    		}
    		Mage::getSingleton('core/session')->addSuccess(Mage::helper('advancedfaq')->__('Your vote has been accepted. Thank you!'));
    		if(Mage::helper('advancedfaq')->getSecureUrl() == 1){
    			$this->_redirect($url,array('_secure'=>true));
    		}
    		else{
    			$this->_redirect($url);
    		}
    	} catch (Exception $e) {
    		Mage::getSingleton('core/session')->addError($e->getMessage());
    		if(Mage::helper('advancedfaq')->getSecureUrl() == 1){
    			$this->_redirect($url,array('_secure'=>true));
    		}
    		else{
    			$this->_redirect($url);
    		}
    	}
    }
    
    
    public function voteajaxAction(){
    
    	$id = $this->getRequest()->getParam('id');
    
    	$faq = Mage::getModel("advancedfaq/faq")->load($id);
    	$votes = $faq->getData('votes');
    	$rating = $faq->getData('rating');
    	$url .= $faq->getData('url_key').Mage::getStoreConfig('advancedfaq/config/question_suffix');
    	$new_rate = ($votes*$rating)+$this->getRequest()->getParam('rating');
    	$new_rate = ($new_rate)/($votes+1);
    
    	try {
    		$model = Mage::getModel('advancedfaq/faq');
    		 
    		$model->setData(array('rating'=>$new_rate,'votes'=>$votes+1))->setId($id)->save();
    
    		$cookie = Mage::getSingleton('core/cookie');
    		$ratingIds = explode(',',$cookie->get('otto_advancedfaq_rating_ids'));
    		if(!in_array($model->getId(), $ratingIds)){
    			$ratingIds[] = $model->getId();
    			$cookie->set('otto_advancedfaq_rating_ids',implode(',', $ratingIds) ,true);
    		}

    	} catch (Exception $e) {
    		Mage::getSingleton('core/session')->addError($e->getMessage());
    	}
    }
}