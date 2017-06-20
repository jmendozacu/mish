<?php
class VES_VendorsReport_Model_Resource_Order_Collection extends Mage_Sales_Model_Resource_Report_Order_Collection
{
	protected $_write_adapter;
	
	protected $_grouped_period;
	
	public function __construct($resource = null){
		parent::__construct();
	}
	
	protected function _construct($resource = null){
		
	}
	
	protected function _getWriteAdapter(){
		if(!$this->_write_adapter){
			$resource = Mage::getSingleton('core/resource');
			$this->_write_adapter = $resource->getConnection('core_write');
		}
		return $this->_write_adapter;
	}
	
	public function setVendorId($vendorId){
		$this->_vendor_id = $vendorId;
		$this->_select->where('o.vendor_id = ?',$this->_vendor_id);
		return $this;
	}
	
	/**
     * Init collection select
     *
     * @return Mage_Core_Model_Resource_Db_Collection_Abstract
     */
    protected function _initSelect()
    {
        $adapter = $this->_getWriteAdapter();
		$aggregationField = 'created_at';
        $adapter->beginTransaction();
        try {
            $from 	= $this->_dateToUtc($this->_from);
            $to 	= $this->_dateToUtc($this->_to);
            $subSelect = null;
	        $this->_checkDates($from, $to);
	        
            $periodExpr = $adapter->getDatePartSql($this->getStoreTZOffsetQuery(
                array('o' => $this->getTable('sales/order')),
                'o.' . $aggregationField,
                $from, $to
            ));
            
        	if ('month' == $this->_period) {
	            $this->_periodFormat = $adapter->getDateFormatSql($periodExpr, '%Y-%m');
	        } elseif ('year' == $this->_period) {
	            $this->_periodFormat = $adapter->getDateExtractSql($periodExpr, Varien_Db_Adapter_Interface::INTERVAL_YEAR);
	        } else {
	            $this->_periodFormat = $adapter->getDateFormatSql($periodExpr, '%Y-%m-%d');
	        }
	        
            // Columns list
            $columns = array(
                // convert dates from UTC to current admin timezone
                'period'                         => $this->_periodFormat,
                /*'store_id'                       => 'o.store_id',
                'order_status'                   => 'o.status',*/
                'orders_count'                   => new Zend_Db_Expr('COUNT(o.entity_id)'),
                'total_qty_ordered'              => new Zend_Db_Expr('SUM(oi.total_qty_ordered)'),
                'total_qty_invoiced'             => new Zend_Db_Expr('SUM(oi.total_qty_invoiced)'),
                'total_income_amount'            => new Zend_Db_Expr(
                    sprintf('SUM((%s - %s) * %s)',
                        $adapter->getIfNullSql('o.base_grand_total', 0),
                        $adapter->getIfNullSql('o.base_total_canceled',0),
                        $adapter->getIfNullSql('o.base_to_global_rate',0)
                    )
                ),
                'total_revenue_amount'           => new Zend_Db_Expr(
                    sprintf('SUM((%s - %s - %s - (%s - %s - %s)) * %s)',
                        $adapter->getIfNullSql('o.base_total_invoiced', 0),
                        $adapter->getIfNullSql('o.base_tax_invoiced', 0),
                        $adapter->getIfNullSql('o.base_shipping_invoiced', 0),
                        $adapter->getIfNullSql('o.base_total_refunded', 0),
                        $adapter->getIfNullSql('o.base_tax_refunded', 0),
                        $adapter->getIfNullSql('o.base_shipping_refunded', 0),
                        $adapter->getIfNullSql('o.base_to_global_rate', 0)
                    )
                ),
                'total_profit_amount'            => new Zend_Db_Expr(
                    sprintf('SUM((%s - %s - %s - %s - %s) * %s)',
                        $adapter->getIfNullSql('o.base_total_paid', 0),
                        $adapter->getIfNullSql('o.base_total_refunded', 0),
                        $adapter->getIfNullSql('o.base_tax_invoiced', 0),
                        $adapter->getIfNullSql('o.base_shipping_invoiced', 0),
                        $adapter->getIfNullSql('o.base_total_invoiced_cost', 0),
                        $adapter->getIfNullSql('o.base_to_global_rate', 0)
                    )
                ),
                'total_invoiced_amount'          => new Zend_Db_Expr(
                    sprintf('SUM(%s * %s)',
                        $adapter->getIfNullSql('o.base_total_invoiced', 0),
                        $adapter->getIfNullSql('o.base_to_global_rate', 0)
                    )
                ),
                'total_canceled_amount'          => new Zend_Db_Expr(
                    sprintf('SUM(%s * %s)',
                        $adapter->getIfNullSql('o.base_total_canceled', 0),
                        $adapter->getIfNullSql('o.base_to_global_rate', 0)
                    )
                ),
                'total_paid_amount'              => new Zend_Db_Expr(
                    sprintf('SUM(%s * %s)',
                        $adapter->getIfNullSql('o.base_total_paid', 0),
                        $adapter->getIfNullSql('o.base_to_global_rate', 0)
                    )
                ),
                'total_refunded_amount'          => new Zend_Db_Expr(
                    sprintf('SUM(%s * %s)',
                        $adapter->getIfNullSql('o.base_total_refunded', 0),
                        $adapter->getIfNullSql('o.base_to_global_rate', 0)
                    )
                ),
                'total_tax_amount'               => new Zend_Db_Expr(
                    sprintf('SUM((%s - %s) * %s)',
                        $adapter->getIfNullSql('o.base_tax_amount', 0),
                        $adapter->getIfNullSql('o.base_tax_canceled', 0),
                        $adapter->getIfNullSql('o.base_to_global_rate', 0)
                    )
                ),
                'total_tax_amount_actual'        => new Zend_Db_Expr(
                    sprintf('SUM((%s -%s) * %s)',
                        $adapter->getIfNullSql('o.base_tax_invoiced', 0),
                        $adapter->getIfNullSql('o.base_tax_refunded', 0),
                        $adapter->getIfNullSql('o.base_to_global_rate', 0)
                    )
                ),
                'total_shipping_amount'          => new Zend_Db_Expr(
                    sprintf('SUM((%s - %s) * %s)',
                        $adapter->getIfNullSql('o.base_shipping_amount', 0),
                        $adapter->getIfNullSql('o.base_shipping_canceled', 0),
                        $adapter->getIfNullSql('o.base_to_global_rate', 0)
                    )
                ),
                'total_shipping_amount_actual'   => new Zend_Db_Expr(
                    sprintf('SUM((%s - %s) * %s)',
                        $adapter->getIfNullSql('o.base_shipping_invoiced', 0),
                        $adapter->getIfNullSql('o.base_shipping_refunded', 0),
                        $adapter->getIfNullSql('o.base_to_global_rate', 0)
                    )
                ),
                'total_discount_amount'          => new Zend_Db_Expr(
                    sprintf('SUM((ABS(%s) - %s) * %s)',
                        $adapter->getIfNullSql('o.base_discount_amount', 0),
                        $adapter->getIfNullSql('o.base_discount_canceled', 0),
                        $adapter->getIfNullSql('o.base_to_global_rate', 0)
                    )
                ),
                'total_discount_amount_actual'   => new Zend_Db_Expr(
                    sprintf('SUM((%s - %s) * %s)',
                        $adapter->getIfNullSql('o.base_discount_invoiced', 0),
                        $adapter->getIfNullSql('o.base_discount_refunded', 0),
                        $adapter->getIfNullSql('o.base_to_global_rate', 0)
                    )
                )
            );
            $select          = $adapter->select();
            $selectOrderItem = $adapter->select();

            $qtyCanceledExpr = $adapter->getIfNullSql('qty_canceled', 0);
            $cols            = array(
                'order_id'           => 'order_id',
                'total_qty_ordered'  => new Zend_Db_Expr("SUM(qty_ordered - {$qtyCanceledExpr})"),
                'total_qty_invoiced' => new Zend_Db_Expr('SUM(qty_invoiced)'),
            );
            $selectOrderItem->from($this->getTable('sales/order_item'), $cols)
                ->where('parent_item_id IS NULL')
                ->group('order_id');

            $select->from(array('o' => $this->getTable('sales/order')), $columns)
                ->join(array('oi' => $selectOrderItem), 'oi.order_id = o.entity_id', array())
                ->where('o.state NOT IN (?)', array(
                    Mage_Sales_Model_Order::STATE_PENDING_PAYMENT,
                    Mage_Sales_Model_Order::STATE_NEW
                ));
			
			
            if ($subSelect !== null) {
                $select->having($this->_makeConditionFromDateRangeSelect($subSelect, 'period'));
            }
            
            if (!$this->isTotals()) {
	            $select->group(array(
	                $this->_periodFormat,
	            ));
            }
            $this->_select = $select;
        }catch (Exception $e) {
            $adapter->rollBack();
            throw $e;
        }
        return $this;
    }
    
    
	/**
    * Retrieve query for attribute with timezone conversion
    *
    * @param string|array $table
    * @param string $column
    * @param mixed $from
    * @param mixed $to
    * @param int|string|Mage_Core_Model_Store|null $store
    * @return string
    */
    public function getStoreTZOffsetQuery($table, $column, $from = null, $to = null, $store = null)
    {
        $column = $this->_getWriteAdapter()->quoteIdentifier($column);

        if (is_null($from)) {
            $selectOldest = $this->_getWriteAdapter()->select()
                ->from(
                    $table,
                    array("MIN($column)")
                );
            $from = $this->_getWriteAdapter()->fetchOne($selectOldest);
        }

        $periods = $this->_getTZOffsetTransitions(
            Mage::app()->getLocale()->storeDate($store)->toString(Zend_Date::TIMEZONE_NAME), $from, $to
        );
        if (empty($periods)) {
            return $column;
        }

        $query = "";
        $periodsCount = count($periods);

        $i = 0;
        foreach ($periods as $offset => $timestamps) {
            $subParts = array();
            foreach ($timestamps as $ts) {
                $subParts[] = "($column between {$ts['from']} and {$ts['to']})";
            }

            $then = $this->_getWriteAdapter()
                ->getDateAddSql($column, $offset, Varien_Db_Adapter_Interface::INTERVAL_SECOND);

            $query .= (++$i == $periodsCount) ? $then : "CASE WHEN " . join(" OR ", $subParts) . " THEN $then ELSE ";
        }

        return $query . str_repeat('END ', count($periods) - 1);
    }
    
