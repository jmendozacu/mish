<?php
/**
 * MageWorx
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MageWorx EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.mageworx.com/LICENSE-1.0.html
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@mageworx.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.mageworx.com/ for more information
 * or send an email to sales@mageworx.com
 *
 * @category   MageWorx
 * @package    MageWorx_DeliveryZone
 * @copyright  Copyright (c) 2013 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * MageWorx DeliveryZone extension
 *
 * @category   MageWorx
 * @package    MageWorx_DeliveryZone
 * @author     MageWorx Dev Team <dev@mageworx.com>
 */

class MageWorx_DeliveryZone_Model_Rates extends Mage_Rule_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('deliveryzone/rates');
    }
    
    /**
     * 
     * @return MageWorx_DeliveryZone_Model_Rates_Condition_Combine
     */
    public function getConditionsInstance() {
        return Mage::getModel('deliveryzone/rates_condition_combine');
    }

    /**
     * 
     * @return MageWorx_DeliveryZone_Model_Rates_Action_Collection
     */
    public function getActionsInstance() {
        return Mage::getModel('deliveryzone/rates_action_collection');
    }
    
    /**
     * Depricated: Validate data
     * @param Varien_Object $object
     * @return boolean
     */
    public function validateData(Varien_Object $object) {
        parent::validateData($object);
        return true; // temp
    }
    /**
     * After save
     */
    protected function _afterSave() {
        parent::_afterSave();
//        $this->_saveWebsites();
        $this->_saveStores();
        $this->_saveCustomerGroups();
        $this->_saveCarrers();
    }
    
    /**
     * Prepare and save websites
     * @return MageWorx_DeliveryZone_Model_Rates
     */
//    private function _saveWebsites() {
//        $websiteModel = Mage::getModel('deliveryzone/rates_website');
//        $collection = $websiteModel->getCollection()->loadByRate($this->getRateId());
//        foreach ($collection as $item) {
//            $item->delete();
//        }
//    
//        foreach ($this->getWebsiteIds() as $website) {
//            $websiteModel->setEntityId(NULL)->setWebsiteId($website)->setRateId($this->getId())->save();
//        }
//        return $this;
//    }
    
    /**
     * Prepare and save websites
     * @return MageWorx_DeliveryZone_Model_Rates
     */
    private function _saveStores() {
        $storeModel = Mage::getModel('deliveryzone/rates_store');
        $collection = $storeModel->getCollection()->loadByRate($this->getRateId());
        foreach ($collection as $item) {
            $item->delete();
        }
    
        foreach ($this->getStoreIds() as $store) {
            $storeModel->setEntityId(NULL)->setStoreId($store)->setRateId($this->getId())->save();
        }
        return $this;
    }
    
    /**
     * Prepare and save customer groups
     * @return MageWorx_DeliveryZone_Model_Rates
     */
    private function _saveCustomerGroups() {
        $customergroupModel = Mage::getModel('deliveryzone/rates_customergroup');
        $collection = $customergroupModel->getCollection()->loadByRate($this->getRateId());
        foreach ($collection as $item) {
            $item->delete();
        }
    
        foreach ($this->getCustomerGroupIds() as $customergroup) {
            $customergroupModel->setEntityId(NULL)->setCustomergroupId($customergroup)->setRateId($this->getId())->save();
        }
        return $this;
    }
    
    /**
     * Prepare and save carrier methods
     * @return MageWorx_DeliveryZone_Model_Rates
     */
    private function _saveCarrers() {
        $carrierModel = Mage::getModel('deliveryzone/rates_carrier');
        $collection = $carrierModel->getCollection()->loadByRate($this->getRateId());
        foreach ($collection as $item) {
            $item->delete();
        }
    
        foreach ($this->getCarrierMethods() as $aCarrier) {
            list($carrier,$methodId) = explode('_', $aCarrier);
            $carrierModel->setEntityId(NULL)->setCarrier($carrier)->setMethodId($aCarrier)->setRateId($this->getId())->save();
        }
        return $this;
    }
}
