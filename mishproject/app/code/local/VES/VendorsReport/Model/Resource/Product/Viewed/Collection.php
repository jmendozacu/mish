<?php
class VES_VendorsReport_Model_Resource_Product_Viewed_Collection extends Mage_Reports_Model_Resource_Report_Product_Viewed_Collection
{
	protected $_vendor_id =0;
	/**
     * Set Vendor Id
     *
     * @param string $vendorId
     * @return Mage_Sales_Model_Resource_Report_Collection_Abstract
     */
    public function setVendorId($vendorId)
    {
        $this->_vendor_id = $vendorId;
        return $this;
    }
	/**
     * Init collection select
     *
     * @return Mage_Reports_Model_Resource_Report_Product_Viewed_Collection
     */
    protected function _initSelect()
    {
    	parent::_initSelect();
    	$select = $this->getSelect();
    	$select->joinLeft(array('product_entity'=>$this->getTable('catalog/product')), 'entity_id=product_id',array('vendor_id'));
        $select->where('vendor_id=?',$this->_vendor_id);
        return $this;
    }
    
	/**
     * Make select object for date boundary
     *
     * @param mixed $from
     * @param mixed $to
     * @return Zend_Db_Select
     */
    protected function _makeBoundarySelect($from, $to)
    {
        $adapter = $this->getConnection();
        $cols    = $this->_getSelectedColumns();
        $cols['views_num'] = 'SUM(views_num)';
        $select  = $adapter->select()
            ->from($this->getResource()->getMainTable(), $cols)
            ->where('period >= ?', $from)
            ->where('period <= ?', $to)
            ->group('product_id')
            ->order('views_num DESC')
            ->limit($this->_ratingLimit);
    	$select->joinLeft(array('product_entity'=>$this->getTable('catalog/product')), 'entity_id=product_id',array('vendor_id'));
        $select->where('vendor_id=?',$this->_vendor_id);
        $this->_applyStoresFilterToSelect($select);

        return $select;
    }
}