	/**
     * Retrieve transitions for offsets of given timezone
     *
     * @param string $timezone
     * @param mixed $from
     * @param mixed $to
     * @return array
     */
    protected function _getTZOffsetTransitions($timezone, $from = null, $to = null)
    {
        $tzTransitions = array();
        try {
            if (!empty($from)) {
                $from = new Zend_Date($from, Varien_Date::DATETIME_INTERNAL_FORMAT);
                $from = $from->getTimestamp();
            }

            $to = new Zend_Date($to, Varien_Date::DATETIME_INTERNAL_FORMAT);
            $nextPeriod = $this->_getWriteAdapter()->formatDate($to->toString(Varien_Date::DATETIME_INTERNAL_FORMAT));
            $to = $to->getTimestamp();

            $dtz = new DateTimeZone($timezone);
            $transitions = $dtz->getTransitions();
            $dateTimeObject = new Zend_Date('c');
            for ($i = count($transitions) - 1; $i >= 0; $i--) {
                $tr = $transitions[$i];
                if (!$this->_isValidTransition($tr, $to)) {
                    continue;
                }

                $dateTimeObject->set($tr['time']);
                $tr['time'] = $this->_getWriteAdapter()
                    ->formatDate($dateTimeObject->toString(Varien_Date::DATETIME_INTERNAL_FORMAT));
                $tzTransitions[$tr['offset']][] = array('from' => $tr['time'], 'to' => $nextPeriod);

                if (!empty($from) && $tr['ts'] < $from) {
                    break;
                }
                $nextPeriod = $tr['time'];
            }
        } catch (Exception $e) {
            $this->_logException($e);
        }

        return $tzTransitions;
    }
    
    
	/**
     * Verifies the transition and the "to" timestamp
     *
     * @param array      $transition
     * @param int|string $to
     * @return bool
     */
    protected function _isValidTransition($transition, $to)
    {
        $result         = true;
        $timeStamp      = $transition['ts'];
        $transitionYear = date('Y', $timeStamp);

        if ($transitionYear > 10000 || $transitionYear < -10000) {
            $result = false;
        } else if ($timeStamp > $to) {
            $result = false;
        }

        return $result;
    }
    
