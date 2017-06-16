<?php

class Mercadolibre_Items_Model_Melifeedbacks extends Mage_Core_Model_Abstract {

    private $moduleName = "Items";
    private $fileName = "Melifeedbacks.php";
    //message variable
    private $infoMessage = "";
    private $errorMessage = "";
    private $successMessage = "";
    private $errorMessageLog = "";

    public function _construct() {
        parent::_construct();
        $this->_init('items/melifeedbacks');
    }

    function getAllFeedbackFromAPIhit() {

        try {
            $commonModel = Mage::getModel('items/common');
            if (Mage::helper('items')->getMlAccessToken()) {
                $access_token = Mage::helper('items')->getMlAccessToken();
            } else {
                $this->errorMessage = "Error :: Access Token Not Found OR Invalid";
                $commonModel->saveLogger($this->moduleName, "Exception", $this->fileName, $this->errorMessage);
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('items')->__($this->errorMessage));
                $this->_redirect('items/adminhtml_itempublishing/');
                return;
            }



            $this->to = Mage::getStoreConfig("mlitems/meligeneralsetting/notificationemailid", Mage::app()->getStore());
//            $item =  Mage::getModel('items/mercadolibreitem')
//					-> getCollection()
//					-> setOrder('meli_item_id', 'ASC')
//					-> addFieldToFilter('meli_item_id', array('neq' => ''))
//					-> addFieldToSelect('meli_item_id');

            if (count($item->getData()) > 0) {
                
            } else {
                $this->errorMessageLog = "There is no feedback found" .
                        $commonModel->saveLogger($this->moduleName, "Exception", $this->fileName, $this->errorMessageLog);
            }
        } catch (Exception $e) {
            $commonModel->saveLogger($this->moduleName, "Exception", $this->fileName, $e->getMessage());
            $commonModel->sendNotificationMail($this->to, 'Exception::ML Catergories All Data Cron', $e->getMessage());
        }
    }

}