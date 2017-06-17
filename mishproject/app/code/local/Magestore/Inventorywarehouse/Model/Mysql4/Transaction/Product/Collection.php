<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_Inventorywarehouse
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Inventorywarehouse Resource Collection Model
 * 
 * @category    Magestore
 * @package     Magestore_Inventorywarehouse
 * @author      Magestore Developer
 */
class Magestore_Inventorywarehouse_Model_Mysql4_Transaction_Product_Collection 
    extends Magestore_Inventoryplus_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('inventorywarehouse/transaction_product');
    }
    public function setReportCollection($transactionIdsArray){
        $this->addFieldToFilter('main_table.warehouse_transaction_id', array('in' => $transactionIdsArray));
        $this->getSelect()
            ->columns(
                array('transactionproductqty' => 'IFNULL(SUM(main_table.qty),0)')
            )->group('main_table.product_id');
    }
}