	/**
     * Apply date range filter
     *
     * @return Mage_Sales_Model_Resource_Report_Collection_Abstract
     */
    protected function _applyDateRangeFilter()
    {
        // Remember that field PERIOD is a DATE(YYYY-MM-DD) in all databases including Oracle
        if ($this->_from !== null) {
            $this->getSelect()->where('created_at >= ?', $this->_from);
        }
        if ($this->_to !== null) {
            $this->getSelect()->where('created_at <= ?', $this->_to);
        }

        return $this;
    }
    
	/**
     * Apply order status filter
     *
     * @return Mage_Sales_Model_Resource_Report_Collection_Abstract
     */
    protected function _applyOrderStatusFilter()
    {
        if (is_null($this->_orderStatus)) {
            return $this;
        }
        $orderStatus = $this->_orderStatus;
        if (!is_array($orderStatus)) {
            $orderStatus = array($orderStatus);
        }
        $this->getSelect()->where('o.status IN(?)', $orderStatus);
        return $this;
    }
    
	/**
     * Retrieve date in UTC timezone
     *
     * @param unknown_type $date
     * @return Zend_Date|null
     */
    protected function _dateToUtc($date)
    {
        if ($date === null) {
            return null;
        }
        $dateUtc = new Zend_Date($date);
        $dateUtc->setTimezone('Etc/UTC');
        return $dateUtc;
    }
    
