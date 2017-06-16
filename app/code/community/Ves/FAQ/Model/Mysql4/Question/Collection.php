<?php
/**
 * Venustheme
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Venustheme EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.venustheme.com/LICENSE-1.0.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.venustheme.com/ for more information
 *
 * @category   Ves
 * @package    Ves_FAQ
 * @copyright  Copyright (c) 2014 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */

/**
 * Ves FAQ Extension
 *
 * @category   Ves
 * @package    Ves_FAQ
 * @author     Venustheme Dev Team <venustheme@gmail.com>
 */
class Ves_FAQ_Model_Mysql4_Question_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('ves_faq/question');
    }

    /**
     * Add Filter by store
     *
     * @param int|Mage_Core_Model_Store $store
     * @return Ves_FAQ_Model_Mysql4_Category_Collection
     */
    public function addStoreFilter($store = null) {

        $store = Mage::app()->getStore($store);
        $this->getSelect()->join(
            array('store_table' => $this->getTable('ves_faq/question_store')),
            'main_table.question_id = store_table.question_id',
            array()
            )
        ->where('store_table.store_id in (?)', array(0, $store->getId()))
        ->group('main_table.question_id');
        return $this;
    }

    /**
     * Add Filter by Visibility
     *
     * @param int|Mage_Core_Model_Store $store
     * @return Ves_FAQ_Model_Mysql4_Category_Collection
     */
    public function addVisibilityFilter(){
        $select = $this->getSelect();
        $customer = Mage::helper('customer')->getCustomer();
        $sessionCustomer = Mage::getSingleton("customer/session");
        if ($sessionCustomer->isLoggedIn()) {
            $customerId = $customer->getId();
            $select->where('visibility = 1')
            ->orWhere('visibility = 2 AND customer_id = ?',$customerId);
        }else{
            $select->where('visibility = 1');
        }
        return $this;
    }
}