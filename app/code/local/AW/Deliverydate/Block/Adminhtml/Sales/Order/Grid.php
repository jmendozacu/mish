<?php
/**
* aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Deliverydate
 * @version    1.3
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */

class AW_Deliverydate_Block_Adminhtml_Sales_Order_Grid extends Mage_Adminhtml_Block_Sales_Order_Grid{

     protected function _getCollectionClass(){
        return 'deliverydate/order_collection';
    }

     protected function _prepareColumns()
    {

         $this->addColumn('real_order_id', array(
            'header'=> Mage::helper('sales')->__('Order #'),
            'width' => '80px',
            'type'  => 'text',
            'index' => 'increment_id',
        ));

           if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'    => Mage::helper('sales')->__('Purchased from (store)'),
                'index'     => 'store_id',
                'type'      => 'store',
                'store_view'=> true,
                'display_deleted' => true,
            ));
        }

         $this->addColumn('created_at', array(
            'header' => Mage::helper('sales')->__('Purchased On'),
            'index' => 'created_at',
            'type' => 'datetime',
            'width' => '100px',
        ));

        $this->addColumn('aw_deliverydate_date', array(
            'header' => Mage::helper('deliverydate')->__('Delivery Date'),
            'index' => 'aw_deliverydate_date',
            'type' => 'date',
            'width' => '100px',
            'sortable'=>$this->_sortableForVersion(),
            'filter_condition_callback' => array($this, 'filterDeliveryDate'),
        ));
        return parent::_prepareColumns();
    }

    protected function _sortableForVersion() {
        $rez = true;
        if (preg_match('/^1.4.0/', Mage::getVersion()) || preg_match('/^1.3/', Mage::getVersion())) {
            $rez = false;
        }
        return $rez;
    }

    protected function filterDeliveryDate($collection, $column) {

        $val = $column->getFilter()->getValue();

        if(!$val) { return $this; }      
         
        $dateFrom = '0000-00-00 00:00:00';
        if (isset($val['from'])) {
            $dateFrom = $this->_getMysqlFormat($val['orig_from'], $val['locale']);
        }
        $dateTo = '9999-03-23 00:00:00';
        if (isset($val['to'])) {           
            $dateTo = $this->_getMysqlFormat($val['orig_to'], $val['locale']);
        }
      
       $collection->getSelect()
               ->joinleft(array('deliveryTable' => Mage::getSingleton('core/resource')->getTableName("deliverydate/delivery")), $this->_getSalesOrdersTableSyn() . '.entity_id = deliveryTable.order_id', array())
               ->where("deliveryTable.delivery_date >= '{$dateFrom}' AND deliveryTable.delivery_date <= '{$dateTo}'");
        
     
    }
    
    private function _getMysqlFormat($date, $locale) {
       
         $date = new Zend_Date($date, Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT), $locale);
         $date->setTimezone('UTC');
         return $date->toString(Varien_Date::DATE_INTERNAL_FORMAT);
         
    }

      

     // stub for 1330
    protected function _prepareCollection() {
        parent::_prepareCollection();

        if (preg_match('/^1.4.0/', Mage::getVersion()) || preg_match('/^1.3/', Mage::getVersion())) {

            $_deliveryTable = Mage::getSingleton('core/resource')->getTableName("deliverydate/delivery");

            $collection = $this->getCollection();

            $collection->getSelect()
                    ->joinleft(array('od' => $_deliveryTable), $this->_getSalesOrdersTableSyn() . '.entity_id = od.order_id', array())
                    ->columns(array('aw_deliverydate_date' => 'od.delivery_date'));

            $collection->clear();
            $this->setCollection($collection);
        }
    }

       protected function _getSalesOrdersTableSyn() {
        $syn = 'main_table';
        if (preg_match('/^1.4.0/', Mage::getVersion()) || preg_match('/^1.3/', Mage::getVersion())) {
            $syn = 'e';
        } elseif (preg_match('/^1.4.1/', Mage::getVersion())) {
            $syn = 'main_table';
        }
        return $syn;
    }
 }
