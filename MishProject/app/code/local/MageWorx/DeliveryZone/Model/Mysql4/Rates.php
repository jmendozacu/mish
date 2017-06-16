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

class MageWorx_DeliveryZone_Model_Mysql4_Rates extends Mage_Core_Model_Mysql4_Abstract
{
    
    protected $_associatedEntitiesMap = array(
        'website' => array(
            'associations_table' => 'deliveryzone/rates_website',
            'rate_id_field'      => 'rate_id',
            'entity_id_field'    => 'website_id'
        ),
        'store' => array(
            'associations_table' => 'deliveryzone/rates_store',
            'rate_id_field'      => 'rate_id',
            'entity_id_field'    => 'store_id'
        ),
        'customer_group' => array(
            'associations_table' => 'deliveryzone/rates_customergroup',
            'rate_id_field'      => 'rate_id',
            'entity_id_field'    => 'customergroup_id'
        ),
        'carrier_methods' => array(
            'associations_table' => 'deliveryzone/rates_carrier',
            'rate_id_field'      => 'rate_id',
            'entity_id_field'    => 'method_id'
        )
    );
    
    public function _construct()
    {
        $this->_init('deliveryzone/rates', 'rate_id');
    }
    
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        $object->setData('customer_group_ids', (array)$this->getCustomerGroupIds($object->getId()));
        $object->setData('store_ids', (array)$this->getStoreIds($object->getId()));
        $object->setData('carrier_methods', (array)$this->getCarrierMethods($object->getId()));

        return parent::_afterLoad($object);
    }
    
    /**
     * Retrieve rule's associated entity Ids by entity type
     *
     * @param int $ruleId
     * @param string $entityType
     *
     * @return array
     */
    public function getAssociatedEntityIds($rateId, $entityType)
    {
        $entityInfo = $this->_getAssociatedEntityInfo($entityType);

        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable($entityInfo['associations_table']), array($entityInfo['entity_id_field']))
            ->where($entityInfo['rate_id_field'] . ' = ?', $rateId);
        return $this->_getReadAdapter()->fetchCol($select);
    }

    /**
     * Retrieve carrier methods ids of specified rule
     *
     * @param int $ruleId
     * @return array
     */
    public function getCarrierMethods($rateId)
    {
        return $this->getAssociatedEntityIds($rateId, 'carrier_methods');
    }
    
    /**
     * Retrieve website ids of specified rule
     *
     * @param int $ruleId
     * @return array
     */
//    public function getWebsiteIds($rateId)
//    {
//        return $this->getAssociatedEntityIds($rateId, 'website');
//    }
    /**
     * Retrieve store ids of specified rule
     *
     * @param int $ruleId
     * @return array
     */
    public function getStoreIds($rateId)
    {
        return $this->getAssociatedEntityIds($rateId, 'store');
    }

    /**
     * Retrieve customer group ids of specified rule
     *
     * @param int $ruleId
     * @return array
     */
    public function getCustomerGroupIds($rateId)
    {
        return $this->getAssociatedEntityIds($rateId, 'customer_group');
    }
    
    /**
     * Retrieve correspondent entity information (associations table name, columns names)
     * of rule's associated entity by specified entity type
     *
     * @param string $entityType
     *
     * @return array
     */
    protected function _getAssociatedEntityInfo($entityType)
    {
        if (isset($this->_associatedEntitiesMap[$entityType])) {
            return $this->_associatedEntitiesMap[$entityType];
        }

        $e = Mage::exception(
            'Mage_Core',
            Mage::helper('deliveryzone')->__('There is no information about associated entity type "%s".', $entityType)
        );
        throw $e;
    }
}