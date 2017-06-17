<?php
class VES_VendorsContact_ContactController extends Mage_Core_Controller_Front_Action
{

    public function preDispatch()
    {
        parent::preDispatch();
        $check = Mage::helper("vendorscontact")->isEnabled();
        if(!$check) {
            $this->norouteAction();
        }
    }

    public function indexAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
        $this->renderLayout();
    }

    public function postAction()
    {
        $vendor =  Mage::registry("current_vendor");
        $post = $this->getRequest()->getPost();
        if ( $post ) {
            $email_to = Mage::helper("vendorscontact")->getEmailVendor();
          //  echo $email_to;exit;
            $translate = Mage::getSingleton('core/translate');
            /* @var $translate Mage_Core_Model_Translate */
            $translate->setTranslateInline(false);
            try {
                $postObject = new Varien_Object();
                $postObject->setData($post);

                $error = false;

                if (!Zend_Validate::is(trim($post['name']) , 'NotEmpty')) {
                    $error = true;
                }

                if (!Zend_Validate::is(trim($post['comment']) , 'NotEmpty')) {
                    $error = true;
                }

                if (!Zend_Validate::is(trim($post['email']), 'EmailAddress')) {
                    $error = true;
                }

                if (Zend_Validate::is(trim($post['hideit']), 'NotEmpty')) {
                    $error = true;
                }

                if ($error) {
                    throw new Exception();
                }
               // $sendor["name"] = $post['name'];
               // $sendor["email"] = $post['email'];
                $sender = Mage::helper("vendorscontact")->getEmailTo();
				
			   $template = Mage::helper("vendorscontact")->getEmailTemplate() ? Mage::helper("vendorscontact")->getEmailTemplate()  : "vendorcontact_email_email_template";
				 
                $mailTemplate = Mage::getModel('core/email_template');
                /* @var $mailTemplate Mage_Core_Model_Email_Template */
                $mailTemplate->setDesignConfig(array('area' => 'frontend'))
                    ->setReplyTo($post['email'])
                    ->sendTransactional(
                        $template,
                        $sender,
                        $email_to,
                        null,
                        array('data' => $postObject)
                    );
               // echo "test";exit;
                if (!$mailTemplate->getSentSuccess()) {
                    throw new Exception();
                }

                $translate->setTranslateInline(true);

                Mage::getSingleton('customer/session')->addSuccess(Mage::helper('vendorscontact')->__('Your inquiry was submitted and will be responded to as soon as possible. Thank you for contacting us.'));
                $this->_redirectUrl(Mage::helper("vendorspage")->getUrl($vendor,"contact"));

                return;
            } catch (Exception $e) {
                $translate->setTranslateInline(true);
               // Mage::getSingleton('customer/session')->addError($e->getMessage());
                Mage::getSingleton('customer/session')->addError(Mage::helper('vendorscontact')->__('Unable to submit your request. Please, try again later'));
                $this->_redirectUrl(Mage::helper("vendorspage")->getUrl($vendor,"contact"));
                return;
            }

        } else {
            $this->_redirectUrl(Mage::helper("vendorspage")->getUrl($vendor,"contact"));
        }
    }
}