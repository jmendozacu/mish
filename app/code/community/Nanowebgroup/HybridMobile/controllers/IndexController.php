<?php
require_once 'Zend/Http/UserAgent.php';
class Nanowebgroup_HybridMobile_IndexController extends Mage_Core_Controller_Front_Action
{
    public function toMobileAction() {
     $ua = new Zend_Http_UserAgent();
     $uastr = $ua->getUserAgent();
     
       if(!$uastr=='Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1)'){
          var_dump('desktop');
        if (isset($_SESSION['hybridmobileswitcher'])) unset($_SESSION['hybridmobileswitcher']);
      }
        return $this->_redirect('/');
    }
    
    public function toDesktopAction() {
      if($_SESSION['HTTP_USER_AGENT']=='Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1)'){
          $_SESSION['hybridmobileswitcher'] = false;
      }

        return $this->_redirect('/');
    }
    
    public function indexAction(){
      var_dump('index action'); die;
    }

    public function authorizeAction() {        
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setTitle($this->__('HybridMobile Authorization'));
        $this->renderLayout();
    }
    
}