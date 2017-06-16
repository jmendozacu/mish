<?php
/**
 * Venustheme
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Venustheme EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.venustheme.com/LICENSE-1.0.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.venustheme.com/ for more information
 *
 * @category   Ves
 * @package    Ves_FAQ
 * @copyright  Copyright (c) 2014 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */

/**
 * Ves FAQ Extension
 *
 * @category   Ves
 * @package    Ves_FAQ
 * @author     Venustheme Dev Team <venustheme@gmail.com>
 */
class Ves_FAQ_IndexController extends Mage_Core_Controller_Front_Action
{
    public function preDispatch()
    {
        parent::preDispatch();
        if( !Mage::getStoreConfigFlag('ves_faq/general_setting/enable') ) {
            $this->norouteAction();
        }
    }

    /**
     * index action
     */
    public function indexAction()
    {
        
    	$status = Mage::getStoreConfig('ves_faq/general_setting/enable');

    	if($status == "0"){
    		$this->_redirect('cms/noRoute');
    	}
    	$this->loadLayout();
    	$this->renderLayout();
    }

    public function saveAction(){

        $status_question = Mage::getStoreConfig('ves_faq/product_page/status_question');

        if( Mage::getStoreConfigFlag('ves_faq/recaptcha/enabled') ){
            $recaptcha = Mage::helper('ves_faq/recaptcha')
            ->setKeys( Mage::getStoreConfig('ves_faq/recaptcha/private_key'),
             Mage::getStoreConfig("ves_faq/recaptcha/public_key") )
            ->getReCapcha();
            $check = $recaptcha->verify( $this->getRequest()->getParam('recaptcha_challenge_field'),
                $this->getRequest()->getParam('recaptcha_response_field') )->isValid();
            if( !$check ){
                Mage::getSingleton('core/session')->addError(Mage::helper('ves_faq')->__("The reCAPTCHA wasn't entered correctly. Go back and try it again."));
                $this->_redirectReferer();
                return;
            }
        }

        try{
            $data = $this->getRequest()->getPost();
            $model = Mage::getModel('ves_faq/question');
            $data['status'] = 2;
            $sessionCustomer =Mage::getSingleton("customer/session");
            if (!$sessionCustomer->isLoggedIn()) {
                $data['visibility'] = 1;
            }else{
                $data['status'] = $status_question;
                $customer = Mage::helper('customer')->getCustomer();
                $data['customer_id'] = $customer->getId();
            }
            $model->setData($data);
            $storeId = Mage::app()->getStore()->getId();
            $model->setData('stores', array($storeId));
            if ($model->getCreatedAt == NULL || $model->getUpdateAt() == NULL) {
                $model->setCreatedAt(now())
                ->setUpdatedAt(now());
            } else {
                $model->setUpdatedAt(now());
            }

            $enable_email = Mage::getStoreConfig('ves_faq/email/enabled');
            $templateId = Mage::getStoreConfig('ves_faq/email/email_template');
            $senderName = Mage::getStoreConfig('ves_faq/email/sender_name');
            $senderEmail = Mage::getStoreConfig('ves_faq/email/sender_email');
            if($enable_email && $senderEmail && $templateId && $senderName && $senderEmail){
                /*** SEND MAIL ***/
                $translate = Mage::getSingleton('core/translate');
                /* @var $translate Mage_Core_Model_Translate */
                $translate->setTranslateInline(false);
                try {
                    $error = false;
                    $sender = array(
                        'name' => $senderName,
                        'email' => $senderEmail
                        );
                    $product = Mage::getSingleton('catalog/product')->load((int)$data['product_id']);
                    $category = Mage::getModel('ves_faq/category')->load($data['category_id']);
                    $data['category_name'] = $category->getName();
                    $data['product_name'] = $product->getName();
                    $data['product_url'] = $product->getProductUrl();
                    $data['product_image'] = $product->getThumbnailUrl();
                    $data['product_price'] = $product->getPrice();
                    $data['product_special_price'] = $product->getFinalPrice();
                    $data['product_sku'] = $product->getSku();
                    $data['name'] = $senderName;
                    $data['email'] = $senderEmail;
                    $data['question_title'] = $data['title'];
                    $data['ip_address'] = Mage::helper('core/http')->getRemoteAddr(true);
                    $data['created'] = date( 'Y-m-d H:i:s' );
                    $data['category_url'] = Mage::helper('ves_faq')->getCategoryLink($category);
                    if ($error) {
                        throw new Exception();
                    }
                    $postObject = new Varien_Object();
                    $postObject->setData($data);
                    $mailTemplate = Mage::getModel('core/email_template');
                    /* @var $mailTemplate Mage_Core_Model_Email_Template */
                    $mailTemplate->setDesignConfig(array('area' => 'frontend'))
                    ->setReplyTo($data['author_email'])
                    ->sendTransactional(
                     $templateId,
                     $sender,
                     $data['author_email'],
                     null,
                     array('data' => $postObject)
                     );
                }catch (Exception $e) {
                    $translate->setTranslateInline(true);

                    Mage::getSingleton('core/session')->addError(Mage::helper('ves_faq')->__('Unable to submit your request. Please, try again later'));
                    $this->_redirectReferer();
                    return;
                }
            }

            $model->save();
            $product = Mage::getModel('catalog/product')->load($data['product_id']);
            Mage::getSingleton('catalog/session')->addSuccess(
                Mage::helper('ves_faq')->__('You question has upload sucessfully, we will answer that as soon as possible')
                );
            $this->_redirectReferer();
        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError($e->getMessage());
            $this->_redirectReferer();
            return;
        }
    }

    public function accountAction(){
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * index action
     */
    public function searchAction()
    {
        $keyword = $this->getRequest()->getParam('s');
        if(strlen($keyword)<=0){
            $this->_redirect('faq/index/index');
        }
        $this->loadLayout();
        $this->renderLayout();
    }
}