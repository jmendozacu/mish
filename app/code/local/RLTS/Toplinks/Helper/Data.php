<?php

class RLTS_Toplinks_Helper_Data extends Mage_Core_Helper_Abstract{
    
    
    public function getNewlink1Label(){
        return Mage::getStoreConfig('toplinks/newlink_config/new1');
    }
    public function getNewLink1Url(){
                return Mage::getStoreConfig('toplinks/newlink_config/newlink1');

    }
    
    public function getNewlink2Label(){
                return Mage::getStoreConfig('toplinks/newlink_config/new2');

    }
    public function getNewLink2Url(){
                return Mage::getStoreConfig('toplinks/newlink_config/newlink2');

    }
    
    public function getNewlink3Label(){
                return Mage::getStoreConfig('toplinks/newlink_config/new3');

    }
    public function getNewLink3Url(){
                return Mage::getStoreConfig('toplinks/newlink_config/newlink3');

    }
    
    public function getNewlink4Label(){
                return Mage::getStoreConfig('toplinks/newlink_config/new4');

    }
    public function getNewLink4Url(){
                return Mage::getStoreConfig('toplinks/newlink_config/newlink4');

    }
    
    public function getNewlink5Label(){
                return Mage::getStoreConfig('toplinks/newlink_config/new5');

    }
    public function getNewLink5Url(){
                return Mage::getStoreConfig('toplinks/newlink_config/newlink5');

    }
    
    
    
     public function getNewFooterlink1Label(){
        return Mage::getStoreConfig('toplinks/newfooterlink/new1');
    }
    public function getNewFooterLink1Url(){
                return Mage::getStoreConfig('toplinks/newfooterlink/newlink1');

    }
    
    public function getNewFooterlink2Label(){
                return Mage::getStoreConfig('toplinks/newfooterlink/new2');

    }
    public function getNewFooterLink2Url(){
                return Mage::getStoreConfig('toplinks/newfooterlink/newlink2');

    }
    
    public function getNewFooterlink3Label(){
                return Mage::getStoreConfig('toplinks/newfooterlink/new3');

    }
    public function getNewFooterLink3Url(){
                return Mage::getStoreConfig('toplinks/newfooterlink/newlink3');

    }
    
    public function getNewFooterlink4Label(){
                return Mage::getStoreConfig('toplinks/newfooterlink/new4');

    }
    public function getNewFooterLink4Url(){
                return Mage::getStoreConfig('toplinks/newfooterlink/newlink4');

    }
    
    public function getNewFooterlink5Label(){
                return Mage::getStoreConfig('toplinks/newfooterlink/new5');

    }
    public function getNewFooterLink5Url(){
                return Mage::getStoreConfig('toplinks/newfooterlink/newlink5');

    }
    
    
    
     public function getNewNavigationLabel(){
        return Mage::getStoreConfig('toplinks/customer_newlink_config/new1');
    }
    public function getNewNavigationLink1Url(){
                return Mage::getStoreConfig('toplinks/customer_newlink_config/newlink1');
    }
    public function getNewNavigationlink2Label(){
                return Mage::getStoreConfig('toplinks/customer_newlink_config/new2');

    }
    public function getNewNavigationLink2Url(){
                return Mage::getStoreConfig('toplinks/customer_newlink_config/newlink2');
    }
    
    public function getNewNavigationlink3Label(){
                return Mage::getStoreConfig('toplinks/customer_newlink_config/new3');

    }
    public function getNewNavigationLink3Url(){
                return Mage::getStoreConfig('toplinks/customer_newlink_config/newlink3');
    }
    
    public function getNewNavigationlink4Label(){
                return Mage::getStoreConfig('toplinks/customer_newlink_config/new4');
    }
    public function getNewNavigationLink4Url(){
                return Mage::getStoreConfig('toplinks/customer_newlink_config/newlink4');

    }
    public function getNewNavigationlink5Label(){
                return Mage::getStoreConfig('toplinks/customer_newlink_config/new5');
    }
    public function getNewNavigationLink5Url(){
                return Mage::getStoreConfig('toplinks/customer_newlink_config/newlink5');

    }
    
    
    public function getUrlOf($parameter){
       return $this->_getUrl($parameter);
    }
    
}
