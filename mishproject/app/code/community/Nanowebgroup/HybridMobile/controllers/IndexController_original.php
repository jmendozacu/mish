<?php
require_once 'Zend/Http/UserAgent.php';

class Nanowebgroup_HybridMobile_IndexController extends Mage_Core_Controller_Front_Action
{
    public function toMobileAction() {
        if (isset($_SESSION['hybridmobileswitcher'])) unset($_SESSION['hybridmobileswitcher']);
        return $this->_redirect('/');
    }
    
    public function toDesktopAction() {
        $_SESSION['hybridmobileswitcher'] = true;
        return $this->_redirect('/');
    }
    
    public function indexAction(){

        if( $this->getRequest()->getParam('view')=='desktop'){

            $ua = new Zend_Http_UserAgent();
            $ua->setUserAgent('Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1)');
            return $this->_redirect('/');
            //var_dump
       }

    }
}