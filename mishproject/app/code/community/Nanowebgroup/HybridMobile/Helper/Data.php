<?php
/**
 * 
 */

class Nanowebgroup_HybridMobile_Helper_Data extends Mage_Core_Helper_Abstract
{
    private $HybridMobile = 'false';
    private $Time = 0;

    public function validateAction() {
       
        $Switch = new Mage_Core_Model_Config(); 
        $status = Mage::getStoreConfig('hybrid_mobile/validate/status', Mage::app()->getStore());
        $time = Mage::getStoreConfig('hybrid_mobile/validate/time', Mage::app()->getStore());
        // $path = Mage::getModuleDir('controllers', 'Nanowebgroup_HybridMobile');
        if($status == NULL || $time == NULL) {
            $this->apiAction();
        } else {
            $this->HybridMobile = $status;
            $this->Time = $time;
        } 

        $current = Mage::getModel('core/date')->timestamp(time('now'));
        if($this->HybridMobile == 'true' || $this->HybridMobile == 'trial') {
            if($this->Time <= $current) {
                $this->apiAction();
            } else {
                $Switch ->saveConfig('hybrid_mobile/general/enable', 1, 'default', 0);
                return true;
            }
        } else {
            if($this->Time <= $current) {
                $this->apiAction();
            } else {
                $Switch ->saveConfig('hybrid_mobile/general/enable', 0, 'default', 0);
                return false;
            }
        }

        $Switch ->saveConfig('hybrid_mobile/general/enable', 0, 'default', 0);
        return false;
    }

    private function parseResult($request){
        if (!$request) {
            return false;
        } else {
            foreach ($request as &$value) {
                $value = trim(str_replace(":", ',', $value));
                $list[] = explode(",", $value);
                $val = explode(",", $value);
                foreach ($val as $k) {
                    if($k == 'true' || $k == 'false' || $k == 'trial') {
                        $this->HybridMobile = $k;
                    } else {
                        $this->Time = (int)$k;
                    }
                }
            }
        }
       return $list;

    }

    private function apiAction() {
        $email = Mage::getStoreConfig('hybrid_mobile/registration/email', Mage::app()->getStore());
        $domain = Mage::getBaseUrl (Mage_Core_Model_Store::URL_TYPE_WEB);
        $current = Mage::getModel('core/date')->timestamp(time('now'));
        
        if($email == '' || $email == NULL) {
            return false;
        } else {
            
            $data = array(
              'action' => 'add_giganto',
              'data' => $domain,
              'email' => $email,
              'type' => 'mobile',
              'current' => $current
            );

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "http://www.nanowebgroup.com/wp-admin/admin-ajax.php");
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HEADER, FALSE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            $response = curl_exec($ch);
            
           
            $response = trim(str_replace(array('{', '}', '"' ), '', $response));

            $request = explode(",", $response);
            $list = $this->parseResult($request);

            $Switch = new Mage_Core_Model_Config(); 
            $Switch ->saveConfig('hybrid_mobile/validate/status', $this->HybridMobile, 'default', 0);
            $Switch ->saveConfig('hybrid_mobile/validate/time', $this->Time, 'default', 0);

            // $path = Mage::getModuleDir('controllers', 'Nanowebgroup_HybridMobile');
            // $fp = fopen($path.DS.'hybridmobile.csv', 'w');

            // foreach ($list as $fields) {
            //     fputcsv($fp, $fields);
            // }
        }
        

    }

    public function restrictSiteAccess(){

        $allowed = Mage::getStoreConfig('hybrid_mobile/general/enable', Mage::app()->getStore());

         return $allowed;
   }
}