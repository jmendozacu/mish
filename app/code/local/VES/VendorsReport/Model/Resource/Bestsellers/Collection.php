<?php
class VES_VendorsReport_Model_Resource_Bestsellers_Collection extends Mage_Sales_Model_Resource_Report_Bestsellers_Collection
{
	protected $_write_adapter;
	
	protected $_grouped_period;
	
	protected $_vendor_id;
	
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
		return $this;
	}
	
	/**
     * Init collection select
     *
     * @return Mage_Core_Model_Resource_Db_Collection_Abstract
     */
    protected function _initSelect()
    {
		$from 	= $this->_dateToUtc($this->_from);
        $to 	= $this->_dateToUtc($this->_to);
        $this->_checkDates($from, $to);
        $adapter = $this->_getWriteAdapter();
        
    	try {
            $subSelect = null;

            // convert dates from UTC to current admin timezone
            $periodExpr = $adapter->getDatePartSql(
                $this->getStoreTZOffsetQuery(
                    array('source_table' => $this->getTable('sales/order')),
                    'source_table.created_at', $from, $to
                )
            );
    		if ('month' == $this->_period) {
	            $this->_periodFormat = $adapter->getDateFormatSql($periodExpr, '%Y-%m');
	        } elseif ('year' == $this->_period) {
	            $this->_periodFormat = $adapter->getDateExtractSql($periodExpr, Varien_Db_Adapter_Interface::INTERVAL_YEAR);
	        } else {
	            $this->_periodFormat = $adapter->getDateFormatSql($periodExpr, '%Y-%m-%d');
	        }
	        
            $helper = Mage::getResourceHelper('core');
            $select = $adapter->select();

            $select->group(array(
                $this->_periodFormat,
                'order_item.product_id'
            ));

            $columns = array(
                'period'                 => $this->_periodFormat,
                'product_id'             => 'order_item.product_id',
            	'store_id'               => 'source_table.store_id',
                'product_name'           => new Zend_Db_Expr(
                    sprintf('MIN(%s)',
                        $adapter->getIfNullSql('product_name.value','product_default_name.value')
                    )
                ),
                'product_price'          => new Zend_Db_Expr(
                        sprintf('%s * %s',
                            $helper->prepareColumn(
                                sprintf('MIN(%s)',
                                    $adapter->getIfNullSql(
                                        $adapter->getIfNullSql('product_price.value','product_default_price.value'),0)
                                ),
                                $select->getPart(Zend_Db_Select::GROUP)
                            ),
                            $helper->prepareColumn(
                                sprintf('MIN(%s)',
                                    $adapter->getIfNullSql('source_table.base_to_global_rate', '0')
                                ),
                                $select->getPart(Zend_Db_Select::GROUP)
                        )
                    )
                ),
                'qty_ordered'            => new Zend_Db_Expr('SUM(order_item.qty_ordered)')
            );

            $select
                ->from(
                    array(
                        'source_table' => $this->getTable('sales/order')),
                    $columns)
                ->joinInner(
                    array(
                        'order_item' => $this->getTable('sales/order_item')),
                    'order_item.order_id = source_table.entity_id',
                    array()
                )
                ->where('source_table.state != ?', Mage_Sales_Model_Order::STATE_CANCELED);


            /** @var Mage_Catalog_Model_Resource_Product $product */
            $product  = Mage::getResourceSingleton('catalog/product');

            $productTypes = array(
                Mage_Catalog_Model_Product_Type::TYPE_GROUPED,
                Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE,
                Mage_Catalog_Model_Product_Type::TYPE_BUNDLE,
            );

            $joinExpr = array(
                'product.entity_id = order_item.product_id',
                $adapter->quoteInto('product.entity_type_id = ?', $product->getTypeId()),
                $adapter->quoteInto('product.type_id NOT IN(?)', $productTypes)
            );
			if($this->_vendor_id){
				$select->where('source_table.vendor_id = ?',$this->_vendor_id);
			}
            $joinExpr = implode(' AND ', $joinExpr);
            $select->joinInner(
                array(
                    'product' => $this->getTable('catalog/product')),
                $joinExpr,
                array()
            );

            // join product attributes Name & Price
            $attr     = $product->getAttribute('name');
            $joinExprProductName       = array(
                'product_name.entity_id = product.entity_id',
                'product_name.store_id = source_table.store_id',
                $adapter->quoteInto('product_name.entity_type_id = ?', $product->getTypeId()),
                $adapter->quoteInto('product_name.attribute_id = ?', $attr->getAttributeId())
            );
            $joinExprProductName        = implode(' AND ', $joinExprProductName);
            $joinExprProductDefaultName = array(
                'product_default_name.entity_id = product.entity_id',
                'product_default_name.store_id = 0',
                $adapter->quoteInto('product_default_name.entity_type_id = ?', $product->getTypeId()),
                $adapter->quoteInto('product_default_name.attribute_id = ?', $attr->getAttributeId())
            );
            $joinExprProductDefaultName = implode(' AND ', $joinExprProductDefaultName);
            $select->joinLeft(
                array(
                    'product_name' => $attr->getBackend()->getTable()),
                $joinExprProductName,
                array()
            )
            ->joinLeft(
                array(
                    'product_default_name' => $attr->getBackend()->getTable()),
                $joinExprProductDefaultName,
                array()
            );
            $attr                    = $product->getAttribute('price');
            $joinExprProductPrice    = array(
                'product_price.entity_id = product.entity_id',
                'product_price.store_id = source_table.store_id',
                $adapter->quoteInto('product_price.entity_type_id = ?', $product->getTypeId()),
                $adapter->quoteInto('product_price.attribute_id = ?', $attr->getAttributeId())
            );
            $joinExprProductPrice    = implode(' AND ', $joinExprProductPrice);

            $joinExprProductDefPrice = array(
                'product_default_price.entity_id = product.entity_id',
                'product_default_price.store_id = 0',
                $adapter->quoteInto('product_default_price.entity_type_id = ?', $product->getTypeId()),
                $adapter->quoteInto('product_default_price.attribute_id = ?', $attr->getAttributeId())
            );
            $joinExprProductDefPrice = implode(' AND ', $joinExprProductDefPrice);
            $select->joinLeft(
                array('product_price' => $attr->getBackend()->getTable()),
                $joinExprProductPrice,
                array()
            )
            ->joinLeft(
                array('product_default_price' => $attr->getBackend()->getTable()),
                $joinExprProductDefPrice,
                array()
            );

            if ($subSelect !== null) {
                $select->having($this->_makeConditionFromDateRangeSelect($subSelect, 'period'));
            }


            $select->useStraightJoin();  // important!
            $this->_select = $select;
        } catch (Exception $e) {
            //$this->_getWriteAdapter()->rollBack();
            throw $e;
        }
        return $this;
    }
    
    protected function _beforeLoad()
    {
    	Mage_Sales_Model_Resource_Report_Collection_Abstract::_beforeLoad();
    $this->_applyStoresFilter();

        if ($this->_period) {
            //
            $selectUnions = array();

            // apply date boundaries (before calling $this->_applyDateRangeFilter())
            $dtFormat   = Varien_Date::DATE_INTERNAL_FORMAT;
            $periodFrom = (!is_null($this->_from) ? new Zend_Date($this->_from, $dtFormat) : null);
            $periodTo   = (!is_null($this->_to)   ? new Zend_Date($this->_to,   $dtFormat) : null);
            /*if ('year' == $this->_period) {

                if ($periodFrom) {
                    // not the first day of the year
                    if ($periodFrom->toValue(Zend_Date::MONTH) != 1 || $periodFrom->toValue(Zend_Date::DAY) != 1) {
                        $dtFrom = $periodFrom->getDate();
                        // last day of the year
                        $dtTo = $periodFrom->getDate()->setMonth(12)->setDay(31);
                        if (!$periodTo || $dtTo->isEarlier($periodTo)) {
                            $selectUnions[] = $this->_makeBoundarySelect(
                                $dtFrom->toString($dtFormat),
                                $dtTo->toString($dtFormat)
                            );

                            // first day of the next year
                            $this->_from = $periodFrom->getDate()
                                ->addYear(1)
                                ->setMonth(1)
                                ->setDay(1)
                                ->toString($dtFormat);
                        }
                    }
                }

                if ($periodTo) {
                    // not the last day of the year
                    if ($periodTo->toValue(Zend_Date::MONTH) != 12 || $periodTo->toValue(Zend_Date::DAY) != 31) {
                        $dtFrom = $periodTo->getDate()->setMonth(1)->setDay(1);  // first day of the year
                        $dtTo = $periodTo->getDate();
                        if (!$periodFrom || $dtFrom->isLater($periodFrom)) {
                            $selectUnions[] = $this->_makeBoundarySelect(
                                $dtFrom->toString($dtFormat),
                                $dtTo->toString($dtFormat)
                            );

                            // last day of the previous year
                            $this->_to = $periodTo->getDate()
                                ->subYear(1)
                                ->setMonth(12)
                                ->setDay(31)
                                ->toString($dtFormat);
                        }
                    }
                }

                if ($periodFrom && $periodTo) {
                    // the same year
                    if ($periodFrom->toValue(Zend_Date::YEAR) == $periodTo->toValue(Zend_Date::YEAR)) {
                        $dtFrom = $periodFrom->getDate();
                        $dtTo = $periodTo->getDate();
                        $selectUnions[] = $this->_makeBoundarySelect(
                            $dtFrom->toString($dtFormat),
                            $dtTo->toString($dtFormat)
                        );

                        $this->getSelect()->where('1<>1');
                    }
                }

            }
            else if ('month' == $this->_period) {
                if ($periodFrom) {
                    // not the first day of the month
                    if ($periodFrom->toValue(Zend_Date::DAY) != 1) {
                        $dtFrom = $periodFrom->getDate();
                        // last day of the month
                        $dtTo = $periodFrom->getDate()->addMonth(1)->setDay(1)->subDay(1);
                        if (!$periodTo || $dtTo->isEarlier($periodTo)) {
                            $selectUnions[] = $this->_makeBoundarySelect(
                                $dtFrom->toString($dtFormat),
                                $dtTo->toString($dtFormat)
                            );

                            // first day of the next month
                            $this->_from = $periodFrom->getDate()->addMonth(1)->setDay(1)->toString($dtFormat);
                        }
                    }
                }

                if ($periodTo) {
                    // not the last day of the month
                    if ($periodTo->toValue(Zend_Date::DAY) != $periodTo->toValue(Zend_Date::MONTH_DAYS)) {
                        $dtFrom = $periodTo->getDate()->setDay(1);  // first day of the month
                        $dtTo = $periodTo->getDate();
                        if (!$periodFrom || $dtFrom->isLater($periodFrom)) {
                            $selectUnions[] = $this->_makeBoundarySelect(
                                $dtFrom->toString($dtFormat),
                                $dtTo->toString($dtFormat)
                            );

                            // last day of the previous month
                            $this->_to = $periodTo->getDate()->setDay(1)->subDay(1)->toString($dtFormat);
                        }
                    }
                }

                if ($periodFrom && $periodTo) {
                    // the same month
                    if ($periodFrom->toValue(Zend_Date::YEAR) == $periodTo->toValue(Zend_Date::YEAR)
                        && $periodFrom->toValue(Zend_Date::MONTH) == $periodTo->toValue(Zend_Date::MONTH)
                    ) {
                        $dtFrom = $periodFrom->getDate();
                        $dtTo = $periodTo->getDate();
                        $selectUnions[] = $this->_makeBoundarySelect(
                            $dtFrom->toString($dtFormat),
                            $dtTo->toString($dtFormat)
                        );

                        $this->getSelect()->where('1<>1');
                    }
                }

            }*/

            $this->_applyDateRangeFilter();

            // add unions to select
            if ($selectUnions) {
                $unionParts = array();
                $cloneSelect = clone $this->getSelect();
                $helper = Mage::getResourceHelper('core');
                $unionParts[] = '(' . $cloneSelect . ')';
                foreach ($selectUnions as $union) {
                    $query = $helper->getQueryUsingAnalyticFunction($union);
                    $unionParts[] = '(' . $query . ')';
                }
                $this->getSelect()->reset()->union($unionParts, Zend_Db_Select::SQL_UNION_ALL);
            }

            if ($this->isTotals()) {
                // calculate total
                $cloneSelect = clone $this->getSelect();
            	if($this->_vendor_id){
					$this->getSelect()->where('source_table.vendor_id = ?',$this->_vendor_id);
				}
                $this->getSelect()->reset()->from($cloneSelect, $this->getAggregatedColumns());
	            
            } else {
                // add sorting
                $this->getSelect()->order(array('period ASC', 'qty_ordered DESC'));
            }
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
            $this->getSelect()->where('source_table.created_at >= ?', $this->_from);
        }
        if ($this->_to !== null) {
            $this->getSelect()->where('source_table.created_at <= ?', $this->_to);
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
     * Apply stores filter to select object
     *
     * @param Zend_Db_Select $select
     * @return Mage_Sales_Model_Resource_Report_Collection_Abstract
     */
    protected function _applyStoresFilterToSelect(Zend_Db_Select $select)
    {
        $nullCheck = false;
        $storeIds  = $this->_storesIds;

        if (!is_array($storeIds)) {
            $storeIds = array($storeIds);
        }

        $storeIds = array_unique($storeIds);

        if ($index = array_search(null, $storeIds)) {
            unset($storeIds[$index]);
            $nullCheck = true;
        }

        $storeIds[0] = ($storeIds[0] == '') ? 0 : $storeIds[0];

        if ($nullCheck) {
            $select->where('source_table.store_id IN(?) OR store_id IS NULL', $storeIds);
        } else {
            $select->where('source_table.store_id IN(?)', $storeIds);
        }

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
}