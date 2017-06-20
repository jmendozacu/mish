<?php

class Nanowebgroup_HybridMobile_Model_Package extends Mage_Core_Model_Design_Package
{
    protected function _checkUserAgentAgainstRegexps($regexpsConfigPath)
    {
      // session_start();
      // $validate = Mage::helper('hybridmobile')->validateAction();
      $enable = Mage::getStoreConfig('hybrid_mobile/general/enable', Mage::app()->getStore());
      if($enable) {
        if( Mage::app()->getRequest()->getParam('view')=='mobile' || (isset($_SESSION['nanoismobile']) && $_SESSION['nanoismobile']) ){

           $_SESSION['nanoshowmobilelink'] = true;
           $_SESSION['nanoismobile'] = false;
           if(isset($_SESSION['nanoviewdesktop'])){
                 unset($_SESSION['nanoviewdesktop']);
           }
           return parent::_checkUserAgentAgainstRegexps($regexpsConfigPath);

        }
        
        if( Mage::app()->getRequest()->getParam('view')=='desktop'){

            if (!isset($_SESSION['nanoviewdesktop'])) {

               $_SESSION['nanoviewdesktop']=true;
               $_SESSION['nanoshowmobilelink'] = true;
            }
            return false;
        }

        if (isset($_SESSION['nanoviewdesktop']) && $_SESSION['nanoviewdesktop']){
            return false;
        }

        if (isset($_SESSION['hybridmobileswitcher']) && $_SESSION['hybridmobileswitcher']) {
            $_SESSION['nanoismobile']=true;
            $_SESSION['nanoshowmobilelink'] = true;
            return false;
        }
        
        return parent::_checkUserAgentAgainstRegexps($regexpsConfigPath);
      }
    }
}