	/**
     * Check range dates and transforms it to strings
     *
     * @param mixed $from
     * @param mixed $to
     * @return Mage_Reports_Model_Resource_Report_Abstract
     */
    protected function _checkDates(&$from, &$to)
    {
        if ($from !== null) {
            $from = $this->formatDate($from);
        }

        if ($to !== null) {
            $to = $this->formatDate($to);
        }

        return $this;
    }
    
	/**
     * Generate table date range select
     *
     * @param string $table
     * @param string $column
     * @param string $whereColumn
     * @param string|null $from
     * @param string|null $to
     * @param array $additionalWhere
     * @param unknown_type $alias
     * @return Varien_Db_Select
     */
    protected function _getTableDateRangeSelect($table, $column, $whereColumn, $from = null, $to = null,
        $additionalWhere = array(), $alias = 'date_range_table')
    {
        $adapter = $this->_getReadAdapter();
        $select  = $adapter->select()
            ->from(
                array($alias => $table),
                $adapter->getDatePartSql(
                    $this->getStoreTZOffsetQuery(array($alias => $table), $alias . '.' . $column, $from, $to)
                )
            )
            ->distinct(true);

        if ($from !== null) {
           $select->where($alias . '.' . $whereColumn . ' >= ?', $from);
        }

        if ($to !== null) {
           $select->where($alias . '.' . $whereColumn . ' <= ?', $to);
        }

        if (!empty($additionalWhere)) {
            foreach ($additionalWhere as $condition) {
                if (is_array($condition) && count($condition) == 2) {
                   $condition = $adapter->quoteInto($condition[0], $condition[1]);
                } elseif (is_array($condition)) { // Invalid condition
                   continue;
                }
                $condition = str_replace('{{table}}', $adapter->quoteIdentifier($alias), $condition);
                $select->where($condition);
            }
        }

        return $select;
    }
}