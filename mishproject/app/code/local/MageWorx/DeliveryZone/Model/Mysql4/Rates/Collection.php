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

class MageWorx_DeliveryZone_Model_Mysql4_Rates_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    // Need to filter by joined fields
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
        )
    );
    
    
    public function _construct()
    {
        parent::_construct();
        $this->_init('deliveryzone/rates');
    }
    
    protected function _afterLoad()
    {
        parent::_afterLoad();
        if ($this->getFlag('add_stores_to_result') && $this->_items) {
            /** @var Mage_Rule_Model_Abstract $item */
            foreach ($this->_items as $item) {
                $item->afterLoad();
            }
        }

        return $this;
    }

    /**
     * Init flag for adding rule store ids to collection result
     *
     * @param bool|null $flag
     *
     * @return Mage_Rule_Model_Resource_Rule_Collection_Abstract
     */
    public function addStoresToResult($flag = null)
    {
        $flag = ($flag === null) ? true : $flag;
        $this->setFlag('add_stores_to_result', $flag);
        return $this;
    }

    /**
     * Limit rules collection by specific stores
     *
     * @param int|array|Mage_Core_Model_Store $storeId
     *
     * @return Mage_Rule_Model_Resource_Rule_Collection_Abstract
     */
    public function addStoreFilter($storeId)
    {
        $entityInfo = $this->_getAssociatedEntityInfo('store');
        if (!$this->getFlag('is_store_table_joined')) {
            $this->setFlag('is_store_table_joined', true);
            if ($storeId instanceof Mage_Core_Model_Store) {
                $storeId = $storeId->getId();
            }

            $subSelect = $this->getConnection()->select()
                ->from(array('store' => $this->getTable($entityInfo['associations_table'])), '')
                ->where('store.' . $entityInfo['entity_id_field'] . ' IN (?)', $storeId);
            $this->getSelect()->exists(
                $subSelect,
                'main_table.' . $entityInfo['rate_id_field'] . ' = store.' . $entityInfo['rate_id_field']
            );
        }
        return $this;
    }
    
    /**
     * Provide support for store id filter
     *
     * @param string $field
     * @param mixed $condition
     *
     * @return Mage_Rule_Model_Resource_Rule_Collection_Abstract
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field == 'store_ids') {
            return $this->addStoreFilter($condition);
        }

        parent::addFieldToFilter($field, $condition);
        return $this;
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
            Mage::helper('rule')->__(
                'There is no information about associated entity type "%s".', $entityType
            )
        ); 
        throw $e;
    }
    
    public function filterByMethod($code) {
        $this->getSelect()
                ->join(array("carrier"=>$this->getTable('deliveryzone/rates_carrier')), 'main_table.rate_id=carrier.rate_id', array())
                ->where('carrier.method_id=?',$code)
                ->where('main_table.is_active=?',1);
        return $this;
    }
    
    public function filterByStore($storeId) {
        $this->getSelect()
                ->join(array("store"=>$this->getTable('deliveryzone/rates_store')), 'main_table.rate_id=store.rate_id', array())
                ->where('store.store_id IN(?)',array(0,$storeId))
                ->where('main_table.is_active=?',1);
        return $this;
    }
//    public function filterByWebsite($websiteId) {
//        $this->getSelect()
//                ->join(array("website"=>$this->getTable('deliveryzone/rates_website')), 'main_table.rate_id=website.rate_id', array())
//                ->where('website.website_id=?',$websiteId);
//        return $this;
//    }

    public function filterByCustomerGroup($groupId) {
        $this->getSelect()
                ->join(array("cg"=>$this->getTable('deliveryzone/rates_customergroup')), 'main_table.rate_id=cg.rate_id', array())
                ->where('cg.customergroup_id=?',$groupId)
                ->where('main_table.is_active=?',1);
        return $this;
    }

    public function sortList() {
        $this->getSelect()
                ->order('main_table.sort_order DESC');
        return $this;
    }
}